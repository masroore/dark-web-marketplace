<?php

namespace App\Exceptions;

use Exception;

class RequestException extends Exception
{
    public function flashError(): void
    {
        session()->flash('errormessage', $this->message);
    }
}
