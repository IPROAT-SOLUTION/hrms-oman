<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManualAttendanceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manual_attendance', function(Blueprint $table)
		{
			$table->bigInteger('primary_id', true)->unsigned();
			$table->integer('branch_id')->nullable()->index();
			$table->string('ID', 50)->index('manual_attendance_id_index');
			$table->string('type', 11)->nullable()->index();
			$table->timestamp('datetime')->default(DB::raw('CURRENT_TIMESTAMP'))->index();
			$table->boolean('status')->default(0);
			$table->string('device_name', 191)->nullable()->index();
			$table->string('devuid', 191)->nullable()->index();
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
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
		Schema::drop('manual_attendance');
	}

}
