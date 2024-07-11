<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNoticeTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notice', function (Blueprint $table) {
			$table->increments('notice_id');
			$table->string('title', 300);
			$table->text('description');
			$table->string('status', 20);
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->date('publish_date');
			$table->string('attach_file', 191)->nullable();
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
		Schema::drop('notice');
	}
}
