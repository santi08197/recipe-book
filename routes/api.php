<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\IngredientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/recipes', [RecipeController::class, 'getRecipes']);
Route::get('/ingredients', [IngredientController::class, 'getIngredients']);
Route::post('/recipes', [RecipeController::class, 'addRecipe']);
Route::post('/ingredients', [IngredientController::class, 'addIngredients']);
Route::post('/recipes/{recipe_id}/ingredients', [RecipeController::class, 'addRecipeIngredient']);
Route::post('/recipes/{recipe_id}/child', [RecipeController::class, 'addRecipeChlidToRecipe']);