<?php

namespace App\Models;

use App\Tables\Tables;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MealIngredients extends Pivot
{
    // protected $table = Tables::$MEAL_INGREDIENTS;
    protected $table = "meal_ingredients";
}
