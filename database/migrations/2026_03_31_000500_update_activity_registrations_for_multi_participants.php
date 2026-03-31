<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->hasIndex('activity_registrations', 'activity_registrations_activity_id_idx')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->index('activity_id', 'activity_registrations_activity_id_idx');
            });
        }

        if (! $this->hasIndex('activity_registrations', 'activity_registrations_user_id_idx')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->index('user_id', 'activity_registrations_user_id_idx');
            });
        }

        if (! Schema::hasColumn('activity_registrations', 'participant_age')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->unsignedTinyInteger('participant_age')->nullable()->after('participant_phone');
            });
        }

        if (! Schema::hasColumn('activity_registrations', 'participant_gender')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->enum('participant_gender', ['L', 'P'])->nullable()->after('participant_age');
            });
        }

        if ($this->hasIndex('activity_registrations', 'activity_registrations_activity_id_user_id_unique')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->dropUnique('activity_registrations_activity_id_user_id_unique');
            });
        }
    }

    public function down(): void
    {
        if (! $this->hasIndex('activity_registrations', 'activity_registrations_activity_id_user_id_unique')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->unique(['activity_id', 'user_id']);
            });
        }

        if ($this->hasIndex('activity_registrations', 'activity_registrations_activity_id_idx')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->dropIndex('activity_registrations_activity_id_idx');
            });
        }

        if ($this->hasIndex('activity_registrations', 'activity_registrations_user_id_idx')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->dropIndex('activity_registrations_user_id_idx');
            });
        }

        if (Schema::hasColumn('activity_registrations', 'participant_gender')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->dropColumn('participant_gender');
            });
        }

        if (Schema::hasColumn('activity_registrations', 'participant_age')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->dropColumn('participant_age');
            });
        }
    }

    private function hasIndex(string $tableName, string $indexName): bool
    {
        $databaseName = DB::getDatabaseName();
        $row = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$databaseName, $tableName, $indexName]
        );

        return (int) ($row->aggregate ?? 0) > 0;
    }
};
