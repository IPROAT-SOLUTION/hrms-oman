<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInterviewTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('interview', function (Blueprint $table) {
			$table->increments('interview_id');
			$table->integer('job_applicant_id')->unsigned();
			$table->date('interview_date');
			$table->time('interview_time');
			$table->string('interview_type', 191);
			$table->text('comment');
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
		Schema::drop('interview');
	}
}
