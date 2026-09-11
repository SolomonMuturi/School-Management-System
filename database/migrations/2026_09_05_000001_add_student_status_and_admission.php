<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentStatusAndAdmission extends Migration
{
    public function up()
    {
        Schema::table('student_records', function (Blueprint $table) {
            if (!Schema::hasColumn('student_records', 'status')) {
                $table->string('status')->default('active');
            }
            if (!Schema::hasColumn('student_records', 'admission_date')) {
                $table->date('admission_date')->nullable();
            }
            if (!Schema::hasColumn('student_records', 'previous_school')) {
                $table->string('previous_school')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('student_records', function (Blueprint $table) {
            $table->dropColumn(['status', 'admission_date', 'previous_school']);
        });
    }
}