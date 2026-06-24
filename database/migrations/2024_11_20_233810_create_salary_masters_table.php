<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_masters', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->index();
            $table->string('month')->index();
            $table->date('date')->index();
            $table->decimal('amount')->default(0);
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
        Schema::dropIfExists('salary_masters');
    }
}
