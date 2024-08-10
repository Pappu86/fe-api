<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('check, if active or inactive');
            $table->enum('type', ['image', 'video', 'iframe'])->default('image')->comment('slider type');
            $table->boolean('is_external')->default(0)->nullable()->comment('external link');
            $table->text('content')->nullable();
            $table->string('link')->nullable();
            $table->unsignedInteger('ordering')->default(0)->nullable();
            $table->softDeletes();

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
        Schema::dropIfExists('sliders');
    }
}
