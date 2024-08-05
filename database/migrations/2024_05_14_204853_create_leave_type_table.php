<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaveTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('leave_type', function(Blueprint $table)
		{
			$table->increments('leave_type_id');
			$table->string('leave_type_name', 191);
			$table->integer('num_of_day');
			$table->boolean('nationality')->comment('0-omanies,1-expacts');
			$table->boolean('religion')->comment('0-muslim,1-non-muslim');
			$table->boolean('gender');
			$table->integer('branch_id')->nullable();
			$table->integer('status')->default(1);
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
		Schema::drop('leave_type');
	}

}
