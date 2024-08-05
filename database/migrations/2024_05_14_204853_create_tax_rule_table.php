<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaxRuleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tax_rule', function(Blueprint $table)
		{
			$table->increments('tax_rule_id');
			$table->integer('amount');
			$table->float('percentage_of_tax', 10, 0);
			$table->integer('amount_of_tax');
			$table->string('gender', 20);
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
		Schema::drop('tax_rule');
	}

}
