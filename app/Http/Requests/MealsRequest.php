<?php

namespace App\Http\Requests;

use App\Models\Language;
use App\Rules\CategoryValidator;
use App\Rules\ListValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MealsRequest extends FormRequest
{
    public static string $PER_PAGE = "per_page";
    public static string $PAGE = "page";
    public static string $CATEGORY = "category";
    public static string $TAGS = "tags";
    public static string $WITH = "with";
    public static string $LANG = "lang";
    public static string $DIFF_TIME = "diff_time";

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $languages = Language::all("locale")->pluck("locale")->toArray();
        return [
            self::$PER_PAGE => ["integer", "numeric", "min:1"],
            self::$PAGE => ["integer", "numeric", "min:1"],
            self::$CATEGORY => [new CategoryValidator("abc")],
            self::$TAGS => [new ListValidator()],
            self::$WITH => [new ListValidator(["ingredients", "category", "tags"])],
            self::$LANG => ["required", Rule::in($languages)],
            self::$DIFF_TIME => ["integer", "numeric"],
        ];
    }

    public function messages(): array
    {
        return [
            "required" => "The :attribute query param is required."
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json($validator->errors()));
    }
}
