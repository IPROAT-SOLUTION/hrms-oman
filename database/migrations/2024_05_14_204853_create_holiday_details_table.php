<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHolidayDetailsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('holiday_details', function (Blueprint $table) {
			$table->increments('holiday_details_id');
			$table->integer('branch_id')->nullable()->index();
			$table->integer('holiday_id')->unsigned()->index();
			$table->date('from_date')->index();
			$table->date('to_date')->index();
			$table->string('leave_timing', 191)->nullable();
			$table->text('comment')->nullable();
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
		Schema::drop('holiday_details');
	}
}
