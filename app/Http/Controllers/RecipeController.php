<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\RecipeIngredient;


class RecipeController extends Controller
{
    public function getRecipes(Request $request){
        
        $validSorts = ['id', 'name', 'price', 'created_at', 'updated_at'];

        $sort = $request->input('sort');
        if ($sort && !in_array($sort, $validSorts)) {
            return response()->json(['error' => 'Invalid sort parameter'], 400);
        }

        $recipe = Recipe::with('ingredients')
            ->when($sort, function ($query, $sort) {
                $direction = substr($sort, 0, 1) === '-' ? 'desc' : 'asc';
                $field = ltrim($sort, '-');
                return $query->orderBy($field, $direction);
            })
            ->get();

        return $recipe;
    }

    public function addRecipe(Request $request){
        $recipeArray = $request->input('recipe');

        $price = $this->getPrice($recipeArray['ingredients']);
        $recipe = new Recipe();
        $recipe->name = $recipeArray['name'];
        $recipe->price = $price;


        if($recipe->save()){
            foreach($recipeArray['ingredients'] as $ingredientArray){

                if(isset($ingredientArray['childRecipeId'])){
                    $childRecipe = Recipe::find($ingredientArray['childRecipeId']);
                    $recipe->children()->attach($childRecipe['id']);
                    continue;
                }
                
                $ingredient = new Ingredient();
                $ingredient->name = $ingredientArray['name'];
                $ingredient->unit_price = $ingredientArray['unit_price'];
                $ingredient->unit = $ingredientArray['unit'];
                $ingredient->save();

                $recipeIngredient = new RecipeIngredient();
                $recipeIngredient->recipe_id = $recipe->id;
                $recipeIngredient->ingredient_id = $ingredient->id;
                $recipeIngredient->gross_amount = $ingredientArray['gross_amount'];
                $recipeIngredient->net_amount = $ingredientArray['net_amount'];
                $recipeIngredient->save();
            }
        }
		return response()->json($recipe,201);
    }

    protected function getPrice($ingredients){
        $price = 0;
        foreach($ingredients as $ingredient){
            if(isset($ingredient['childRecipeId'])){
                $childRecipe = Recipe::find($ingredient['childRecipeId']);
                $price += $childRecipe['price'] * $ingredient['portions'];
                continue;
            }
            $price += $ingredient['gross_amount'] * $ingredient['unit_price']; 
        }
        return $price;
    }
    
    public function addIngredient(Request $request){
        $validated = $request->validate([
            'name' => 'required|string',
            'unit' => 'required|string',
            'unit_price' => 'required|numeric',
        ]);

        try{
            $igredient = new Ingredient();
			$igredient->fill($validated);
			$igredient->save();

			return response()->json($igredient,201);
			
			
		}catch(Exception $e){
			return response()->json([
				'message' => $e->getMessage(),
	        ], 401);
		}
        
    }

    public function addRecipeIngredient(Request $request, $recipe_id){
        $validated = $request->validate([
            'ingredient_id' => 'required|int',
            'gross_amount' => 'required|numeric',
            'net_amount' => 'required|numeric',
        ]);

        try{
            $recipeIngredient = new RecipeIngredient();
			$recipeIngredient->fill($validated);
			$recipeIngredient->recipe_id = $recipe_id;
            if($recipeIngredient->save()){
                $this->updateRecipePrice($recipe_id, $validated);
            }

			return response()->json($recipeIngredient,201);
			
		}catch(Exception $e){
			return response()->json([
				'message' => $e->getMessage(),
	        ], 401);
		}
        
    }
    
    protected function updateRecipePrice($recipe_id, $recipeIngredient){
        $ingredient = Ingredient::find($recipeIngredient['ingredient_id']);
        $recipe = Recipe::findOrFail($recipe_id);
        $recipe->price += $recipeIngredient['gross_amount'] * $ingredient->unit_price;
        return $recipe->save();
    }
}
