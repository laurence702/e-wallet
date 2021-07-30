<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class SetUpUserModule extends Migration
{
	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('first_name', 50);
			$table->string('last_name', 50);
			$table->string('phone', 100)->nullable();
			$table->string('email', 100)->nullable();
			$table->string('pin', 20);
			$table->boolean('verified')->nullable()->default(0);
			$table->double('account_balance', 8, 2)->nullable()->default(00.000000);
			$table->timestamps();
			$table->softDeletes();
		});
	}
	
	
	public function down()
	{
		 Schema::dropIfExists('users');
	}
}
