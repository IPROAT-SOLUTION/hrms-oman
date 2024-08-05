<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSessionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sessions', function (Blueprint $table) {
			$table->string('id', 191)->unique();
			$table->integer('user_id')->unsigned()->nullable()->index();
			$table->string('ip_address', 45)->nullable()->index();
			$table->text('user_agent')->nullable();
			$table->text('payload');
			$table->integer('last_activity')->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sessions');
	}
}
