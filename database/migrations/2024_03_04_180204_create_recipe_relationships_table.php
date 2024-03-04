<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('recipe_relationships', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_recipe_id');
            $table->unsignedBigInteger('child_recipe_id');
            $table->foreign('parent_recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->foreign('child_recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->primary(['parent_recipe_id', 'child_recipe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('recipe_relationships');
    }
};
