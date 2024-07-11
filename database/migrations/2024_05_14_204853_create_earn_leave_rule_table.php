<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEarnLeaveRuleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('earn_leave_rule', function(Blueprint $table)
		{
			$table->increments('earn_leave_rule_id');
			$table->integer('branch_id')->nullable()->index();
			$table->integer('for_month')->index();
			$table->float('day_of_earn_leave')->index();
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
		Schema::drop('earn_leave_rule');
	}

}
