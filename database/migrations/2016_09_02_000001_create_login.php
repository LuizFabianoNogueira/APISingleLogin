<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users'))
        {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('login')->unique()->nullable();
                $table->string('email')->unique()->nullable();
                $table->string('phone')->unique()->nullable();
                $table->string('password');
                $table->integer('level_of_access');
                $table->boolean('active')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('password_resets'))
        {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token')->index();
                $table->timestamp('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('users'))
        {
            Schema::drop('users');
        }

        if (Schema::hasTable('password_resets'))
        {
            Schema::drop('password_resets');
        }
    }
}
