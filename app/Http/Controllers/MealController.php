<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Rules\CategoryValidator;
use App\Rules\ListValidator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class MealController extends Controller
{
    private Builder $mealsBuilder;

    public function __construct(private Request $request)
    {
        $this->mealsBuilder = Meal::query();
    }

    public function handle()
    {
        $this->validateRequest();
        // App::setLocale("hr");

        $sql = $this->mealsBuilder->get();
        return response($sql);
    }

    private function validateRequest()
    {
        $this->request->validate([
            "per_page" => ["integer", "numeric", "min:1"],
            "page" => ["integer", "numeric", "min:1"],
            "category" => [new CategoryValidator("abc")],
            "tags" => [new ListValidator],
            "with" => [new ListValidator(["ingredients", "category", "tags"])],
            // TODO: validate based on database
            "lang" => ["required", Rule::in("en", "hr")],
            "diff_time" => ["integer", "numeric"],
        ]);
    }
}
