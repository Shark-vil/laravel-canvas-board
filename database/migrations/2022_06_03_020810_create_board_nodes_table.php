<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardNodesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('board_nodes', function (Blueprint $table) {
			$table->id();
			$table->string('uuid')->uuid();
			$table->string('type');
			$table->float('x')->default(0);
			$table->float('y')->default(0);
			$table->float('scaleX')->default(1);
			$table->float('scaleY')->default(1);
			$table->float('width')->default(100);
			$table->float('height')->default(100);
			$table->float('rotation')->default(0);
			$table->json('property')->nullable();
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
		Schema::dropIfExists('board_nodes');
	}
}
