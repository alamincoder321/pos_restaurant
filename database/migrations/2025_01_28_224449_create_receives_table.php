<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receives', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->foreignId('customer_id')->nullable()->constrained('customers', 'id');
            $table->date('date');
            $table->enum('type', ['customer'])->default('customer');
            $table->enum('payment_method', ['cash', 'bank'])->default('cash');
            $table->foreignId('bank_id')->nullable()->constrained('banks', 'id');
            $table->decimal('amount')->default(0);
            $table->decimal('previous_due')->default(0);
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
        Schema::dropIfExists('receives');
    }
}
