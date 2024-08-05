<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeAccessControlTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_access_control', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('employee')->nullable()->index();
			$table->integer('department')->nullable()->index();
			$table->integer('branch_id')->nullable()->index();
			$table->integer('device')->nullable()->index();
			$table->integer('user_id')->nullable()->index();
			$table->string('device_employee_id', 191)->nullable()->index();
			$table->boolean('status')->nullable()->index();
			$table->integer('created_by')->nullable()->index();
			$table->integer('updated_by')->nullable()->index();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('employee_access_control');
	}

}
