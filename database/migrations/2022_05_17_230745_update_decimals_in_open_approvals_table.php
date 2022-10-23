<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDecimalsInOpenApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('open_approvals', function (Blueprint $table) {
			$table->decimal('total_credit_limit', 20, 5)->nullable()->change();
			$table->decimal('interest_rate', 20, 5)->nullable()->change();
			$table->decimal('rate', 20, 5)->nullable()->change();
			$table->decimal('factor_rate', 20, 5)->nullable()->change();
			$table->decimal('draw_fee', 20, 5)->nullable()->change();
			$table->decimal('misc_fees', 20, 5)->nullable()->change();
			$table->decimal('multiplier', 20, 5)->nullable()->change();
			$table->decimal('closing_costs', 20, 5)->nullable()->change();
			$table->decimal('cost_of_capital', 20, 5)->nullable()->change();
			$table->decimal('maximum_amount', 20, 5)->nullable()->change();
			$table->decimal('maximum_advance', 20, 5)->nullable()->change();
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
            $table->decimal('total_credit_limit', 20, 2)->nullable()->change();
			$table->decimal('interest_rate')->nullable()->change();
			$table->decimal('rate')->nullable()->change();
			$table->decimal('factor_rate')->nullable()->change();
			$table->decimal('draw_fee')->nullable()->change();
			$table->decimal('misc_fees')->nullable()->change();
			$table->decimal('multiplier')->nullable()->change();
			$table->decimal('closing_costs', 20, 2)->nullable()->change();
			$table->decimal('cost_of_capital', 20, 2)->nullable()->change();
			$table->decimal('maximum_amount', 20, 2)->nullable()->change();
			$table->decimal('maximum_advance', 20, 2)->nullable()->change();
        });
    }
}
