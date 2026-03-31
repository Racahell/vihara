<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BackupRestoreController extends Controller
{
    public function index(Request $request)
    {
        $defaultTab = $request->routeIs('admin.restore-data.index') ? 'restore' : 'backup';
        $tab = $request->query('tab', $defaultTab);
        if (!in_array($tab, ['backup', 'restore', 'clear'], true)) {
            $tab = 'backup';
        }

        $tables = $this->backupTables();

        return view('admin.backup-restore', [
            'tab' => $tab,
            'tableCount' => count($tables),
            'tables' => $tables,
        ]);
    }

    public function backup(Request $request)
    {
        $allTables = $this->backupTables();
        $selectedTable = $request->validate([
            'selected_table' => ['nullable', 'string', 'in:' . implode(',', $allTables)],
        ])['selected_table'] ?? null;

        $tables = $selectedTable ? [$selectedTable] : $allTables;
        $sql = [];

        $sql[] = '-- vihara-sql-backup-v1';
        $sql[] = '-- app: ' . config('app.name');
        $sql[] = '-- created_at: ' . now()->toIso8601String();
        $sql[] = '-- database: ' . (config('database.connections.' . config('database.default') . '.database') ?? '-');
        $sql[] = '';
        $sql[] = 'SET FOREIGN_KEY_CHECKS=0;';
        $sql[] = '';

        foreach ($tables as $table) {
            $columns = Schema::getColumnListing($table);
            $rows = DB::table($table)->get($columns)->map(fn ($row) => (array) $row)->all();

            $sql[] = '-- Table: ' . $table;
            $sql[] = 'DELETE FROM `' . $table . '`;';

            if ($rows !== []) {
                $quotedColumns = implode(', ', array_map(fn ($col) => '`' . $col . '`', $columns));
                $valueLines = [];
                foreach ($rows as $row) {
                    $encoded = array_map(fn ($value) => $this->encodeSqlValue($value), $row);
                    $valueLines[] = '(' . implode(', ', $encoded) . ')';
                }
                $sql[] = 'INSERT INTO `' . $table . '` (' . $quotedColumns . ') VALUES';
                $sql[] = implode(",\n", $valueLines) . ';';
            }

            $sql[] = '';
        }

        $sql[] = 'SET FOREIGN_KEY_CHECKS=1;';
        $content = implode("\n", $sql);
        $filename = $selectedTable
            ? 'vihara-backup-' . $selectedTable . '-' . now()->format('Ymd-His') . '.sql'
            : 'vihara-backup-' . now()->format('Ymd-His') . '.sql';

        return response()->streamDownload(function () use ($content): void {
            echo $content;
        }, $filename, ['Content-Type' => 'application/sql']);
    }

    public function restore(Request $request)
    {
        $allTables = $this->backupTables();
        $validated = $request->validate([
            'backup_file' => ['required', 'file', 'mimes:sql,txt', 'max:102400'],
            'selected_table' => ['nullable', 'string', 'in:' . implode(',', $allTables)],
        ]);

        $content = file_get_contents($validated['backup_file']->getRealPath());
        $statements = $this->splitSqlStatements($content);
        if ($statements === []) {
            return back()->withErrors(['backup_file' => 'File SQL backup kosong atau tidak valid.']);
        }
        $selectedTable = $validated['selected_table'] ?? null;
        if ($selectedTable) {
            $statements = $this->filterStatementsByTable($statements, $selectedTable);
            if ($statements === []) {
                return back()->withErrors(['backup_file' => 'Tidak ada statement untuk tabel yang dipilih pada file SQL.']);
            }
        }

        try {
            DB::beginTransaction();

            foreach ($statements as $statement) {
                DB::unprepared($statement);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['backup_file' => 'Restore gagal: ' . $e->getMessage()]);
        }

        return redirect()
            ->route('admin.backup-restore.index', ['tab' => 'restore'])
            ->with('status', 'Restore data berhasil diproses.');
    }

    public function clearData()
    {
        if (!Schema::hasTable('users') || !Schema::hasTable('roles') || !Schema::hasTable('role_user')) {
            return back()->withErrors(['clear_data' => 'Tabel user/role tidak lengkap. Proses dibatalkan.']);
        }

        $superAdminUserIds = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.slug', 'superadmin')
            ->distinct()
            ->pluck('users.id')
            ->all();

        if ($superAdminUserIds === []) {
            return back()->withErrors(['clear_data' => 'Tidak ada user dengan role superadmin. Proses dibatalkan.']);
        }

        $driver = DB::getDriverName();
        $tables = collect($this->clearableTables())
            ->reject(fn ($table) => in_array($table, ['roles', 'permissions', 'permission_role', 'website_settings'], true))
            ->values()
            ->all();

        try {
            $this->disableForeignKeyChecks($driver);
            DB::beginTransaction();

            foreach ($tables as $table) {
                if ($table === 'users') {
                    DB::table('users')->whereNotIn('id', $superAdminUserIds)->delete();
                    continue;
                }

                if ($table === 'role_user') {
                    DB::table('role_user')->whereNotIn('user_id', $superAdminUserIds)->delete();
                    continue;
                }

                DB::table($table)->delete();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('clear_data_failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['clear_data' => 'Gagal menghapus data: ' . $e->getMessage()]);
        } finally {
            $this->enableForeignKeyChecks($driver);
        }

        return redirect()
            ->route('admin.backup-restore.index', ['tab' => 'clear'])
            ->with('status', 'Semua data operasional berhasil dihapus. Akun superadmin tetap dipertahankan.');
    }

    public function clearTable(Request $request)
    {
        $allTables = $this->backupTables();
        $validated = $request->validate([
            'selected_table' => ['required', 'string', 'in:' . implode(',', $allTables)],
        ]);
        $table = $validated['selected_table'];

        if (in_array($table, $this->protectedTables(), true)) {
            return back()->withErrors(['clear_table' => 'Tabel ini dilindungi dan tidak bisa dihapus dari menu ini.']);
        }

        $superAdminUserIds = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.slug', 'superadmin')
            ->distinct()
            ->pluck('users.id')
            ->all();

        try {
            if ($table === 'users') {
                DB::table('users')->whereNotIn('id', $superAdminUserIds)->delete();
            } elseif ($table === 'role_user') {
                DB::table('role_user')->whereNotIn('user_id', $superAdminUserIds)->delete();
            } else {
                DB::table($table)->delete();
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['clear_table' => 'Gagal menghapus tabel: ' . $e->getMessage()]);
        }

        return redirect()
            ->route('admin.backup-restore.index', ['tab' => 'clear'])
            ->with('status', 'Data tabel `' . $table . '` berhasil dihapus.');
    }

    private function backupTables(): array
    {
        $databaseName = config('database.connections.' . config('database.default') . '.database');
        $driver = DB::getDriverName();

        if ($driver === 'mysql' && $databaseName) {
            $tables = DB::table('information_schema.tables')
                ->where('table_schema', $databaseName)
                ->orderBy('table_name')
                ->pluck('table_name')
                ->all();

            return array_values(array_map('strval', $tables));
        }

        return collect(Schema::getTableListing())
            ->map(function ($table) use ($databaseName) {
                $table = (string) $table;
                if ($databaseName && str_starts_with($table, $databaseName . '.')) {
                    return substr($table, strlen($databaseName) + 1);
                }
                return $table;
            })
            ->filter(fn ($table) => $table !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function protectedTables(): array
    {
        return [
            'roles',
            'permissions',
            'permission_role',
            'website_settings',
        ];
    }

    private function clearableTables(): array
    {
        $excluded = [
            'migrations',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'sessions',
            'password_reset_tokens',
            'personal_access_tokens',
        ];

        return array_values(array_filter(
            $this->backupTables(),
            fn ($table) => !in_array($table, $excluded, true)
        ));
    }

    private function encodeSqlValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return DB::getPdo()->quote((string) $value);
    }

    private function splitSqlStatements(string $sql): array
    {
        $sql = collect(preg_split("/\r\n|\n|\r/", $sql) ?: [])
            ->reject(function (string $line): bool {
                $trimmed = ltrim($line);
                return str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#');
            })
            ->implode("\n");

        $statements = [];
        $buffer = '';
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $prev = $i > 0 ? $sql[$i - 1] : '';

            if ($char === "'" && !$inDoubleQuote && $prev !== '\\') {
                $inSingleQuote = !$inSingleQuote;
            } elseif ($char === '"' && !$inSingleQuote && $prev !== '\\') {
                $inDoubleQuote = !$inDoubleQuote;
            }

            if ($char === ';' && !$inSingleQuote && !$inDoubleQuote) {
                $statement = trim($buffer);
                $buffer = '';

                if ($statement !== '') {
                    $statements[] = $statement;
                }
                continue;
            }

            $buffer .= $char;
        }

        $rest = trim($buffer);
        if ($rest !== '') {
            $statements[] = $rest;
        }

        return $statements;
    }

    private function filterStatementsByTable(array $statements, string $table): array
    {
        $pattern = '/^(DELETE\\s+FROM|INSERT\\s+INTO|REPLACE\\s+INTO|TRUNCATE\\s+TABLE)\\s+`?'
            . preg_quote($table, '/')
            . '`?\\b/i';

        return array_values(array_filter($statements, fn ($statement) => preg_match($pattern, trim($statement)) === 1));
    }

    private function disableForeignKeyChecks(string $driver): void
    {
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }
    }

    private function enableForeignKeyChecks(string $driver): void
    {
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }
}
