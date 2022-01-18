<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeatureFlagsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_flag_user', function (Blueprint $table) {
            $table->increments('id');

            $table->string('user_id')->index();

            $table->string('feature_flag_name')->index();
            $table->foreign('feature_flag_name')->references('name')->on('feature_flags')->onDelete('cascade');

            $table->unique(['user_id','feature_flag_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feature_flag_user');
    }
}
