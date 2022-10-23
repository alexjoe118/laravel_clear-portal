<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();
			$table->foreignId('loan_group_id')->nullable()->constrained();
			$table->string('title');
			$table->string('slug')->unique();
			$table->text('description')->nullable();
			$table->json('props')->nullable();
			$table->string('learn_more')->nullable();
			$table->json('required_document_types')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_products');
    }
}
