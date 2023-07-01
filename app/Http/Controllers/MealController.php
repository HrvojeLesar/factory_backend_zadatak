<?php

namespace App\Http\Controllers;

use App\Http\Resources\MealResource;
use App\Models\Language;
use App\Models\Meal;
use App\Rules\CategoryValidator;
use App\Rules\ListValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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

    public function __construct(private Request $request)
    {
    }

    public function handle()
    {
        $this->validateRequest();
        App::setLocale($this->request->query(self::$LANG));

        $per_page = $this->request->query(self::$PER_PAGE, 10);
        $page = $this->request->query(self::$PAGE, 1);
        return MealResource::collection(
            Meal::query()
                ->filterCategory($this->request->query(self::$CATEGORY))
                ->filterTag($this->request->query(self::$TAGS))
                ->filterWith($this->request->query(self::$WITH))
                ->filterDiffTime($this->request->query(self::$DIFF_TIME))
                ->paginate($per_page, ["*"], "page", $page)->appends($this->request->query())
        );
    }

    private function validateRequest()
    {
        $languages = Language::all("locale")->pluck("locale")->toArray();
        $this->request->validate([
            self::$PER_PAGE => ["integer", "numeric", "min:1"],
            self::$PAGE => ["integer", "numeric", "min:1"],
            self::$CATEGORY => [new CategoryValidator("abc")],
            self::$TAGS => [new ListValidator],
            self::$WITH => [new ListValidator(["ingredients", "category", "tags"])],
            self::$LANG => ["required", Rule::in($languages)],
            self::$DIFF_TIME => ["integer", "numeric"],
        ]);
    }
}
