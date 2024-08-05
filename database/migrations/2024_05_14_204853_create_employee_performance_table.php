<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeePerformanceTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_performance', function (Blueprint $table) {
			$table->increments('employee_performance_id');
			$table->integer('employee_id');
			$table->string('month', 191);
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->text('remarks')->nullable();
			$table->boolean('status')->default(0);
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
		Schema::drop('employee_performance');
	}
}
