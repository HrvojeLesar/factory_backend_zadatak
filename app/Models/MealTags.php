<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MealTags extends Pivot
{
    use HasFactory;
    protected $table = "meal_tags";
}
