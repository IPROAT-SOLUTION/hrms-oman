<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFrontSettingsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('front_settings', function (Blueprint $table) {
			$table->increments('id');
			$table->string('company_title', 191);
			$table->text('home_page_big_title');
			$table->text('short_description');
			$table->string('service_title', 191);
			$table->string('job_title', 191);
			$table->string('about_us_image', 191);
			$table->string('logo', 191);
			$table->text('footer_text')->nullable();
			$table->text('about_us_description');
			$table->string('contact_website', 191)->nullable();
			$table->string('contact_phone', 191)->nullable();
			$table->string('contact_email', 191)->nullable();
			$table->text('contact_address')->nullable();
			$table->string('counter_1_title', 191);
			$table->integer('counter_1_value');
			$table->string('counter_2_title', 191);
			$table->integer('counter_2_value');
			$table->string('counter_3_title', 191);
			$table->integer('counter_3_value');
			$table->string('counter_4_title', 191);
			$table->integer('counter_4_value');
			$table->boolean('show_job')->nullable()->default(1);
			$table->boolean('show_service')->nullable()->default(1);
			$table->boolean('show_about')->nullable()->default(1);
			$table->boolean('show_contact')->nullable()->default(1);
			$table->boolean('show_counter')->nullable()->default(1);
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
		Schema::drop('front_settings');
	}
}
