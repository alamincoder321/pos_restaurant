<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code')->index();
            $table->string('name',);
            $table->string('title');
            $table->text('address', 500)->nullable();
            $table->string('phone', 15);
            $table->char('status', 1)->default('a');
            $table->integer('created_by')->index();
            $table->dateTime('created_at')->useCurrent();
            $table->integer('updated_by')->nullable()->index();
            $table->dateTime('updated_at')->nullable();
            $table->integer('deleted_by')->nullable()->index();
            $table->softDeletes();
            $table->ipAddress('ipAddress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
