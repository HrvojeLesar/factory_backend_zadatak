<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ListValidator implements ValidationRule
{
    private $allowedKeywords;

    public function __construct($allowedKeywords = null)
    {
        $this->allowedKeywords = $allowedKeywords;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $values = explode(",", $value);
        switch ($this->allowedKeywords) {
            case null: {
                    if (!$this->validateNumeric($values)) {
                        $fail("Must be a list of positive numeric values larger than 0.");
                    }
                    break;
                }
            default: {
                    if (!$this->validateByKeywords($values)) {
                        $fail("Must be a list of keywords (ingredients|category|tags).");
                    }
                    break;
                }
        }
    }

    private function validateNumeric(array $values): bool
    {
        $success = true;
        foreach ($values as $number) {
            if (!$this->isInteger($number) || !is_numeric($number) || $number <= 0) {
                $success = false;
                break;
            }
        }
        return $success;
    }

    private function validateByKeywords(array $values): bool
    {
        $success = true;
        foreach ($values as $val) {
            if (!in_array($val, $this->allowedKeywords)) {
                $success = false;
                break;
            }
        }
        return $success;
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
