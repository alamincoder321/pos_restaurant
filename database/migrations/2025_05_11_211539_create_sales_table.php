<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->date('date');
            $table->string('table_id', 100)->nullable();
            $table->bigInteger('employee_id')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers', 'id');
            $table->string('customer_type')->default('general');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->decimal('subtotal')->default(0);
            $table->decimal('discount')->default(0);
            $table->decimal('vat')->default(0);
            $table->decimal('transport_cost')->default(0);
            $table->decimal('total')->default(0);
            $table->decimal('cashPaid')->default(0);
            $table->decimal('bankPaid')->default(0);
            $table->decimal('paid')->default(0);
            $table->decimal('returnAmount')->default(0);
            $table->decimal('due')->default(0);
            $table->decimal('previous_due')->default(0);
            $table->enum('sale_type', ['retail', 'wholesale'])->default('retail')->index();
            $table->text('note')->nullable();
            $table->string('order_type', 50)->default('dine-in')->comment('dine-in, take-away, delivery');
            $table->enum('order_status', ['pending', 'preparing', 'served', 'completed', 'cancelled'])->default('pending')->comment('pending, preparing, served, completed, cancelled');
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
        Schema::dropIfExists('sales');
    }
}
