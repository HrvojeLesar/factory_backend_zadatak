<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CategoryValidator implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!($this->isInteger($value) && is_numeric($value) && $value > 0) && !($value === "NULL" || $value === "!NULL")) {
            $fail("The :attribute must be a number, NULL or !NULL");
        }
    }

    private function isInteger(mixed $value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT)) {
            return true;
        } else {
            return false;
        }
    }
}
