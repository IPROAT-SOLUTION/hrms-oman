<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeAwardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_award', function(Blueprint $table)
		{
			$table->increments('employee_award_id');
			$table->integer('employee_id');
			$table->string('award_name', 191);
			$table->string('gift_item', 191);
			$table->string('month', 191);
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
		Schema::drop('employee_award');
	}

}
