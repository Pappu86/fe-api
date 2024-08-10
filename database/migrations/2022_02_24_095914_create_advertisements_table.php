<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained('ads_providers')->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->unique()->constrained('ads_positions')->cascadeOnDelete();
            $table->boolean('status')->default(0)->comment('check, if active or inactive');
            $table->enum('type', ['image', 'video', 'iframe'])->default('image');
            $table->string('title')->nullable();
            $table->boolean('is_modal')->default(0)->nullable();
            $table->boolean('is_auto_modal')->default(0)->nullable();
            $table->boolean('is_external')->default(1)->nullable();
            $table->text('content')->nullable();
            $table->string('link')->nullable();
            $table->boolean('has_mobile_content')->default(0)->nullable();
            $table->text('mobile_content')->nullable();
            $table->string('mobile_link')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('clicks_count')->default(0)->nullable();
            $table->integer('auto_modal_duration')->default(5000)->nullable();
            $table->json('position')->nullable();
            $table->boolean('is_multi_ads')->default(0)->nullable();

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
        Schema::dropIfExists('advertisements');
    }
}