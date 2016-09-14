<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameReportbackIdToReportbackItemIdInReactionReportbackitemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reaction_reportbackitem', function($table) {
            $table->renameColumn('reportback_id', 'reportback_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reaction_reportbackitem', function($table) {
            $table->renameColumn('reportback_item_id', 'reportback_id');
        });
    }
}
