<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWeeklyHolidayTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('weekly_holiday', function (Blueprint $table) {
			$table->increments('week_holiday_id');
			$table->integer('branch_id')->nullable()->index();
			$table->integer('employee_id')->nullable()->index();
			$table->string('month', 191)->nullable()->index();
			$table->text('day_name')->index();
			$table->string('weekoff_days', 191)->nullable()->index();
			$table->boolean('status')->default(1)->index();
			$table->boolean('created_by')->nullable();
			$table->boolean('updated_by')->nullable();
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
		Schema::drop('weekly_holiday');
	}
}
