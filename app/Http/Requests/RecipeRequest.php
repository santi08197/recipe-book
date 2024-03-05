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
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.gross_amount' => 'required|numeric|min:0',
            'ingredients.*.net_amount' => 'required|numeric|min:0',
            'ingredients.*.unit' => 'required|string|max:255',
            'ingredients.*.unit_price' => 'required|numeric|min:0',
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
            'ingredients.*.name.required' => 'El nombre del ingrediente es obligatorio.',
            'ingredients.*.name.string' => 'El nombre del ingrediente debe ser una cadena de texto.',
            'ingredients.*.name.max' => 'El nombre del ingrediente no debe exceder :max caracteres.',
            'ingredients.*.gross_amount.required' => 'La cantidad bruta del ingrediente es obligatoria.',
            'ingredients.*.gross_amount.numeric' => 'La cantidad bruta del ingrediente debe ser un número.',
            'ingredients.*.gross_amount.min' => 'La cantidad bruta del ingrediente debe ser como mínimo :min.',
            'ingredients.*.net_amount.required' => 'La cantidad neta del ingrediente es obligatoria.',
            'ingredients.*.net_amount.numeric' => 'La cantidad neta del ingrediente debe ser un número.',
            'ingredients.*.net_amount.min' => 'La cantidad neta del ingrediente debe ser como mínimo :min.',
            'ingredients.*.unit.required' => 'La unidad del ingrediente es obligatoria.',
            'ingredients.*.unit.string' => 'La unidad del ingrediente debe ser una cadena de texto.',
            'ingredients.*.unit.max' => 'La unidad del ingrediente no debe exceder :max caracteres.',
            'ingredients.*.unit_price.required' => 'El precio unitario del ingrediente es obligatorio.',
            'ingredients.*.unit_price.numeric' => 'El precio unitario del ingrediente debe ser un número.',
            'ingredients.*.unit_price.min' => 'El precio unitario del ingrediente debe ser como mínimo :min.',
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
