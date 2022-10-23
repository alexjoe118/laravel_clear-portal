<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id')->constrained();
			$table->foreignId('loan_request_id')->constrained();
			$table->foreignId('open_approval_id')->constrained();
			$table->foreignId('loan_product_id')->constrained();
			$table->foreignId('lender_id')->nullable()->constrained();
			$table->decimal('loan_amount', 20, 2)->nullable();
			$table->decimal('credit_limit', 20, 2)->nullable();
			$table->decimal('payback_amount', 20, 2)->nullable();
			$table->decimal('payment_amount', 20, 2)->nullable();
			$table->string('payment_frequency')->nullable();
			$table->string('payment_day')->nullable();
			$table->integer('number_of_payments')->nullable();
			$table->integer('term_length')->nullable();
			$table->date('payoff_date')->nullable();
			$table->date('estimated_renewal_date')->nullable();
			$table->date('estimated_payoff_date')->nullable();
			$table->boolean('funded')->default(false);
			$table->date('funded_date')->nullable();
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
        Schema::dropIfExists('loans');
    }
}
