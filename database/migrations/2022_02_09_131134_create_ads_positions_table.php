<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads_positions', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('check, if active or inactive');
            $table->string('page')->nullable();
            $table->string('section')->nullable();
            $table->string('name')->nullable()->unique();
            $table->string('size')->nullable();
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
        Schema::dropIfExists('ads_positions');
    }
}
