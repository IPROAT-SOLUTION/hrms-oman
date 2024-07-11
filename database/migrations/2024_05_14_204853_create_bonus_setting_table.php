<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBonusSettingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bonus_setting', function(Blueprint $table)
		{
			$table->increments('bonus_setting_id');
			$table->string('festival_name', 191);
			$table->integer('percentage_of_bonus');
			$table->string('bonus_type')->comment('Gross, Basic');
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
		Schema::drop('bonus_setting');
	}

}
