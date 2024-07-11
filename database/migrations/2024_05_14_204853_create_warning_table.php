<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWarningTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('warning', function (Blueprint $table) {
			$table->increments('warning_id');
			$table->integer('warning_to')->unsigned();
			$table->string('warning_type', 191);
			$table->string('subject', 191);
			$table->integer('warning_by')->unsigned();
			$table->date('warning_date');
			$table->text('description')->nullable();
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
		Schema::drop('warning');
	}
}
