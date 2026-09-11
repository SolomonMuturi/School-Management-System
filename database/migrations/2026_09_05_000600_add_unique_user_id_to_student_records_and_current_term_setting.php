<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueUserIdToStudentRecordsAndCurrentTermSetting extends Migration
{
    public function up()
    {
        // A student account must map to exactly one student record.
        Schema::table('student_records', function (Blueprint $table) {
            $table->unique('user_id');
        });

        // Current term tracked centrally (System Settings = single source of truth).
        DB::table('settings')->insertOrIgnore([
            ['type' => 'current_term', 'description' => '1', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::table('student_records', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });

        DB::table('settings')->where('type', 'current_term')->delete();
    }
}