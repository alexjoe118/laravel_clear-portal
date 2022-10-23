<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->integer('customer_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
			$table->string('title')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
			$table->string('city')->nullable();
			$table->string('state')->nullable();
			$table->string('zip_code')->nullable();
            $table->string('phone_number')->nullable();
			$table->date('date_of_birth')->nullable();
			$table->string('ssn_1')->nullable();
			$table->string('ssn_2')->nullable();
			$table->string('approximate_credit_score')->nullable();
			$table->string('signature')->nullable();
            $table->string('photo')->nullable();
			$table->foreignId('business_id')->nullable()->constrained();
			$table->decimal('business_ownership')->nullable();
			$table->foreignId('advisor_id')->nullable()->constrained('users');
            $table->string('role')->default('user');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
