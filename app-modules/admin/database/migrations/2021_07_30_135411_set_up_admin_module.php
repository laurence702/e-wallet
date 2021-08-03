<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class SetUpAdminModule extends Migration
{
	public function up()
	{
		Schema::create('admins', function(Blueprint $table) {
		
		});
	}
	
	public function down()
	{
		//Schema::dropIfExists('admins');
	}
}
