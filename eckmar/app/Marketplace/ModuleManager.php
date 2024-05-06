<?php

namespace App\Marketplace;

use Module;

class ModuleManager
{
    private static $availableModules = [
        'MultiCurrency',
        'FinalizeEarly',
        'FeaturedProducts',
    ];

    public static function isEnabled($module)
    {
        if (!in_array($module, self::$availableModules)) {
            return false;
        }
        if (!Module::has($module)) {
            return false;
        }
        if (!Module::enabled($module)) {
            return false;
        }

        return true;
    }
}
