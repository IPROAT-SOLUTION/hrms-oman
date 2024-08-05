<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIncentivesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('incentives', function (Blueprint $table) {
			$table->bigInteger('incentive_details_id', true)->unsigned();
			$table->string('finger_print_id', 191)->index();
			$table->integer('branch_id')->index();
			$table->date('incentive_date')->index();
			$table->date('working_date')->nullable()->index();
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
		Schema::drop('incentives');
	}
}
