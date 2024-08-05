<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('job', function (Blueprint $table) {
			$table->increments('job_id');
			$table->string('job_title', 200);
			$table->string('post', 200);
			$table->text('job_description');
			$table->date('application_end_date');
			$table->date('publish_date');
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->boolean('status');
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
		Schema::drop('job');
	}
}
