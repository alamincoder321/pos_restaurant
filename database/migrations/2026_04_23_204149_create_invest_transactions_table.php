<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invest_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invest_account_id')->constrained('invest_accounts', 'id');
            $table->date('date');
            $table->enum('type', ['deposit', 'withdraw', 'profit', 'loss']);
            $table->decimal('amount', 18, 2);
            $table->string('note')->nullable();
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
        Schema::dropIfExists('invest_transactions');
    }
}
