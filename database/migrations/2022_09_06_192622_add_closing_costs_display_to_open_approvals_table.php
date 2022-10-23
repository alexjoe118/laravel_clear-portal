<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClosingCostsDisplayToOpenApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('open_approvals', function (Blueprint $table) {
            $table->string('closing_costs_display')->after('closing_costs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('open_approvals', function (Blueprint $table) {
            $table->dropColumn('closing_costs_display');
        });
    }
}
