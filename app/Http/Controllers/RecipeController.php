<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\RecipeIngredient;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RecipeRequest;


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

    public function addRecipe(RecipeRequest $request){
        $recipeArray = $request->safe()->all();
        try{
			DB::beginTransaction();

            $recipe = new Recipe();
            $recipe->name = $recipeArray['name'];
            
            $recipe->sale_percentage = $recipeArray['sale_percentage'];

            $price = 0;
            $priceAux = 0;
            $sellingPriceChild = 0;
            
            if($recipe->save()){
                foreach($recipeArray['ingredients'] as $ingredient){
                    if(isset($ingredient['childRecipeId'])){
                        $childRecipe = Recipe::find($ingredient['childRecipeId']);
                        $recipe->children()->attach($childRecipe['id']);

                        $price += $childRecipe['price'] * $ingredient['portions'];
                        $sellingPriceChild += $childRecipe['selling_price'] * $ingredient['portions'];
                        
                        continue;
                    }

                    $recipeIngredient = new RecipeIngredient();
                    $recipeIngredient->recipe_id = $recipe->id;
                    $recipeIngredient->ingredient_id = $ingredient['ingredient_id'];
                    $recipeIngredient->gross_amount = $ingredient['gross_amount'];
                    $recipeIngredient->net_amount = $ingredient['net_amount'];
                    $recipeIngredient->save();
                    
                    $unitPrice = Ingredient::where('id', $ingredient['ingredient_id'])->value('unit_price');
                    $price += $ingredient['gross_amount'] * $unitPrice;
                    $priceAux = $price;
                }
            }

            $sellingPrice = $priceAux + ($priceAux * $recipeArray['sale_percentage'] / 100) + $sellingPriceChild;
            
            $recipe->selling_price = $sellingPrice;
            $recipe->price = $price;
            $recipe->save();
            
            DB::commit();
            return response()->json($recipe,201);
        }catch(Exception $e){
			DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }
    }
    
    public function addRecipeIngredient(Request $request, $recipe_id){
        $validated = $request->validate([
            'ingredient_id' => 'required|int|exists:App\Models\Ingredient,id',
            'gross_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
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
        $unitPrice = Ingredient::where('id', $recipeIngredient['ingredient_id'])->value('unit_price');
        $recipe = Recipe::findOrFail($recipe_id);
        $recipe->price += $recipeIngredient['gross_amount'] * $unitPrice;
        $recipe->selling_price = $recipe->price + ($recipe->price * $recipe->sale_percentage / 100);
        return $recipe->save();
    }

    public function addRecipeChlidToRecipe(Request $request,$idParentRecipe){
        
        try{
            $validated = $request->validate([
                'childRecipeId' => 'required|int|exists:App\Models\Recipe,id',
                'portions' => 'required|int|min:0',
            ]);

            $childRecipe = Recipe::findOrFail($validated['childRecipeId']);
            $parentRecipe = Recipe::findOrFail($idParentRecipe);

            $parentRecipe->price += $childRecipe->price * $validated['portions'];  
            $parentRecipe->selling_price += $childRecipe->selling_price * $validated['portions'];
            $parentRecipe->save();  

            return response()->json($parentRecipe,201);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function getProfitability(){
        $mostProfitableRecipe = Recipe::getMostProfitableRecipe();
        $leastProfitableRecipe = Recipe::getLeastProfitableRecipe();
        return response()->json([
            'most_profitable_recipe' => 'La receta mas rentable es la de '.$mostProfitableRecipe['name'].' con una ganancia de '.$mostProfitableRecipe['gain'],
            'least_profitable_recipe' => 'La receta menos rentable es la de '.$leastProfitableRecipe['name'].' con una ganancia de '.$leastProfitableRecipe['gain'],
        ]);
    }
}
