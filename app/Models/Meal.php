<?php

namespace App\Models;

use App\Tables\Tables;
use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Meal extends Model
{
    use HasFactory, Translatable, SoftDeletes;

    protected $appends = ["title", "description"];
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

    public function status(int $diff_time = null)
    {
        $status = "created";
        if ($diff_time == null) {
            return $status;
        }

        $max_timestamp = is_null($this->created_at) ? 0 : Carbon::parse($this->created_at)->timestamp;

        $updated_at_timestamp = is_null($this->updated_at) ? 0 : Carbon::parse($this->updated_at)->timestamp;
        if ($updated_at_timestamp > $max_timestamp) {
            $max_timestamp = $updated_at_timestamp;
            $status = "modified";
        }

        $deleted_at_timestamp = is_null($this->deleted_at) ? 0 : Carbon::parse($this->deleted_at)->timestamp;
        if ($deleted_at_timestamp >= $max_timestamp) {
            $max_timestamp = $deleted_at_timestamp;
            $status = "deleted";
        }

        return $status;
    }

    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(Tag::class, MealTags::class, "meal_id", "id", "id", "tag_id");
    }

    public function ingredients(): HasManyThrough
    {
        return $this->hasManyThrough(Ingredient::class, MealIngredients::class, "meal_id", "id", "id", "ingredient_id");
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, "id", "category_id");
    }

    public function scopeFilterCategory(Builder $query, string|array|null $category_value)
    {
        switch ($category_value) {
            case null: {
                    break;
                }
            case "NULL": {
                    $query->whereNull("category_id");
                    break;
                }
            case "!NULL": {
                    $query->whereNotNull("category_id");
                    break;
                }
            default: {
                    $query->where("category_id", "=", $category_value);
                    break;
                }
        }
    }

    public function scopeFilterTag(Builder $query, string|array|null $tags_query)
    {
        if (is_null($tags_query)) {
            return;
        }
        $tags = explode(",", $tags_query);
        $filter = DB::table(Tables::$MEALS . " AS m")
            ->select("mt.meal_id")
            ->join(Tables::$MEAL_TAGS . " AS mt", "mt.tag_id", "=", "m.id")
            ->whereIn("mt.tag_id", $tags)
            ->groupBy("mt.meal_id")
            ->having(DB::raw("COUNT(mt.meal_id)"), "=", count($tags));
        $query->whereIn("id", $filter);
    }

    public function scopeFilterWith(Builder $query, string|array|null $with)
    {
        if (is_null($with)) {
            return;
        }

        $query->with(explode(",", $with));
    }

    public function scopeFilterDiffTime(Builder $query, string|array|null $diff_time)
    {
        if (is_null($diff_time)) {
            return;
        }

        $timestamp = intval($diff_time);
        if ($timestamp > 0) {
            $date = Carbon::createFromTimestamp($timestamp);
            $query
                ->withTrashed()
                ->where("deleted_at", ">", $date, "or")
                ->where("created_at", ">", $date, "or")
                ->where("updated_at", ">", $date, "or");
        }
    }
}
