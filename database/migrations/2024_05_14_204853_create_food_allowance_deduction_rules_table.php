<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFoodAllowanceDeductionRulesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('food_allowance_deduction_rules', function (Blueprint $table) {
			$table->increments('food_allowance_deduction_rule_id');
			$table->integer('breakfast_cost');
			$table->integer('lunch_cost');
			$table->integer('dinner_cost');
			$table->boolean('status')->default(1);
			$table->text('remarks')->nullable();
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
		Schema::drop('food_allowance_deduction_rules');
	}
}
