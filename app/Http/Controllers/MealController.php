<?php

namespace App\Http\Controllers;

use App\Http\Requests\MealsRequest;
use App\Http\Resources\MealResource;
use App\Models\Meal;
use Illuminate\Support\Facades\App;

class MealController extends Controller
{
    public function __construct(private MealsRequest $request)
    {
    }

    public function handle()
    {
        App::setLocale($this->request->query(MealsRequest::$LANG));

        $per_page = $this->request->query(MealsRequest::$PER_PAGE, 10);
        $page = $this->request->query(MealsRequest::$PAGE, 1);
        return MealResource::collection(
            Meal::query()
                ->filterCategory($this->request->query(MealsRequest::$CATEGORY))
                ->filterTag($this->request->query(MealsRequest::$TAGS))
                ->filterWith($this->request->query(MealsRequest::$WITH))
                ->filterDiffTime($this->request->query(MealsRequest::$DIFF_TIME))
                ->paginate($per_page, ["*"], "page", $page)->appends($this->request->query())
        );
    }
}
