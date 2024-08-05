<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTrainingInfoTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('training_info', function (Blueprint $table) {
			$table->increments('training_info_id');
			$table->integer('training_type_id')->unsigned();
			$table->integer('employee_id')->unsigned();
			$table->string('subject', 200);
			$table->date('start_date');
			$table->date('end_date');
			$table->text('description');
			$table->string('certificate', 200);
			$table->integer('created_by');
			$table->integer('updated_by');
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
		Schema::drop('training_info');
	}
}
