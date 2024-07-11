<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReminderTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reminder', function (Blueprint $table) {
			$table->integer('reminder_id');
			$table->text('title')->nullable();
			$table->text('content')->nullable();
			$table->date('expiry_date')->nullable();
			$table->timestamps();
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
			$table->boolean('status')->nullable()->default(1);
			$table->dateTime('last_reminder')->nullable();
			$table->string('file')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reminder');
	}
}
