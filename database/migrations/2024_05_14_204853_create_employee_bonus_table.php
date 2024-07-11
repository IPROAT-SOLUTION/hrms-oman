<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeBonusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_bonus', function(Blueprint $table)
		{
			$table->increments('employee_bonus_id');
			$table->integer('bonus_setting_id');
			$table->integer('employee_id');
			$table->string('month', 191);
			$table->integer('gross_salary');
			$table->integer('basic_salary');
			$table->integer('bonus_amount');
			$table->integer('tax');
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
		Schema::drop('employee_bonus');
	}

}
