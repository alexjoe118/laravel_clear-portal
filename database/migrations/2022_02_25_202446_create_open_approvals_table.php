<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_approvals', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id')->constrained();
			$table->foreignId('loan_product_id')->constrained();
			$table->foreignId('loan_request_id')->constrained();
			$table->integer('term_length')->nullable();
			$table->decimal('total_credit_limit', 20, 2)->nullable();
			$table->decimal('interest_rate')->nullable();
			$table->decimal('rate')->nullable();
			$table->decimal('factor_rate')->nullable();
			$table->decimal('draw_fee')->nullable();
			$table->decimal('misc_fees')->nullable();
			$table->decimal('multiplier')->nullable();
			$table->decimal('closing_costs', 20, 2)->nullable();
			$table->decimal('cost_of_capital', 20, 2)->nullable();
			$table->decimal('maximum_amount', 20, 2)->nullable();
			$table->boolean('prepayment_discount')->default(false);
			$table->string('payment_frequency')->nullable();
			$table->integer('number_of_payments')->nullable();
			$table->decimal('maximum_advance', 20, 2)->nullable();
			$table->longText('notes')->nullable();
			$table->date('approval_expires');
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
        Schema::dropIfExists('open_approvals');
    }
}
