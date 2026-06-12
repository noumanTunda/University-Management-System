<?php
use Illuminate\Database\Migrations\Migration;

class AddDormitorySigninout extends Migration
{
    public function up()
    {
        Schema::table('dormitory_students', function ($table) {
            $table->dateTime('signed_out_at')->nullable()->after('isActive');
            $table->string('signout_reason', 255)->nullable()->after('signed_out_at');
        });

        Schema::create('dormitory_requests', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('dormitory_student_id');
            $table->unsignedInteger('student_id');
            $table->enum('type', ['signin']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->foreign('dormitory_student_id')->references('id')->on('dormitory_students')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dormitory_requests');
        Schema::table('dormitory_students', function ($table) {
            $table->dropColumn(['signed_out_at', 'signout_reason']);
        });
    }
}
