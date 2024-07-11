<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhiteListedIpsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('white_listed_ips', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('ip_setting_id')->nullable()->default(0);
			$table->string('white_listed_ip', 191)->nullable();
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
		Schema::drop('white_listed_ips');
	}

}
