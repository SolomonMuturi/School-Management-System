<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('finance_account_id');
            $table->enum('type', ['income', 'expense', 'refund', 'transfer'])->default('income');
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            $table->date('transaction_date');
            $table->string('reference_no')->nullable();
            $table->string('related_to')->nullable();
            $table->string('session');
            $table->string('year');
            $table->timestamps();

            $table->foreign('finance_account_id')->references('id')->on('finance_accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('finance_transactions');
    }
}