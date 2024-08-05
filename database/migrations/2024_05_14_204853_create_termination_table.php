<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTerminationTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('termination', function (Blueprint $table) {
			$table->increments('termination_id');
			$table->integer('terminate_to')->unsigned();
			$table->integer('terminate_by')->unsigned();
			$table->string('termination_type', 191);
			$table->string('subject', 191);
			$table->date('notice_date');
			$table->date('termination_date');
			$table->text('description')->nullable();
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
		Schema::drop('termination');
	}
}
