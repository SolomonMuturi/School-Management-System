<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceReceiptsTable extends Migration
{
    public function up()
    {
        Schema::create('finance_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no');
            $table->unsignedBigInteger('finance_payment_id');
            $table->unsignedInteger('student_id');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method');
            $table->string('reference_no')->nullable();
            $table->decimal('balance_after', 12, 2)->default(0);
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
        Schema::dropIfExists('finance_receipts');
    }
}