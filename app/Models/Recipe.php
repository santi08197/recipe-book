<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Recipe extends Model
{
    use HasFactory;

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')->as('amounts')
                    ->withPivot('gross_amount', 'net_amount');
    }

    public function parents()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_relationships', 'child_recipe_id', 'parent_recipe_id');
    }

    public function children()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_relationships', 'parent_recipe_id', 'child_recipe_id');
    }

    public static function getMostProfitableRecipe()
    {
        return SELF::select('id', 'name', 'selling_price', 'price')
            ->addSelect(DB::raw('selling_price - price AS gain'))
            ->orderBy('gain', 'desc')
            ->first();
    }
    
    public static function getLeastProfitableRecipe()
    {
        return SELF::select('id', 'name', 'selling_price', 'price')
            ->addSelect(DB::raw('selling_price - price AS gain'))
            ->orderBy('gain', 'asc')
            ->first();
    }
}
