<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMyattTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('myatt', function(Blueprint $table)
		{
			$table->integer('myID', true);
			$table->string('finger_print_id', 50);
			$table->date('date')->nullable();
			$table->string('AttCode', 5);
			$table->dateTime('in_time')->nullable();
			$table->dateTime('out_time')->nullable();
			$table->time('working_time')->nullable();
			$table->time('working_hour')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('myatt');
	}

}
