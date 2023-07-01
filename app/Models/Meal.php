<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory, Translatable;

    protected $appends = ["title", "description", "status"];
    protected $visible = ["id", "title", "description", "status", "category", "tags", "ingredients"];
    public $translatedAttributes = ["title", "description"];

    protected function title(): Attribute
    {
        return new Attribute(fn () => $this->translate()->title);
    }

    protected function description(): Attribute
    {
        return new Attribute(fn () => $this->translate()->description);
    }

    protected function status(): Attribute
    {
        return new Attribute(fn () => "created");
    }
}
