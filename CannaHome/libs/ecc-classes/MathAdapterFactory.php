<?php

class MathAdapterFactory
{
    private static $forcedAdapter;

    public static function forceAdapter(?MathAdapterInterface $adapter = null): void
    {
        self::$forcedAdapter = $adapter;
    }

    /**
     * @param bool $debug
     *
     * @return null|DebugDecorator|MathAdapterInterface
     */
    public static function getAdapter($debug = false)
    {
        if (self::$forcedAdapter !== null) {
            return self::$forcedAdapter;
        }

        $adapter = null;
        $adapterClass = self::getAdapterClass();

        $adapter = new Gmp();

        return self::wrapAdapter($adapter, (bool) $debug);
    }

    /**
     * @param bool $debug
     *
     * @return DebugDecorator|MathAdapterInterface
     */
    public static function getGmpAdapter($debug = false)
    {
        if (self::canLoad('gmp')) {
            return self::wrapAdapter(new Gmp(), $debug);
        }

        throw new RuntimeException('Please install GMP extension.');
    }

    /**
     * @return string
     */
    private static function getAdapterClass()
    {
        if (self::canLoad('gmp')) {
            return 'Gmp';
        }

        throw new RuntimeException('Please install GMP extension.');
    }

    /**
     * @return bool
     */
    private static function canLoad($target)
    {
        return extension_loaded($target);
    }

    /**
     * @return DebugDecorator|MathAdapterInterface
     */
    private static function wrapAdapter(MathAdapterInterface $adapter, $debug)
    {
        if ($debug === true) {
            return new DebugDecorator($adapter);
        }

        return $adapter;
    }
}
