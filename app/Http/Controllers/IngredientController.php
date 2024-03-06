<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;

class IngredientController extends Controller
{

    public function getIngredients(Request $request)
    {
        try{
            $ingredients = Ingredient::all();
            return response()->json($ingredients,200);
        }catch(Exception $e){
            return response("Failed", 500);
        }
    }


    public function addIngredients(Request $request){
        $validated = $request->validate([
            '*.name' => 'required|string',
            '*.unit' => 'required|string',
            '*.unit_price' => 'required|numeric|min:0',
        ]);

        try{
            $ingredients = [];

            foreach($validated as $ingredientData){
                $ingredient = new Ingredient();
                $ingredient->fill($ingredientData);
                $ingredient->save();

                $ingredients[] = $ingredient;
            }

			return response()->json($ingredients,201);
			
		}catch(Exception $e){
			return response()->json([
				'message' => $e->getMessage(),
	        ], 401);
		}
        
    }
}
