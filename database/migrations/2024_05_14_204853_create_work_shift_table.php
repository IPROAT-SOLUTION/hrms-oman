<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkShiftTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('work_shift', function(Blueprint $table)
		{
			$table->increments('work_shift_id');
			$table->string('shift_name', 100)->index();
			$table->integer('branch_id')->nullable()->index();
			$table->time('start_time')->index();
			$table->time('end_time')->index();
			$table->time('late_count_time')->index();
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
		Schema::drop('work_shift');
	}

}
