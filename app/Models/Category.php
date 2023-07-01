<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, Translatable;

    public $translatedAttributes = ["title"];
    protected $appends = ["title", "slug"];
    protected $visible = ["id", "title", "slug"];

    protected function title(): Attribute
    {
        return new Attribute(fn () => $this->translate()->title);
    }

    protected function slug(): Attribute
    {
        return new Attribute(fn () => "category-" . $this->id);
    }
}
