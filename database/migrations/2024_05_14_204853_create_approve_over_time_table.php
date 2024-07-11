<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApproveOverTimeTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('approve_over_time', function (Blueprint $table) {
			$table->bigInteger('approve_over_time_id', true)->unsigned();
			$table->integer('branch_id')->nullable()->index();
			$table->string('finger_print_id')->nullable()->index();
			$table->date('date')->nullable()->index();
			$table->time('actual_overtime')->nullable()->index();
			$table->time('approved_overtime')->nullable()->index();
			$table->text('remark')->nullable();
			$table->boolean('status')->nullable()->default(0)->index();
			$table->integer('updated_by')->nullable()->index();
			$table->integer('created_by')->nullable()->index();
			$table->timestamps();
			$table->decimal('gross_salary', 9, 3)->nullable();
			$table->decimal('per_hour_salary', 9, 6)->nullable();
			$table->decimal('over_time_amount', 9, 3)->nullable()->default(0.000);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('approve_over_time');
	}
}
