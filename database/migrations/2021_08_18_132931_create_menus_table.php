<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('check, if active or inactive');
            $table->string('title')->nullable();
            $table->string('icon')->nullable();
            $table->string('link')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedInteger('ordering')->default(0);
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
        Schema::dropIfExists('menus');
    }
}
