<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('check, if active or inactive');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('reporter_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type')->nullable()->comment('key from types table');
            $table->string('image')->nullable()->comment('image');
            $table->string('meta_image')->nullable()->comment('seo meta image');
            $table->unsignedBigInteger('views_count')->default(0)->comment('views count');
            $table->dateTime('datetime')->nullable()->comment('publish date and time of post');
            $table->boolean('is_fb_article')->default(0)->nullable()->comment('facebook instant article');
            $table->boolean('is_edited')->default(0)->nullable()->comment('post editer status');
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
        Schema::dropIfExists('posts');
    }
}