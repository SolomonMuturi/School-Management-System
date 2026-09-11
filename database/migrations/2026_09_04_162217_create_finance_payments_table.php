<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('finance_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->unsignedBigInteger('student_fee_id');
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method');
            $table->string('reference_no')->nullable();
            $table->string('received_by')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('completed');
            $table->unsignedBigInteger('finance_account_id')->nullable();
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
        Schema::dropIfExists('finance_payments');
    }
}