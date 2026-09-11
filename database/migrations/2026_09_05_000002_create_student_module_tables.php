<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStudentModuleTables extends Migration
{
    public function up()
    {
        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('user_id');
            $table->string('relationship')->default('Guardian');
            $table->tinyInteger('is_primary')->default(0);
            $table->timestamps();
        });

        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('class_id');
            $table->unsignedInteger('section_id')->nullable();
            $table->date('date');
            $table->string('status')->default('present');
            $table->string('note')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'date']);
        });

        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->string('title');
            $table->string('doc_type')->nullable();
            $table->string('file_path');
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('student_discipline', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->date('date');
            $table->string('incident_type');
            $table->text('description');
            $table->string('action_taken')->nullable();
            $table->string('warning_level')->default('Minor');
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('student_health', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id')->unique();
            $table->string('allergies')->nullable();
            $table->string('medical_conditions')->nullable();
            $table->string('medications')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->text('health_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('student_transport', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id')->unique();
            $table->string('route_name')->nullable();
            $table->string('pickup_point')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('status')->default('active');
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('student_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->string('activity_type')->default('Club');
            $table->string('activity_name');
            $table->string('role')->nullable();
            $table->string('session')->nullable();
            $table->string('achievements')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('student_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->string('event_type');
            $table->string('description');
            $table->string('meta')->nullable();
            $table->unsignedInteger('recorded_by')->nullable();
            $table->timestamps();
        });

        $defaults = [
            'student_id_prefix' => 'CJ',
            'student_doc_types' => 'Birth Certificate, Admission Letter, Medical Report, Report Card, Transfer Certificate, Passport, Certificate',
            'student_required_fields' => 'name,gender,address,nal_id,state_id,lga_id,my_class_id,section_id,year_admitted',
        ];

        foreach ($defaults as $type => $description) {
            $exists = DB::table('settings')->where('type', $type)->exists();
            if (!$exists) {
                DB::table('settings')->insert(['type' => $type, 'description' => $description, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('student_events');
        Schema::dropIfExists('student_activities');
        Schema::dropIfExists('student_transport');
        Schema::dropIfExists('student_health');
        Schema::dropIfExists('student_discipline');
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('student_attendance');
        Schema::dropIfExists('student_guardians');

        DB::table('settings')->whereIn('type', ['student_id_prefix', 'student_doc_types', 'student_required_fields'])->delete();
    }
}