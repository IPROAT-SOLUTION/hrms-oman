<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompOffTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comp_off', function (Blueprint $table) {
			$table->bigInteger('comp_off_details_id', true)->unsigned();
			$table->integer('employee_id')->nullable()->index();
			$table->string('finger_print_id', 191)->index();
			$table->integer('branch_id')->index();
			$table->date('off_date')->index();
			$table->date('working_date')->index();
			$table->boolean('off_timing')->default(1)->index();
			$table->text('comment')->nullable();
			$table->integer('created_by')->nullable()->index();
			$table->integer('updated_by')->nullable()->index();
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
		Schema::drop('comp_off');
	}
}
