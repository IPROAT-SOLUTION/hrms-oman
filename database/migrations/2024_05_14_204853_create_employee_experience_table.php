<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeExperienceTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_experience', function (Blueprint $table) {
			$table->increments('employee_experience_id');
			$table->integer('branch_id')->nullable();
			$table->integer('employee_id')->unsigned();
			$table->string('organization_name', 200);
			$table->string('designation', 200);
			$table->date('from_date');
			$table->date('to_date');
			$table->text('skill');
			$table->text('responsibility');
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
		Schema::drop('employee_experience');
	}
}
