<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->unsignedBigInteger('student_fee_id');
            $table->enum('type', ['discount', 'scholarship', 'waiver'])->default('discount');
            $table->decimal('amount', 12, 2);
            $table->string('reason')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('status')->default('active');
            $table->string('session');
            $table->string('term');
            $table->string('year');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_fee_id')->references('id')->on('student_fees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}