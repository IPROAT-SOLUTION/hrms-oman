<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGeneralSettingsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('general_settings', function (Blueprint $table) {
			$table->integer('genset_id');
			$table->text('email_ids')->nullable();
			$table->timestamps();
			$table->text('employeedoc_mail_subject')->nullable();
			$table->text('employeedoc_sender_mail')->nullable();
			$table->text('employeedoc_sender_name')->nullable();
			$table->text('officedoc_mail_subject')->nullable();
			$table->text('officedoc_sender_mail')->nullable();
			$table->text('officedoc_sender_name')->nullable();
			$table->text('employeedoc_mail_admin_subject')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('general_settings');
	}
}
