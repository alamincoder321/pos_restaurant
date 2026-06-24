<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_id')->nullable()->constrained('salary_masters', 'id');
            $table->foreignId('employee_id')->nullable()->constrained('users', 'id');
            $table->decimal('basic_salary')->default(0);
            $table->decimal('house_rent')->default(0);
            $table->decimal('medical_fee')->default(0);
            $table->decimal('other_fee')->default(0);
            $table->decimal('gross_salary')->default(0);
            $table->decimal('ot_amount')->default(0);
            $table->decimal('deduction')->default(0);
            $table->decimal('advance')->default(0);
            $table->decimal('total')->default(0);
            $table->decimal('paid')->default(0);
            $table->decimal('due')->default(0);
            $table->text('note')->nullable();
            $table->char('status', 1)->default('a');
            $table->foreignId('created_by')->nullable()->constrained('users', 'id');
            $table->dateTime('created_at')->useCurrent();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
            $table->dateTime('updated_at')->nullable();
            $table->foreignId('deleted_by')->nullable()->constrained('users', 'id');
            $table->softDeletes();
            $table->ipAddress('ipAddress');
            $table->foreignId('branch_id')->constrained('branches', 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_details');
    }
}
