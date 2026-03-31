<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('activity_registrations', 'participant_address')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->string('participant_address', 255)->nullable()->after('participant_gender');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('activity_registrations', 'participant_address')) {
            Schema::table('activity_registrations', function (Blueprint $table) {
                $table->dropColumn('participant_address');
            });
        }
    }
};
