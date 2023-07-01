<?php

namespace App\Models;

use App\Tables\Tables;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MealTags extends Pivot
{
    // protected $table = Tables::$MEAL_TAGS;
    protected $table = "meal_tags";
}
