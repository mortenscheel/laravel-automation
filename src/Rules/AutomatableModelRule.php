<?php

namespace Scheel\Automation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Scheel\Automation\Contracts\Automatable;

class AutomatableModelRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return class_exists($value) &&
            is_a($value, Model::class, true) &&
            is_a($value, Automatable::class, true);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute is not an automatable model.';
    }
}
