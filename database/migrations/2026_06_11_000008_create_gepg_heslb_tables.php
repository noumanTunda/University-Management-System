<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGepgHeslbTables extends Migration
{
    public function up()
    {
        // GePG Bills — control numbers for government payment gateway
        Schema::create('gepg_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->integer('fee_collection_id')->unsigned()->nullable();
            $table->string('control_number', 30)->unique()->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('bill_description', 255);
            $table->enum('status', ['Pending', 'Issued', 'Paid', 'Expired'])->default('Pending');
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('fee_collection_id')->references('id')->on('fee_collections');
        });

        // GePG Payment Receipts — callback records from the treasury
        Schema::create('gepg_payment_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('control_number', 30);
            $table->string('transaction_id', 60)->unique();
            $table->decimal('amount_paid', 15, 2);
            $table->string('payment_provider', 50);
            $table->string('payer_mobile', 15)->nullable();
            $table->dateTime('paid_at');
            $table->timestamps();

            $table->foreign('control_number')->references('control_number')->on('gepg_bills');
        });

        // HESLB Loan Allocations
        Schema::create('heslb_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->integer('semester_id')->unsigned();
            $table->string('batch_no', 15);
            $table->decimal('tuition_fee', 15, 2)->default(0);
            $table->decimal('meals_accommodation', 15, 2)->default(0);
            $table->decimal('books_stationery', 15, 2)->default(0);
            $table->decimal('special_faculty_req', 15, 2)->default(0);
            $table->enum('disbursement_status', ['Allocated', 'Disbursed', 'Held'])->default('Allocated');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('semester_id')->references('id')->on('semesters');
        });

        // Add NECTA index columns to students
        Schema::table('students', function (Blueprint $table) {
            $table->string('necta_f4_index', 25)->nullable()->unique()->after('idNo');
            $table->string('necta_f6_index', 25)->nullable()->unique()->after('necta_f4_index');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('necta_f6_index');
            $table->dropColumn('necta_f4_index');
        });
        Schema::drop('heslb_allocations');
        Schema::drop('gepg_payment_receipts');
        Schema::drop('gepg_bills');
    }
}
