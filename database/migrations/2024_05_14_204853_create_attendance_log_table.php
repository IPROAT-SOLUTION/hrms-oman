<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceLogTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_log', function (Blueprint $table) {
			$table->increments('attendance_log_id');
			$table->integer('branch_id')->nullable()->index();
			$table->string('employeeId', 191)->nullable()->index('attendance_log_employeeid_index');
			$table->date('date')->index();
			$table->time('time')->index();
			$table->string('deviceSerial', 191)->nullable()->index('attendance_log_deviceserial_index');
			$table->string('deviceId', 191)->nullable()->index('attendance_log_deviceid_index');
			$table->string('locationName', 191)->nullable()->index('attendance_log_locationname_index');
			$table->string('locationId', 191)->nullable()->index('attendance_log_locationid_index');
			$table->string('mode', 191)->nullable()->index();
			$table->boolean('type')->nullable()->index();
			$table->string('deviceName', 191)->nullable()->index('attendance_log_devicename_index');
			$table->string('lateEntry', 191)->nullable()->index('attendance_log_lateentry_index');
			$table->string('companyDisplayId', 191)->nullable()->index('attendance_log_companydisplayid_index');
			$table->text('companyName')->nullable();
			$table->text('lastEvaluatedKey')->nullable();
			$table->integer('size')->nullable();
			$table->boolean('status')->nullable()->default(0)->index();
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
		Schema::drop('attendance_log');
	}
}
