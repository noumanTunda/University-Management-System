<?php

use Illuminate\Database\Migrations\Migration;

class CreateAccountingTables extends Migration
{
    public function up()
    {
        // 1. Chart of Accounts
        Schema::create('chart_of_accounts', function ($table) {
            $table->increments('id');
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->enum('type', ['Asset', 'Liability', 'Income', 'Expense']);
            $table->decimal('balance', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Journal Entries
        Schema::create('journal_entries', function ($table) {
            $table->increments('id');
            $table->date('entry_date');
            $table->string('description', 255);
            $table->string('reference_type', 50)->nullable(); // e.g. 'invoice', 'payment', 'gepg'
            $table->unsignedInteger('reference_id')->nullable();
            $table->timestamps();
        });

        // 3. Journal Entry Items (debits/credits)
        Schema::create('journal_entry_items', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('journal_entry_id');
            $table->unsignedInteger('account_id');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts');
        });

        // 4. Fee Invoices (per student)
        Schema::create('fee_invoices', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->string('invoice_no', 30)->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('status', ['Pending', 'Partial', 'Paid', 'Overdue', 'Cancelled'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students');
        });

        // 5. Invoice Items
        Schema::create('invoice_items', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id');
            $table->string('description', 255);
            $table->decimal('amount', 15, 2);
            $table->unsignedInteger('fee_id')->nullable();
            $table->unsignedInteger('account_id');
            $table->timestamps();
            $table->foreign('invoice_id')->references('id')->on('fee_invoices')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts');
        });

        // 6. Payment Allocations (linking payments to invoices)
        Schema::create('payment_allocations', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('receipt_id')->nullable(); // gepg_payment_receipts.id
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method', 50)->default('GePG');
            $table->string('reference', 100)->nullable();
            $table->timestamps();
            $table->foreign('invoice_id')->references('id')->on('fee_invoices');
        });

        // Seed default chart of accounts
        $accounts = [
            ['code' => '1001', 'name' => 'Cash & Bank',               'type' => 'Asset'],
            ['code' => '1002', 'name' => 'Student Receivables',        'type' => 'Asset'],
            ['code' => '2001', 'name' => 'Deferred Revenue',           'type' => 'Liability'],
            ['code' => '4001', 'name' => 'Tuition Fees',               'type' => 'Income'],
            ['code' => '4002', 'name' => 'Laboratory Fees',            'type' => 'Income'],
            ['code' => '4003', 'name' => 'Library Fees',               'type' => 'Income'],
            ['code' => '4004', 'name' => 'Registration Fees',          'type' => 'Income'],
            ['code' => '4005', 'name' => 'Penalties & Fines',          'type' => 'Income'],
            ['code' => '4006', 'name' => 'Other Income',               'type' => 'Income'],
            ['code' => '5001', 'name' => 'Salaries & Wages',           'type' => 'Expense'],
            ['code' => '5002', 'name' => 'Utilities',                  'type' => 'Expense'],
            ['code' => '5003', 'name' => 'General Expenses',           'type' => 'Expense'],
        ];
        foreach ($accounts as $a) {
            DB::table('chart_of_accounts')->insert($a);
        }
    }

    public function down()
    {
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('fee_invoices');
        Schema::dropIfExists('journal_entry_items');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
    }
}
