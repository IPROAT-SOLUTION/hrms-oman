<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSocialSecurityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_security', function(Blueprint $table)
		{
			$table->integer('social_security_id', true);
			$table->decimal('gross_salary', 9, 3)->nullable();
			$table->date('year');
			$table->boolean('nationality')->comment('0-Omanis,1-Expats');
			$table->decimal('percentage', 9, 3);
			$table->timestamps();
			$table->decimal('employer_contribution', 10, 3)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('social_security');
	}

}
