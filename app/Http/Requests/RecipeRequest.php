<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
class RecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sale_percentage' => 'required|numeric|min:30|max:50',
            'ingredients.*.ingredient_id' => 'sometimes|int|exists:App\Models\Ingredient,id',
            'ingredients.*.gross_amount' => 'sometimes|numeric|min:0',
            'ingredients.*.net_amount' => 'sometimes|numeric|min:0',
            'ingredients.*.childRecipeId' => 'sometimes|int|exists:App\Models\Recipe,id',
            'ingredients.*.portions' => 'sometimes|int|min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre de la receta es obligatorio.',
            'name.string' => 'El nombre de la receta debe ser una cadena de texto.',
            'name.max' => 'El nombre de la receta no debe exceder :max caracteres.',
            'sale_percentage.required' => 'El porcentaje de venta es obligatorio.',
            'sale_percentage.numeric' => 'El porcentaje de venta debe ser un número.',
            'sale_percentage.min' => 'El porcentaje de venta debe ser como mínimo :min.',
            'sale_percentage.max' => 'El porcentaje de venta no debe exceder :max.',
            'ingredients.*.ingredient_id.int' => 'El id del ingrediente debe ser un número entero.',
            'ingredients.*.ingredient_id.exists' => 'El id del ingrediente debe existir en tabla Ingredients.',
            'ingredients.*.name.string' => 'El nombre del ingrediente debe ser una cadena de texto.',
            'ingredients.*.name.max' => 'El nombre del ingrediente no debe exceder :max caracteres.',
            'ingredients.*.gross_amount.numeric' => 'La cantidad bruta del ingrediente debe ser un número.',
            'ingredients.*.gross_amount.min' => 'La cantidad bruta del ingrediente debe ser como mínimo :min.',
            'ingredients.*.net_amount.numeric' => 'La cantidad neta del ingrediente debe ser un número.',
            'ingredients.*.net_amount.min' => 'La cantidad neta del ingrediente debe ser como mínimo :min.',
            'ingredients.*.childRecipeId.int' => 'El id del receta hija debe ser un número entero.',
            'ingredients.*.childRecipeId.exists' => 'El id de la receta hija debe existir en tabla Recipes.',
            'ingredients.*.portions.int' => 'Las porciones deben ser un número entero.',
            'ingredients.*.portions.min' => 'Las porciones deben ser como mínimo :min.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
