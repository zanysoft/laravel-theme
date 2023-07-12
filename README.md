## Description

[![Laravel](https://img.shields.io/badge/Laravel-5.x-orange.svg?style=flat-square)](http://laravel.com)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)
[![Downloads](https://img.shields.io/packagist/dt/zanysoft/laravel-theme.svg?style=flat-square)](https://packagist.org/packages/zanysoft/laravel-theme)

This is a Laravel package that adds basic support for managing themes. It allows you to build your views & your assets in seperate folders, and supports for theme extending! Awesome :)

Features:

* Views & Asset separation in theme folders
* Theme inheritance: Extend any theme and create Theme hierarchies
* Integrates [Orchestra/Asset](http://orchestraplatform.com/docs/3.0/components/asset) to provide Asset dependencies managment
* Your App & Views remain theme-agnostic. Include new themes with (almost) no modifications
* Themes are distributable! Create a single-file theme Package and install it on any Laravel application.
* Ships with console commands to manage themes

## 1 Installation

This package is very easy to set up. There are only couple of steps.

Install with Composer

```
composer require zanysoft/laravel-theme
```

If you’re on Laravel 5.4 or earlier, you’ll need to add the following to your `config/app.php` (for Laravel 5.5 and up these will be auto-discovered by Laravel):

```php
'providers' => [
    // ...
    ZanySoft\LaravelTheme\ThemeServiceProvider::class,
];

'aliases' => [
    // ...
    'Theme' => ZanySoft\LaravelTheme\Facades\Theme::class,
]
```

Publish the package config file to your application. Run these commands inside your terminal.

php artisan vendor:publish --provider="ZanySoft\LaravelTheme\ThemeServiceProvider"
That's it. You are now ready to start theming your applications!

## 2 Creating a theme

There are two equivalent ways to define a theme. You may pick the one that suits better to you

####A) Configure a theme with a theme.json file

Tip: You should use `php artisan theme:create` command to create a theme. These are the manual steps:

Into the root of your views path create a new folder and then place a theme.json file inside with some configuration:

```json
{
  "name"        : "THEME_NAME",
  "asset-path"  : "ASSET_PATH",
  "extends"     : "PARENT_THEME"
}
```

**Important notes:**

* **THEME_NAME** is the name of your theme. Can be any string.
* **ASSET_PATH** is relative to public path. You should create this folder too!
* **PARENT_THEME** is the name of the parent theme. Set it to null if this is a stand-alone theme
* You can add any additionaly configuration into the json file. You may access to your own settings with Theme:getSetting('key') & Theme:setSetting('key','value') at runtime.
* Why we don't set the views path? Because it is the current path! Just rename current folder and all will work fine.

####B) Configure a theme within the config/themes.php file

You may want to override previous settings (or define a new theme) into your `config/themes.php`. Just add your theme into the themes array. The format for every theme is very simple:

```php
// Select a name for your theme
'themes' => [
    'theme-name' => [

        /*
        |--------------------------------------------------------------------------
        | Theme to extend. Defaults to null (=none)
        |--------------------------------------------------------------------------
        */
        'extends'       => 'theme-to-extend',
  
        /*
        |--------------------------------------------------------------------------
        | The path where the view are stored. Defaults to 'theme-name' 
        | It is relative to 'themes_path' ('/resources/views' by default)
        |--------------------------------------------------------------------------
        */
        'views-path'    => 'path-to-views',
    
        /*
        |--------------------------------------------------------------------------
        | The path where the assets are stored. Defaults to 'theme-name' 
        | It is relative to laravels public folder (/public)
        |--------------------------------------------------------------------------
        */
        'asset-path'    => 'path-to-assets',
  
        /*
        |--------------------------------------------------------------------------
        | Custom configuration. You can add your own custom keys.
        | Use Theme::getSetting('key') & Theme::setSetting('key', 'value') to access them
        |--------------------------------------------------------------------------
        */
        'key'           => 'value', 
    ],
],
```

all settings are optional and can be omitted.

### Implicit Theme decleration

Defining a theme is completely optional.

You may not create a configuration file as long as the defaults fits you! If a theme has not been registered at all then the default values will be used when you activate it.

For example if you `Theme::set('my-theme')` and you haven't created a theme.json file, nor declared 'my-theme' at the `config\themes.php` file, then the default locations will be assumed:

* views = THEMES_ROOT_PATH/my-theme
* assets = public/my-theme

### Custom configuration settings

You can add your own configuration into a theme (either into the theme.json file or into the config/themes.php).

This is an example theme.json with added configuration:

```json
{
  "name"        : "myTheme",
  "asset-path"  : "myTheme",
  "views-path"  : "myTheme",
  "extends"     : null,
  "thumbs-size"  : 150,
  "sidebar-position" : "right"
}
```

You can access these settings at runtime with:

```php
Theme::getSetting('key','default'); // read current theme's configuration value for 'key'
Theme::setSetting('key','value');    // assign a key-value pair to current theme's configuration
```

### Cache Settings

Warning: Theme settings are cached for faster loading times. If you change any setting you should refresh the cache in order to take effect.

Use `php artisan theme:refresh-cache` command for refresh cache. You can disable caching on `config\themes.php`

## 3 Build views

Whenever you need the path of a file (image/css/js etc) with root you can retrieve its path with:

```php
theme_path('path-to-file')

if(file_exists(theme_path('path-to-file')){
    //
}
```

Whenever you need the url of a local file (image/css/js etc) you can retrieve its path with:

```php
theme_url('path-to-file',$absolute) //if absolute true then will return full url other wil return reletive to root.
```

The `path-to-file` should be relative to Theme Folder (NOT to public!). For example, if you have placed an image in `public/theme-name/img/logo.png` your Blade code would be:

```php
<img src="{{theme_url('img/logo.png')}}">
```

When you are referring to a local file it will be looked-up in the current theme hierarchy, and the correct path will be returned. If the file is not found on the current theme or its parents then you can define in the configuration file the action that will be carried out: `THROW_EXCEPTION` | `LOG_ERROR` as warning (Default) | `IGNORE` assumes the file does exist on pubilc folder and returns the path

Some useful helpers you can use:

```php
Theme::path('file-name'); // Equivalent to theme_path('filename')
    Theme::url('file-name'); // Equivalent to theme_url('filename')
    Theme::js('file-name');  // Use with {!! ... !!} syntax
    Theme::css('file-name'); // Use with {!! ... !!} syntax
    Theme::img('src', 'alt', 'class-name', ['attribute' => 'value']);
```

**Fully qualified URLs:**

The generated URLs will be fully qualified. if you need relative to the document root then enter second parameter to false (by default is true):

```php
theme_url('path_to/file.jpg');  // "http://my-domain/theme/path_to/file.jpg"
    theme_url('path_to/file.jpg',false);  // "/theme/path_to/file.jpg"
```

Your domain is retrieved from the `url` setting at the `app.php` config file (by default it will read your `.env` file entry)

**URL queries**

You may include queries in any url function. ie:

```php
Theme::css('theme.css?ver=1.2') // theme-path/theme.css?ver=1.2
```

**Parametric filenames**
You can include any configuration key (see Custom Theme Settings) of the current theme inside any path string using {curly brackets}. For example:

```php
Theme::url('jquery-{version}.js')
```

if there is a "version" key defined in the theme's configuration it will be evaluated and then the filename will be looked-up in the theme hierarchy. (e.g: many commercial themes ship with multiple versions of the main.css for different color-schemes, or you can use language-dependent assets)

## 4. Extending Themes

You can set a theme to extend an other. Check section [How to Create a theme](#2-creating-a-theme) for instructions. A child theme may override parent's theme Assets and Views.


When you are requesting a view/asset that doesn't exist in your active theme, then it will be resolved from it's parent theme. You can easily create variations of your theme by simply overriding your views/themes that are different.

#### Assets

This is example will help you understand the theme hierarcy behaviour:

```php
\public
 +- image1.jpg
 | 
 +- \ThemeA
 |   +- image2.jpg
 |   +- image3.jpg
 | 
 +- \ThemeB   // (Also it Extends ThemeA)
     +- image3.jpg
```

Consider these scenarios:

```php
Theme::Set('ThemeA'); // ThemeA is Active
theme_url('image1.jpg'); // = /image1.jpg
theme_url('image2.jpg'); // = /ThemeA/image2.jpg
theme_url('image3.jpg'); // = /ThemeA/image3.jpg

Theme::Set('ThemeB'); // ThemeB is Active, it extends ThemeA
theme_url('image1.jpg'); // = /image1.jpg
theme_url('image2.jpg'); // = /ThemeA/image2.jpg
theme_url('image3.jpg'); // = /ThemeB/image3.jpg
```

All themes fall back to the default laravel folders if a resource is not found on the theme folders.
So for example you can leave your common libraries (jquery/bootstrap ...) in your `public` folder and use them from all themes.
No need to duplicate common assets for each theme!

#### Views

The same behaviour aplies to your views too. When Laravel renders a view it will browse the active Theme's hierarcy until it will find the correct blade file and render it. This can even be a partial view (requested via `@include` or `@extends`) or a `@component`. All themes will fallback to Laravel default folders (eg `resources\views`).

## 5 Setting the active theme

Working with themes is very straightforward. Use:

```php
Theme::set('theme-name');        // switch to 'theme-name'
Theme::get();                    // retrieve current theme's name
Theme::current();                // retrieve current theme (insance of ZanySoft\LaravelTheme\Theme)
Theme::exists('theme-name');     // Check if 'theme-name' is a registered theme
```

You should set your active theme on each request. A good place for this is a Middleware. You can even define the Theme inside your Controller, before returning your views.

You can optional set a default theme in the `theme.php` configuration file, which will be activated during application bootstraping.

### Using a Middleware to set a theme

A [helper middleware](https://github.com/zanysoft/laravel-theme/blob/master/src/Middleware/setTheme.php) is included out of the box if you want to define a Theme per route. To use it:

First register it in `app\Http\Kernel.php`:

```php
protected $routeMiddleware = [
    // ...
    'setTheme' => \ZanySoft\LaravelTheme\Middleware\setTheme::class,
];
```

Now you can apply the middleware to a route or route-group. Eg:

```php
Route::group(['prefix' => 'admin', 'middleware'=>'setTheme:ADMIN_THEME'], function() {
    // ... Add your routes here 
    // The ADMIN_THEME will be applied.
});
```

### Example: Let the user change the theme

Scenario: You have a theme switcher and when the user clicks on a theme, then he hits a route and the theme is applied on the whole application, for all the subsequent requests.

*Note: This is just a boilerplate to help you get started. You may need to adapt it to your needs...*

**Route:**

```php
Route::get('/change-theme/{theme-name}', 'themeController@changeTheme');
```

**Controller:**

```php
public function changeTheme($themeName)
{
    if(Theme::exists($themeName)){
        Theme::set($themeName)
        session(['theme-name' => $themeName]);
        return redirect()->back();
    }
}
```

**Set the theme on a custom middleware**

First create your middlware. ie: `artisan make:middleware SetThemeFromSession`

Then in the `handle` method of your middleware, read the theme name which is stored in the session:

```php
public function handle($request, Closure $next)
{
    if(session()->has('theme-name')){
        \Theme::set(session('theme-name'));
    }
}
```

Now you need to register your middleware inside the `app\Http\Kernel.php` file. For example if you want to apply your theme to all your routes, then you should add it into the `web` array:

```php
'web' => [
        // ...
        \App\Http\Middleware\SetThemeFromSession::class,
    ],
```

## 6 Console Comands

### theme:list

will show a list of your installed themes.

```
php artisan theme:list
```

Example output:

```sh
+--------------------+--------------------+----------------------+----------------------+
|     Theme Name     |       Extends      |      Views Path      |      Asset Path      |
+--------------------+--------------------+----------------------+----------------------+
| theme1             |                    | theme1-views         | theme1-assets        |
| theme2             | theme1             | theme6               | theme6               |
+--------------------+--------------------+----------------------+----------------------+
```

### theme:create [THEME-NAME]

Will gather some information and create a new theme.

```
php artisan theme:create
```

This is an example:

```shell
php artisan theme:create

 Give theme name:
 > dummyTheme

 Where will views be located [Default='dummyTheme']?:
 > dummyTheme

 Where will assets be located [Default='dummyTheme']?:
 > dummyTheme

 Extends an other theme? (yes/no) [no]:
 > y

 Which one:
  [0 ] Main Theme
  [1 ] Some other Theme
 > 0

Summary:
- Theme name: dummyTheme
- Views Path: laravel-app/resources/themes/dummyTheme
- Asset Path: laravel-app/public/dummyTheme
- Extends Theme: Main Theme

 Create Theme? (yes/no) [yes]:
```

### theme:remove [THEME-NAME]

Will delete theme view & asset folders.

```shell
php artisan theme:remove
```

### theme:package [THEME-NAME]

A distributable theme package will be created.

Theme packages are archives that contain both the Views & Assets folder of a Theme. You may distribute a theme as a package and install it to any laravel application. Theme packages will be stored into `storage/themes` path.

### theme:install

Will install a theme from a theme package. Views & Assets folders will be created for the theme. Theme information will be extracted from the `themes.json` file inside the archive.

```shell
php artisan theme:install

 Select a theme to install::
  [0] theme-admin
  [1] theme-client
 > 0

Theme views installed to path [laravel-app/resources/themes/admin]
Theme assets installed to path [laravel-app/public/admin]
```

### theme:refresh-cache

```shell
php artisan theme:refresh-cache
```

Rebuilds the theme cache. Theme cache stores all themes settings in a single file to reduce filesystem querying. Theme caching can be disabled at `config\themes.php`

