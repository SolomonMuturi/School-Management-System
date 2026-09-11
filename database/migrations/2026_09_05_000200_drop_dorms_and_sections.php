<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropDormsAndSections extends Migration
{
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        Schema::table('marks', function (Blueprint $t) {
            $t->dropForeign('marks_section_id_foreign');
        });

        Schema::table('exam_records', function (Blueprint $t) {
            $t->dropForeign('exam_records_section_id_foreign');
        });

        Schema::table('student_records', function (Blueprint $t) {
            $t->dropForeign('student_records_section_id_foreign');
            $t->dropForeign('student_records_dorm_id_foreign');
        });

        Schema::table('promotions', function (Blueprint $t) {
            $t->dropForeign('promotions_from_section_foreign');
            $t->dropForeign('promotions_to_section_foreign');
        });

        Schema::table('student_records', function (Blueprint $t) {
            $t->dropColumn(['section_id', 'dorm_id', 'dorm_room_no']);
        });

        Schema::table('marks', function (Blueprint $t) {
            $t->dropColumn('section_id');
        });

        Schema::table('exam_records', function (Blueprint $t) {
            $t->dropColumn('section_id');
        });

        Schema::table('promotions', function (Blueprint $t) {
            $t->dropColumn(['from_section', 'to_section']);
        });

        Schema::table('student_attendance', function (Blueprint $t) {
            $t->dropColumn('section_id');
        });

        Schema::table('teacher_assignments', function (Blueprint $t) {
            $t->dropColumn('section_id');
        });

        Schema::table('lessons', function (Blueprint $t) {
            $t->dropColumn('section_id');
        });

        Schema::table('assignments', function (Blueprint $t) {
            $t->dropColumn('section_id');
        });

        Schema::dropIfExists('sections');
        Schema::dropIfExists('dorms');
        Schema::dropIfExists('dormitories');

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $fields = DB::table('settings')->where('type', 'student_required_fields')->value('description');
        if ($fields) {
            $clean = implode(',', array_filter(array_map('trim', explode(',', $fields)), function ($f) {
                return $f !== 'section_id';
            }));
            DB::table('settings')->where('type', 'student_required_fields')->update(['description' => $clean, 'updated_at' => now()]);
        }
    }

    public function down()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('my_class_id');
            $table->string('name');
            $table->tinyInteger('active')->default(0);
            $table->unsignedInteger('teacher_id')->nullable();
            $table->timestamps();
        });

        Schema::create('dorms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::table('student_records', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable();
            $table->unsignedInteger('dorm_id')->nullable();
            $table->string('dorm_room_no')->nullable();
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('dorm_id')->references('id')->on('dorms')->onDelete('set null');
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable();
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        });

        Schema::table('exam_records', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable();
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->unsignedInteger('from_section')->nullable();
            $table->unsignedInteger('to_section')->nullable();
            $table->foreign('from_section')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('to_section')->references('id')->on('sections')->onDelete('cascade');
        });

        Schema::table('student_attendance', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable();
        });

        Schema::table('teacher_assignments', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable();
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable();
        });
    }
}