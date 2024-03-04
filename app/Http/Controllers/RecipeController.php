<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\RecipeIngredient;


class RecipeController extends Controller
{
       
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
    
    public function addProduct(Request $request){
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
}
