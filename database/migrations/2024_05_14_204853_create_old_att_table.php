<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOldAttTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('old_att', function (Blueprint $table) {
			$table->string('OLD_ID', 50);
			$table->date('Date');
			$table->text('AttCode');
			$table->time('TIMEIN');
			$table->time('TIMEOUT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('old_att');
	}
}
