<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundsTable extends Migration
{
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('finance_payment_id');
            $table->unsignedInteger('student_id');
            $table->decimal('amount', 12, 2);
            $table->string('reason');
            $table->string('processed_by')->nullable();
            $table->string('status')->default('completed');
            $table->date('refund_date');
            $table->string('session');
            $table->string('term');
            $table->string('year');
            $table->timestamps();

            $table->foreign('finance_payment_id')->references('id')->on('finance_payments')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('refunds');
    }
}