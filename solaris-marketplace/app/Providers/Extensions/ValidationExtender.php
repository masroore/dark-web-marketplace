<?php

namespace App\Providers\Extensions;

use Illuminate\Support\Str;

class ValidationExtender implements Extender
{
    /**
     * Custom rule list.
     *
     * @var array
     */
    protected $rules = ['pgusers'];

    /**
     * Extends out-of-box laravel's functionality.
     */
    public function extend(): void
    {
        foreach ($this->rules as $rule) {
            /** @var \App\Http\Rules\Rule $handler */
            $handler = app('\App\Http\Rules\\' . Str::studly($rule));

            \Illuminate\Support\Facades\Validator::extend(
                $rule,
                fn ($field, $value, $params) => $handler->validate($field, $value, $params)
            );

            \Illuminate\Support\Facades\Validator::replacer(
                $rule,
                fn ($message, $field, $rulename, $params) => $handler->message($message, $field, $params)
            );
        }
    }
}
