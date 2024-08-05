<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMsSqlTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ms_sql', function (Blueprint $table) {
			$table->increments('primary_id');
			$table->integer('branch_id')->nullable()->index();
			$table->integer('local_primary_id')->nullable()->index();
			$table->integer('evtlguid')->nullable()->index();
			$table->string('ID', 191)->nullable()->index('ms_sql_id_index');
			$table->string('type', 191)->nullable()->index();
			$table->timestamp('datetime')->default(DB::raw('CURRENT_TIMESTAMP'))->index();
			$table->boolean('status')->default(0)->index();
			$table->integer('employee')->nullable()->index();
			$table->integer('device')->nullable()->index();
			$table->string('device_employee_id', 191)->nullable()->index();
			$table->text('sms_log')->nullable();
			$table->string('device_name', 191)->nullable()->index();
			$table->string('devuid', 191)->nullable()->index();
			$table->boolean('live_status')->nullable()->index();
			$table->dateTime('punching_time')->nullable()->index();
			$table->boolean('created_by')->nullable()->index();
			$table->boolean('updated_by')->nullable()->index();
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
		Schema::drop('ms_sql');
	}
}
