<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('phone', 15);
            $table->string('email')->nullable();
            $table->longText('address')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo')->nullable();
            $table->text('url')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
            $table->dateTime('updated_at')->nullable();
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
        Schema::dropIfExists('company_profiles');
    }
}
