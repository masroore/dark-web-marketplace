<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Route;
use Throwable;

/**
 * Exception for defining which route are you redirected.
 *
 * Class RedirectException
 */
class RedirectException extends Exception
{
    private $route;

    public function __construct(string $message, string $route, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->route = $route;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function flashError(): void
    {
        session()->flash('errormessage', $this->message);
    }
}
