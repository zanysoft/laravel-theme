<?php
if (!function_exists('themes_path')) {
    function themes_path($filename = null)
    {
        return app()->make('zanysoft.themes')->themes_path($filename);
    }
}

if (!function_exists('theme_path')) {
    function theme_path($path = null, $absolute = true)
    {
        return app()->make('zanysoft.themes')->asset_path($path, $absolute);
    }
}

if (!function_exists('theme_name')) {
    function theme_name()
    {
        $name = app()->make('zanysoft.themes')->get();

        return trim($name);
    }
}

if (!function_exists('theme_url')) {
    function theme_url($url, $absolute = true)
    {
        return app()->make('zanysoft.themes')->url($url, $absolute);
    }
}