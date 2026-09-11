<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->decimal('amount', 12, 2);
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->unsignedInteger('student_id')->nullable();
            $table->unsignedBigInteger('student_fee_id')->nullable();
            $table->string('checkout_request_id')->nullable();
            $table->string('merchant_request_id')->nullable();
            $table->string('mpesa_receipt_number')->nullable();
            $table->string('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('finance_payment_id')->nullable();
            $table->longText('raw_callback')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('checkout_request_id');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_fee_id')->references('id')->on('student_fees')->onDelete('cascade');
            $table->foreign('finance_payment_id')->references('id')->on('finance_payments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mpesa_transactions');
    }
}