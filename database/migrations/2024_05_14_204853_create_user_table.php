<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user', function(Blueprint $table)
		{
			$table->increments('user_id');
			$table->integer('role_id')->unsigned();
			$table->integer('branch_id')->nullable();
			$table->string('user_name', 50);
			$table->string('password', 200);
			$table->string('org_password', 200)->nullable();
			$table->boolean('status')->default(1);
			$table->string('remember_token', 100)->nullable();
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->string('device_employee_id', 191)->nullable();
			$table->softDeletes();
			$table->timestamps();
			$table->string('google2fa_secret', 191)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user');
	}

}
