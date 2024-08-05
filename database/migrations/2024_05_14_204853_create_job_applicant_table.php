<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobApplicantTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('job_applicant', function (Blueprint $table) {
			$table->increments('job_applicant_id');
			$table->integer('job_id')->unsigned();
			$table->string('applicant_name', 100);
			$table->string('applicant_email', 100);
			$table->integer('phone');
			$table->text('cover_letter');
			$table->string('attached_resume', 200);
			$table->date('application_date');
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
		Schema::drop('job_applicant');
	}
}
