<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateXAndYAndScaleXAndScaleYAndWidthAndHeightAndRotationToBoardNodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('board_nodes', function (Blueprint $table) {
					$table->float('x')->default(0)->nullable()->change();
					$table->float('y')->default(0)->nullable()->change();
					$table->float('scaleX')->default(1)->nullable()->change();
					$table->float('scaleY')->default(1)->nullable()->change();
					$table->float('width')->default(100)->nullable()->change();
					$table->float('height')->default(100)->nullable()->change();
					$table->float('rotation')->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('board_nodes', function (Blueprint $table) {
					$table->float('x')->default(0)->change();
					$table->float('y')->default(0)->change();
					$table->float('scaleX')->default(1)->change();
					$table->float('scaleY')->default(1)->change();
					$table->float('width')->default(100)->change();
					$table->float('height')->default(100)->change();
					$table->float('rotation')->default(0)->change();
        });
    }
}
