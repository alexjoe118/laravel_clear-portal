<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('dba');
			$table->string('address_line_1');
			$table->string('address_line_2')->nullable();
			$table->string('city');
			$table->string('state');
			$table->string('zip_code');
            $table->string('phone_number');
			$table->string('federal_tax_id');
			$table->date('start_date');
			$table->string('website')->nullable();
			$table->string('type_of_entity');
			$table->string('industry');
			$table->string('gross_annual_sales');
			$table->string('monthly_sales_volume');
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
        Schema::dropIfExists('businesses');
    }
}
