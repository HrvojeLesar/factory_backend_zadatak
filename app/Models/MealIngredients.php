<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MealIngredients extends Pivot
{
    use HasFactory;
    protected $table = "meal_ingredients";
}
