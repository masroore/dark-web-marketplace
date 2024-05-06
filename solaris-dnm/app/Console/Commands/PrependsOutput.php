<?php

namespace App\Console\Commands;

trait PrependsOutput
{
    public function comment($string, $style = null, $verbosity = null): void
    {
        parent::comment($this->prepend($string), $style, $verbosity);
    }

    public function error($string, $style = null, $verbosity = null): void
    {
        parent::error($this->prepend($string), $style, $verbosity);
    }

    public function info($string, $style = null, $verbosity = null): void
    {
        parent::info($this->prepend($string), $style, $verbosity);
    }

    public function warn($string, $style = null, $verbosity = null): void
    {
        parent::warn($this->prepend($string), $style, $verbosity);
    }

    protected function prepend($string)
    {
        if (method_exists($this, 'getPrependString')) {
            return $this->getPrependString($string) . $string;
        }

        return $string;
    }
}
