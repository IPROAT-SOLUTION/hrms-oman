<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIpSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ip_settings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('ip_address', 191)->nullable()->index();
			$table->boolean('ip_status')->default(0)->index()->comment('0 = not checking it 1 = checking ip');
			$table->boolean('status')->index()->comment('0 = not providing employee self attendance 1 = providing');
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
		Schema::drop('ip_settings');
	}

}
