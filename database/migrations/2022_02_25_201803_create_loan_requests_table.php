<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id')->constrained();
			$table->foreignId('loan_product_id')->constrained();
			$table->decimal('requested_amount', 20, 2);
			$table->string('funds_needed_estimate');
			$table->string('funds_usage');
			$table->string('communication_channel');
			$table->json('documents')->nullable();
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
        Schema::dropIfExists('loan_requests');
    }
}
