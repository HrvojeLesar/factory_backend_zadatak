<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Language;
use App\Models\Meal;
use App\Models\MealIngredients;
use App\Models\MealTags;
use App\Models\Tag;
use App\Rules\CategoryValidator;
use App\Rules\ListValidator;
use App\Tables\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MealController extends Controller
{
    private static string $PER_PAGE = "per_page";
    private static string $PAGE = "page";
    private static string $CATEGORY = "category";
    private static string $TAGS = "tags";
    private static string $WITH = "with";
    private static string $LANG = "lang";
    private static string $DIFF_TIME = "diff_time";

    private Builder $mealsBuilder;
    private LengthAwarePaginator $paginator;

    public function __construct(private Request $request)
    {
        $this->mealsBuilder = Meal::query();
    }

    public function add()
    {
        $category = new Category();
        $category->translateOrNew("en")->title = "Category";
        $category->translateOrNew("hr")->title = "Kategorija";
        $category->save();

        $tag = new Tag();
        $tag->translateOrNew("en")->title = "Tag";
        $tag->translateOrNew("hr")->title = "Hrvatski Tag";
        $tag->save();

        $ing = new Ingredient();
        $ing->translateOrNew("en")->title = "Ingredient";
        $ing->translateOrNew("hr")->title = "Sastojak";
        $ing->save();

        $meal = new Meal();
        $meal->translateOrNew("en")->title = "Meal";
        $meal->translateOrNew("hr")->title = "Jelo";
        $meal->translateOrNew("en")->description = "Very nice";
        $meal->translateOrNew("hr")->description = "Fino jelo";
        $meal->category_id = $category->id;
        $meal->save();

        $mealTag = new MealTags();
        $mealTag->meal_id = $meal->id;
        $mealTag->tag_id = $tag->id;
        $mealTag->save();

        $mealIng = new MealIngredients();
        $mealIng->meal_id = $meal->id;
        $mealIng->ingredient_id = $ing->id;
        $mealIng->save();
    }

    public function handle()
    {
        $this->validateRequest();
        $this->filter();
        $this->with();
        $this->paginate();

        App::setLocale($this->request->query(self::$LANG));
        return response()->json([
            "meta" => [
                "currentPage" => $this->paginator->currentPage(),
                "totalItems" => $this->paginator->total(),
                "itemsPerPage" => $this->paginator->perPage(),
                "totalPages" => $this->paginator->lastPage(),
            ],
            "data" => $this->mealsBuilder->get(),
            "links" => [
                // TODO: append other url params
                "prev" => $this->paginator->previousPageUrl(),
                // TODO: append other url params
                "next" => $this->paginator->nextPageUrl(),
                // TODO: append other url params
                "self" => $this->paginator->url($this->paginator->currentPage()),
            ]
        ]);
    }

    private function validateRequest()
    {
        $this->request->validate([
            self::$PER_PAGE => ["integer", "numeric", "min:1"],
            self::$PAGE => ["integer", "numeric", "min:1"],
            self::$CATEGORY => [new CategoryValidator("abc")],
            self::$TAGS => [new ListValidator],
            self::$WITH => [new ListValidator(["ingredients", "category", "tags"])],
            // TODO: validate based on database
            // dd(Language::all("locale"));
            self::$LANG => ["required", Rule::in("en", "hr")],
            self::$DIFF_TIME => ["integer", "numeric"],
        ]);
    }

    private function filter()
    {
        $this->filterCategory();
        $this->filterTag();
    }

    private function filterCategory()
    {
        $category_value = $this->request->query(self::$CATEGORY);
        if ($category_value == null) {
            return;
        } else if ($category_value === "NULL") {
            $this->mealsBuilder = $this->mealsBuilder->where("category_id", "=", null);
        } else if ($category_value === "!NULL") {
            $this->mealsBuilder = $this->mealsBuilder->where("category_id", "!=", null);
        } else {
            $this->mealsBuilder = $this->mealsBuilder->where("category_id", "=", $category_value);
        }
    }

    private function filterTag()
    {
        $tags_query = $this->request->query(self::$TAGS);
        if ($tags_query == null) {
            return;
        }

        $tags = explode(",", $tags_query);
        $filter = DB::table(Tables::$MEALS . " AS m")
            ->select("mt.meal_id")
            ->join(Tables::$MEAL_TAGS . " AS mt", "mt.tag_id", "=", "m.id")
            ->whereIn("mt.tag_id", $tags)
            ->groupBy("mt.meal_id")
            ->having(DB::raw("COUNT(mt.meal_id)"), "=", count($tags));
        $this->mealsBuilder = $this->mealsBuilder->whereIn("id", $filter);
    }

    private function with()
    {
        $with = $this->request->query("with");
        if ($with != null) {
            $this->mealsBuilder = $this->mealsBuilder->with(explode(",", $with));
        }
    }

    private function paginate()
    {
        $per_page = $this->request->query("per_page", 10);
        $page = $this->request->query("page", 1);
        $this->paginator = $this->mealsBuilder->paginate($per_page, ["*"], "page", $page);
    }
}
