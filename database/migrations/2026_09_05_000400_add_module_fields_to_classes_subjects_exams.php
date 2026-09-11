<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModuleFieldsToClassesSubjectsExams extends Migration
{
    public function up()
    {
        Schema::table('my_classes', function (Blueprint $table) {
            $table->string('code', 30)->nullable()->after('name');
            $table->unsignedInteger('teacher_id')->nullable()->after('class_type_id');
            $table->string('status', 20)->default('active')->after('teacher_id');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->string('code', 30)->nullable()->after('name');
            $table->text('description')->nullable()->after('slug');
            $table->string('status', 20)->default('active')->after('description');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->string('type', 30)->default('term')->after('name');
            $table->date('start_date')->nullable()->after('type');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('status', 20)->default('pending')->after('end_date');
        });

        Schema::table('my_classes', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('my_classes', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['code', 'teacher_id', 'status']);
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['code', 'description', 'status']);
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['type', 'start_date', 'end_date', 'status']);
        });
    }
}