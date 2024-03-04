<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
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
}
