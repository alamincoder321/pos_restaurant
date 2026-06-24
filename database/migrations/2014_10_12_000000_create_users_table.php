<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('emp_code')->nullable();
            $table->string('name');
            $table->string('username');
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('phone', 15);
            $table->string('role');
            $table->integer('department_id')->index()->nullable();
            $table->integer('designation_id')->index()->nullable();
            $table->enum('gender', ['male', 'female', 'others'])->nullable();
            $table->date('birth_date')->nullable();
            $table->date('join_date')->nullable();
            $table->text('address')->nullable();
            $table->decimal('gross_salary', 18, 2)->default(0);
            $table->decimal('basic_salary', 18, 2)->default(0);
            $table->decimal('house_rent', 18, 2)->default(0);
            $table->decimal('medical_fee', 18, 2)->default(0);
            $table->decimal('other_fee', 18, 2)->default(0);
            $table->string('reference')->nullable();
            $table->string('image')->nullable();
            $table->char('status', 1)->default('a');
            $table->string('action')->nullable()->comment('e=>entry u=>update d=>delete');
            $table->integer('created_by')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
            $table->ipAddress('ipAddress');
            $table->integer('branch_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
