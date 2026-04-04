<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use App\Support\ClientIpResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdminCanCreateUsers();

        $actor = $request->user();
        $isSuperadmin = $actor?->hasRole('superadmin') ?? false;
        $isOwnerReadOnly = $request->user()?->hasRole('owner') ?? false;
        $canCreateUser = $request->user()?->hasPermission('data_user.create') ?? false;
        $canViewDeleted = $isSuperadmin;
        $tab = (string) $request->query('tab', 'active');
        if (! in_array($tab, ['active', 'deleted'], true)) {
            $tab = 'active';
        }
        if ($tab === 'deleted' && ! $canViewDeleted) {
            $tab = 'active';
        }

        $perPage = (int) $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
        $q = trim((string) $request->query('q', ''));

        $users = User::with('roles')
            ->when(! $isSuperadmin, function ($query): void {
                $query->whereDoesntHave('roles', function ($roleQuery): void {
                    $roleQuery->where('slug', 'superadmin');
                });
            })
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%')
                        ->orWhere('username', 'like', '%' . $q . '%')
                        ->orWhere('phone', 'like', '%' . $q . '%');
                });
            })
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());

        $deletedUsers = $canViewDeleted
            ? User::onlyTrashed()
                ->with('roles')
                ->when($q !== '', function ($query) use ($q): void {
                    $query->where(function ($inner) use ($q): void {
                        $inner->where('name', 'like', '%' . $q . '%')
                            ->orWhere('email', 'like', '%' . $q . '%')
                            ->orWhere('username', 'like', '%' . $q . '%')
                            ->orWhere('phone', 'like', '%' . $q . '%');
                    });
                })
                ->latest('deleted_at')
                ->paginate($perPage, ['*'], 'deleted_page')
                ->appends($request->query())
            : null;

        return view('admin.users', [
            'users' => $users,
            'deletedUsers' => $deletedUsers,
            'roles' => Role::orderBy('name')->get(),
            'perPage' => $perPage,
            'q' => $q,
            'tab' => $tab,
            'isOwnerReadOnly' => $isOwnerReadOnly,
            'canCreateUser' => $canCreateUser,
            'canViewDeleted' => $canViewDeleted,
        ]);
    }

    public function access()
    {
        $matrix = $this->permissionMatrix();
        $allPermissionSlugs = collect($matrix)
            ->flatMap(fn (array $item) => collect($item['actions'])
                ->map(fn (string $action) => $item['key'] . '.' . $action))
            ->values()
            ->all();

        $this->ensurePermissionCatalog($matrix);
        $this->ensureSuperadminHasAllPermissions($allPermissionSlugs);
        $this->ensureDefaultRolePermissions($allPermissionSlugs);

        $roles = Role::withCount('users')->orderBy('name')->get();
        $selectedRoleId = (int) request()->integer('role_id');
        $selectedRole = $roles->firstWhere('id', $selectedRoleId) ?? $roles->firstWhere('slug', 'admin') ?? $roles->first();
        $selectedPermissionSlugs = [];
        $isSuperadminRole = false;

        if ($selectedRole) {
            $selectedPermissionSlugs = $selectedRole->permissions()
                ->pluck('permissions.slug')
                ->all();
            $isSuperadminRole = $selectedRole->slug === 'superadmin';
        }

        return view('admin.users-access', [
            'roles' => $roles,
            'matrix' => $matrix,
            'selectedRole' => $selectedRole,
            'selectedPermissionSlugs' => $selectedPermissionSlugs,
            'isSuperadminRole' => $isSuperadminRole,
        ]);
    }

    public function updateAccess(Request $request, AuditLogService $auditLogService)
    {
        $matrix = $this->permissionMatrix();
        $this->ensurePermissionCatalog($matrix);
        $allowedSlugs = collect($matrix)
            ->flatMap(fn (array $item) => collect($item['actions'])
                ->map(fn (string $action) => $item['key'] . '.' . $action))
            ->values()
            ->all();

        $data = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($allowedSlugs)],
        ]);

        $role = Role::findOrFail($data['role_id']);
        if ($role->slug === 'superadmin') {
            return back()->withErrors(['role_id' => 'Hak akses Superadmin bersifat penuh dan tidak dapat diubah.']);
        }

        $selectedSlugs = collect($data['permissions'] ?? [])
            ->unique()
            ->values()
            ->all();

        $permissionIds = Permission::whereIn('slug', $selectedSlugs)->pluck('id')->all();
        $role->permissions()->sync($permissionIds);

        $auditLogService->record(
            $request,
            'update_role_permissions',
            'Update hak akses role ' . $role->slug . ' (' . count($permissionIds) . ' permission)',
            'roles',
            $role->id
        );

        return redirect()
            ->route('admin.users.access', ['role_id' => $role->id])
            ->with('status', 'Hak akses role berhasil diperbarui.');
    }

    public function updateRole(Request $request, User $user, AuditLogService $auditLogService)
    {
        $this->abortIfTargetIsSuperadminForNonSuperadmin($request, $user);

        return $this->update($request, $user, $auditLogService);
    }

    public function store(Request $request, AuditLogService $auditLogService)
    {
        $this->ensureAdminCanCreateUsers();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:32'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        $username = $data['username'] ?? null;
        if (! $username) {
            $username = $this->generateUniqueUsername($data['email']);
        }

        $user = User::create([
            'name' => $data['name'],
            'username' => $username,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => (bool) $data['is_active'],
            'email_verified_at' => (bool) $data['is_active'] ? now() : null,
            'activated_at' => (bool) $data['is_active'] ? now() : null,
            'registration_ip' => ClientIpResolver::resolve($request),
        ]);

        $user->roles()->sync([$data['role_id']]);

        $role = Role::find($data['role_id']);
        $auditLogService->record(
            $request,
            'create_user',
            'Tambah user ' . $user->email . ' (role: ' . ($role?->slug ?? '-') . ', status: ' . ($user->is_active ? 'aktif' : 'nonaktif') . ')',
            'users',
            $user->id
        );

        return back()->with('status', 'User baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $user, AuditLogService $auditLogService)
    {
        $this->abortIfTargetIsSuperadminForNonSuperadmin($request, $user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:32'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'is_active' => (bool) $data['is_active'],
            'activated_at' => (bool) $data['is_active'] ? ($user->activated_at ?? now()) : null,
        ]);

        $user->roles()->sync([$data['role_id']]);

        $role = Role::find($data['role_id']);
        $auditLogService->record(
            $request,
            'update_user',
            'Update user ' . $user->email . ' (role: ' . ($role?->slug ?? '-') . ', status: ' . ($user->is_active ? 'aktif' : 'nonaktif') . ')',
            'users',
            $user->id
        );

        return back()->with('status', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user, AuditLogService $auditLogService)
    {
        $this->abortIfTargetIsSuperadminForNonSuperadmin($request, $user);

        if ((int) $request->user()->id === (int) $user->id) {
            return back()->withErrors(['user' => 'Akun aktif yang sedang digunakan tidak bisa dihapus.']);
        }

        $email = $user->email;
        $user->delete();

        $auditLogService->record($request, 'delete_user', 'Hapus pengguna: ' . $email, 'users', $user->id);

        return back()->with('status', 'Pengguna berhasil dihapus (soft delete).');
    }

    public function restore(Request $request, int $userId, AuditLogService $auditLogService)
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $user->restore();

        $auditLogService->record($request, 'restore_user', 'Restore pengguna: ' . $user->email, 'users', $user->id);

        return back()->with('status', 'Pengguna berhasil direstore.');
    }

    public function forceDelete(Request $request, int $userId, AuditLogService $auditLogService)
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        if ($user->hasRole('superadmin')) {
            return back()->withErrors(['user' => 'User superadmin tidak dapat dihapus permanen.']);
        }

        $email = $user->email;
        $id = $user->id;
        $user->roles()->detach();
        $user->forceDelete();

        $auditLogService->record($request, 'hard_delete_user', 'Hapus permanen pengguna: ' . $email, 'users', $id);

        return back()->with('status', 'Pengguna berhasil dihapus permanen.');
    }

    private function generateUniqueUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '-');
        $base = $base !== '' ? $base : 'user';
        $candidate = $base;
        $counter = 1;

        while (User::where('username', $candidate)->exists()) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    private function permissionMatrix(): array
    {
        return [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'data_user', 'label' => 'Data User', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'hak_akses', 'label' => 'Hak Akses', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'pengurus', 'label' => 'Pengurus', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'kegiatan', 'label' => 'Kegiatan', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'pendaftaran_kegiatan', 'label' => 'Pendaftaran Kegiatan', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'check_in', 'label' => 'Check In', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'donasi', 'label' => 'Donasi', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'verifikasi_donasi', 'label' => 'Verifikasi Donasi', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'laporan', 'label' => 'Laporan', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'notifikasi', 'label' => 'Notifikasi', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'pengaturan_website', 'label' => 'Pengaturan Website', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'backup_restore', 'label' => 'Backup Restore', 'actions' => ['view', 'create', 'edit', 'delete']],
            ['key' => 'log_sistem', 'label' => 'Log Sistem', 'actions' => ['view', 'create', 'edit', 'delete']],
        ];
    }

    private function ensurePermissionCatalog(array $matrix): void
    {
        foreach ($matrix as $item) {
            foreach ($item['actions'] as $action) {
                Permission::firstOrCreate(
                    ['slug' => $item['key'] . '.' . $action],
                    ['name' => $item['label'] . ' - ' . strtoupper($action)]
                );
            }
        }
    }

    private function ensureSuperadminHasAllPermissions(array $permissionSlugs): void
    {
        $superadminRole = Role::where('slug', 'superadmin')->first();
        if (! $superadminRole) {
            return;
        }

        $permissionIds = Permission::whereIn('slug', $permissionSlugs)->pluck('id')->all();
        if ($permissionIds !== []) {
            $superadminRole->permissions()->sync($permissionIds);
        }
    }

    private function ensureDefaultRolePermissions(array $allPermissionSlugs): void
    {
        $defaultMap = [
            'admin' => [
                'dashboard.view',
                'data_user.view', 'data_user.create', 'data_user.edit', 'data_user.delete',
                'pengurus.view',
                'kegiatan.view', 'kegiatan.create', 'kegiatan.edit', 'kegiatan.delete',
                'pendaftaran_kegiatan.view',
                'check_in.view', 'check_in.create',
                'donasi.view', 'donasi.create', 'donasi.edit',
                'verifikasi_donasi.view', 'verifikasi_donasi.edit',
                'laporan.view',
                'notifikasi.view',
                'log_sistem.view',
            ],
            'owner' => [
                'dashboard.view',
                'pengurus.view',
                'kegiatan.view',
                'donasi.view',
                'laporan.view',
            ],
            'petugas' => [
                'dashboard.view',
                'pendaftaran_kegiatan.view',
                'check_in.view', 'check_in.create',
            ],
            'umat' => [
                'dashboard.view',
                'kegiatan.view',
                'donasi.view', 'donasi.create',
            ],
        ];

        foreach ($defaultMap as $roleSlug => $allowedSlugs) {
            $role = Role::where('slug', $roleSlug)->first();
            if (! $role) {
                continue;
            }

            $currentCount = $role->permissions()->count();
            if ($currentCount > 0) {
                continue;
            }

            $effectiveSlugs = collect($allowedSlugs)->intersect($allPermissionSlugs)->values()->all();
            $permissionIds = Permission::whereIn('slug', $effectiveSlugs)->pluck('id')->all();
            $role->permissions()->sync($permissionIds);
        }
    }

    private function ensureAdminCanCreateUsers(): void
    {
        $permission = Permission::firstOrCreate(
            ['slug' => 'data_user.create'],
            ['name' => 'Data User - CREATE']
        );

        $adminRole = Role::where('slug', 'admin')->first();
        if (! $adminRole) {
            return;
        }

        $adminRole->permissions()->syncWithoutDetaching([$permission->id]);
    }

    private function abortIfTargetIsSuperadminForNonSuperadmin(Request $request, User $target): void
    {
        $actor = $request->user();
        if (! $actor) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        if ($actor->hasRole('superadmin')) {
            return;
        }

        if ($target->hasRole('superadmin')) {
            abort(403, 'Akun superadmin hanya dapat diakses oleh superadmin.');
        }
    }
}
