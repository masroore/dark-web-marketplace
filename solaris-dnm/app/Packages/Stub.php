<?php
/**
 * File: Stub.php
 * This file is part of MM2-dev project.
 * Do not modify if you do not know what to do.
 */

namespace App\Packages;

use Exception;

class Stub
{
    protected $instance;

    public function __construct($model, array $attributes = [])
    {
        $model = '\\App\\' . $model;
        if (!class_exists($model)) {
            throw new Exception('Model ' . $model . ' is not found.');
        }

        $this->instance = new $model($attributes);
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        } elseif (method_exists($this->instance, $name)) {
            return call_user_func_array([$this->instance, $name], $arguments);
        }

        throw new Exception('Method ' . $name . ' is not found at ' . get_class($this->instance));

    }

    public function __get($name)
    {
        return $this->instance->{$name};
    }

    public function __set($name, $value): void
    {
        $this->instance->{$name} = $value;
    }

    public function push(): void
    {
        throw new Exception('Changes in stubs are not allowed.');
    }

    public function save(array $options = []): void
    {
        throw new Exception('Changes in stubs are not allowed.');
    }

    public function saveOrFail(array $options = []): void
    {
        throw new Exception('Changes in stubs are not allowed.');
    }

    public function touch(): void
    {
        throw new Exception('Changes in stubs are not allowed.');
    }

    public function original()
    {
        return $this->instance;
    }
}
