<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index()->comment('language');
            $table->string('title')->nullable()->comment('post title');
            $table->string('slug')->nullable()->unique()->comment('post slug');
            $table->string('meta_title')->nullable()->comment('post seo meta title');
            $table->text('meta_description')->nullable()->comment('post seo meta description');
            $table->text('excerpt')->nullable()->comment('post excerpt');
            $table->longText('content')->nullable()->comment('post content');
            $table->string('short_title')->nullable()->comment('post short title');
            $table->string('shoulder')->nullable()->comment('post shoulder above title');
            $table->string('hanger')->nullable()->comment('post hanger under title');
            $table->text('caption')->nullable()->comment('post image caption');
            $table->string('source')->nullable()->comment('post image source');

            $table->unique(['post_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_translations');
    }
}
