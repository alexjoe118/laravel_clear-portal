<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
			$table->string('first_name');
			$table->string('last_name');
			$table->string('title');
            $table->text('address_line_1');
            $table->text('address_line_2')->nullable();
			$table->string('city');
			$table->string('state');
			$table->string('zip_code');
            $table->string('phone_number');
			$table->date('date_of_birth');
			$table->string('ssn_1');
			$table->string('ssn_2');
			$table->string('approximate_credit_score');
			$table->string('signature')->nullable();
			$table->foreignId('business_id')->constrained();
			$table->decimal('business_ownership');
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
        Schema::dropIfExists('partners');
    }
}
