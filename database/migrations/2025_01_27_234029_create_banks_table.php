<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number');
            $table->string('type')->default('savings');
            $table->string('branch_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->decimal('balance')->default(0);
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
        Schema::dropIfExists('banks');
    }
}
