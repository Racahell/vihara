<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $isOwnerReadOnly = request()->user()?->hasRole('owner') ?? false;
        $perPage = (int) request()->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
        $q = trim((string) request()->query('q', ''));

        $users = User::with('roles')
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
            ->appends(request()->query());

        return view('admin.users', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
            'perPage' => $perPage,
            'q' => $q,
            'isOwnerReadOnly' => $isOwnerReadOnly,
        ]);
    }

    public function updateRole(Request $request, User $user, AuditLogService $auditLogService)
    {
        return $this->update($request, $user, $auditLogService);
    }

    public function update(Request $request, User $user, AuditLogService $auditLogService)
    {
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
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->withErrors(['user' => 'Akun aktif yang sedang digunakan tidak bisa dihapus.']);
        }

        $email = $user->email;
        $user->delete();

        $auditLogService->record($request, 'delete_user', 'Hapus pengguna: ' . $email, 'users', $user->id);

        return back()->with('status', 'Pengguna berhasil dihapus (soft delete).');
    }
}
