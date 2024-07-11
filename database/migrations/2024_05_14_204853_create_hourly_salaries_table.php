<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHourlySalariesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hourly_salaries', function(Blueprint $table)
		{
			$table->increments('hourly_salaries_id');
			$table->string('hourly_grade', 191);
			$table->integer('hourly_rate');
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
		Schema::drop('hourly_salaries');
	}

}
