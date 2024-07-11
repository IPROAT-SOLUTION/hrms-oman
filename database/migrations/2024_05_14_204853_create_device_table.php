<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeviceTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('device', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', 50)->nullable();
			$table->string('ip', 50)->nullable()->unique();
			$table->string('protocol', 10)->nullable();
			$table->text('model')->nullable();
			$table->boolean('status')->nullable();
			$table->boolean('device_status');
			$table->string('created_by', 191)->nullable();
			$table->string('updated_by', 191)->nullable();
			$table->integer('port')->nullable();
			$table->string('username', 191)->nullable();
			$table->string('password', 100)->nullable();
			$table->text('devIndex')->nullable();
			$table->text('devResponse')->nullable();
			$table->boolean('verification_status')->nullable();
			$table->boolean('type')->nullable();
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
		Schema::drop('device');
	}
}
