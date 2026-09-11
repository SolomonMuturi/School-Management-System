<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAcademicManagementTables extends Migration
{
    public function up()
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->tinyInteger('is_current')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('abbr')->nullable();
            $table->integer('sequence')->default(1);
            $table->boolean('is_current')->default(0);
            $table->timestamps();
        });

        Schema::create('academic_year_term', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('academic_term_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('curriculums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('program')->nullable();
            $table->unsignedInteger('academic_year_id')->nullable();
            $table->unsignedInteger('academic_term_id')->nullable();
            $table->string('status')->default('active');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('curriculum_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedInteger('my_class_id')->nullable();
            $table->timestamps();
        });

        Schema::create('curriculum_topics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('my_class_id')->nullable();
            $table->string('topic');
            $table->text('learning_objectives')->nullable();
            $table->string('term')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('subject_id');
            $table->unsignedInteger('my_class_id')->nullable();
            $table->unsignedInteger('section_id')->nullable();
            $table->unsignedInteger('academic_year_id')->nullable();
            $table->unsignedInteger('academic_term_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['teacher_id', 'subject_id', 'academic_year_id'], 'teacher_subject_year_unique');
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('subject_id');
            $table->unsignedInteger('my_class_id');
            $table->unsignedInteger('section_id')->nullable();
            $table->date('lesson_date');
            $table->string('topic');
            $table->text('plan')->nullable();
            $table->text('learning_objectives')->nullable();
            $table->text('teaching_notes')->nullable();
            $table->unsignedInteger('academic_year_id')->nullable();
            $table->unsignedInteger('academic_term_id')->nullable();
            $table->timestamps();
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('subject_id');
            $table->unsignedInteger('my_class_id');
            $table->unsignedInteger('section_id')->nullable();
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->date('due_date')->nullable();
            $table->string('attachment_path')->nullable();
            $table->integer('max_marks')->default(100);
            $table->unsignedInteger('academic_year_id')->nullable();
            $table->unsignedInteger('academic_term_id')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedInteger('student_id');
            $table->string('submission_text')->nullable();
            $table->string('attachment_path')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->dateTime('submitted_at')->nullable();
            $table->unsignedInteger('marks')->nullable();
            $table->string('feedback')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id', 'student_id']);
        });

        $session = DB::table('settings')->where('type', 'current_session')->value('description')
            ?: (date('Y').'-'.(date('Y') + 1));

        DB::table('academic_years')->insertOrIgnore([
            'name' => $session,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+1 year')),
            'is_current' => 1,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('academic_terms')->insert([
            ['name' => 'First Term', 'abbr' => '1st', 'sequence' => 1, 'is_current' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Second Term', 'abbr' => '2nd', 'sequence' => 2, 'is_current' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Third Term', 'abbr' => '3rd', 'sequence' => 3, 'is_current' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('settings')->insertOrIgnore([
            ['type' => 'academic_settings', 'description' => 'enable_ranking=1;enable_teacher_comments=1;report_card_position=1', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('teacher_assignments');
        Schema::dropIfExists('curriculum_topics');
        Schema::dropIfExists('curriculum_subject');
        Schema::dropIfExists('curriculums');
        Schema::dropIfExists('academic_year_term');
        Schema::dropIfExists('academic_terms');
        Schema::dropIfExists('academic_years');

        DB::table('settings')->where('type', 'academic_settings')->delete();
    }
}