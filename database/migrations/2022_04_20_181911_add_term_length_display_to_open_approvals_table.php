<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTermLengthDisplayToOpenApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('open_approvals', function (Blueprint $table) {
            $table->string('term_length_display')->after('term_length')->default('months');
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
            $table->dropColumn('term_length_display');
        });
    }
}
