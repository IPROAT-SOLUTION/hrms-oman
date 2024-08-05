<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployeeShiftTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_shift', function(Blueprint $table)
		{
			$table->bigInteger('employee_shift_id', true)->unsigned();
			$table->string('finger_print_id', 191)->index();
			$table->string('month', 10)->index();
			$table->integer('branch_id')->nullable();
			$table->string('d_1', 10)->nullable()->index();
			$table->string('d_2', 10)->nullable()->index();
			$table->string('d_3', 10)->nullable()->index();
			$table->string('d_4', 10)->nullable()->index();
			$table->string('d_5', 10)->nullable()->index();
			$table->string('d_6', 10)->nullable()->index();
			$table->string('d_7', 10)->nullable()->index();
			$table->string('d_8', 10)->nullable()->index();
			$table->string('d_9', 10)->nullable()->index();
			$table->string('d_10', 10)->nullable()->index();
			$table->string('d_11', 10)->nullable()->index();
			$table->string('d_12', 10)->nullable()->index();
			$table->string('d_13', 10)->nullable()->index();
			$table->string('d_14', 10)->nullable()->index();
			$table->string('d_15', 10)->nullable()->index();
			$table->string('d_16', 10)->nullable()->index();
			$table->string('d_17', 10)->nullable()->index();
			$table->string('d_18', 10)->nullable()->index();
			$table->string('d_19', 10)->nullable()->index();
			$table->string('d_20', 10)->nullable()->index();
			$table->string('d_21', 10)->nullable()->index();
			$table->string('d_22', 10)->nullable()->index();
			$table->string('d_23', 10)->nullable()->index();
			$table->string('d_24', 10)->nullable()->index();
			$table->string('d_25', 10)->nullable()->index();
			$table->string('d_26', 10)->nullable()->index();
			$table->string('d_27', 10)->nullable()->index();
			$table->string('d_28', 10)->nullable()->index();
			$table->string('d_29', 10)->nullable()->index();
			$table->string('d_30', 10)->nullable()->index();
			$table->string('d_31', 10)->nullable()->index();
			$table->string('remarks')->nullable()->index();
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
		Schema::drop('employee_shift');
	}

}
