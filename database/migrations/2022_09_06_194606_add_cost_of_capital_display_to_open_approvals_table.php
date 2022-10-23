<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostOfCapitalDisplayToOpenApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('open_approvals', function (Blueprint $table) {
            $table->boolean('cost_of_capital_display')->default(true)->after('cost_of_capital');
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
            $table->dropColumn('cost_of_capital_display');
        });
    }
}
