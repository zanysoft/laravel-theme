<?php

namespace ZanySoft\LaravelTheme\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Get the registered name of the component.
 *
 * @method static \ZanySoft\LaravelTheme\Theme set($themeName) Enable $themeName & set view paths
 * @method static \ZanySoft\LaravelTheme\Theme add(\ZanySoft\LaravelTheme\Theme $theme) Register a new theme
 * @method static \ZanySoft\LaravelTheme\Theme find($themeName) Find a theme by it's name
 * @method static \ZanySoft\LaravelTheme\Theme current()
 * @method static boolean exists($themeName) Check if @themeName is registered
 * @method static array all() Return list of registered themes
 * @method static string url($filename, $absolute = true) Return url of current theme
 * @method static string css($href) Return css <link/> link tage
 * @method static string js($href) Return js <script/> tag
 *
 **/
class Theme extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'zanysoft.themes';
    }
}
