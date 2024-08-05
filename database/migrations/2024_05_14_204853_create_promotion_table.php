<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromotionTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promotion', function (Blueprint $table) {
			$table->increments('promotion_id');
			$table->integer('employee_id')->unsigned();
			$table->integer('current_department')->unsigned();
			$table->integer('current_designation')->unsigned();
			$table->integer('current_pay_grade');
			$table->integer('current_salary');
			$table->integer('promoted_pay_grade')->unsigned();
			$table->integer('new_salary');
			$table->integer('promoted_department')->unsigned();
			$table->integer('promoted_designation')->unsigned();
			$table->date('promotion_date');
			$table->text('description')->nullable();
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->boolean('status')->default(1);
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
		Schema::drop('promotion');
	}
}
