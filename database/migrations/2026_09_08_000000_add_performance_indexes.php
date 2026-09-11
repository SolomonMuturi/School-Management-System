<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPerformanceIndexes extends Migration
{
    protected $indexes = [
        'student_records'         => ['my_class_id', 'user_id', 'my_parent_id'],
        'student_fees'            => ['student_id', 'fee_structure_id'],
        'finance_payments'        => ['student_id', 'student_fee_id', 'finance_account_id'],
        'finance_receipts'        => ['finance_payment_id', 'student_id'],
        'marks'                   => ['exam_id', 'my_class_id', 'student_id'],
        'exam_records'            => ['exam_id', 'my_class_id', 'student_id'],
        'discounts'               => ['student_id'],
        'refunds'                 => ['student_id'],
        'expenses'                => ['finance_account_id'],
        'finance_transactions'    => ['finance_account_id'],
        'subjects'                => ['my_class_id'],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->indexes as $table => $cols) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($cols as $col) {
                if (!Schema::hasColumn($table, $col)) {
                    continue;
                }
                $index = $table . '_' . $col . '_index';
                try {
                    if (!Schema::hasIndex($table, $index)) {
                        Schema::table($table, function (Blueprint $table) use ($col) {
                            $table->index($col);
                        });
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->indexes as $table => $cols) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($cols as $col) {
                $index = $table . '_' . $col . '_index';
                try {
                    if (Schema::hasIndex($table, $index)) {
                        Schema::table($table, function (Blueprint $table) use ($index) {
                            $table->dropIndex($index);
                        });
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }
    }
}