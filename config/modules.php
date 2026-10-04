<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Module Namespace
    |--------------------------------------------------------------------------
    |
    | The root PSR-4 namespace used for every module. This must match the
    | "Modules\\" entry mapped to the "path" below in the host app's composer.json.
    |
    */

    'namespace' => 'Modules',

    /*
    |--------------------------------------------------------------------------
    | Modules Path
    |--------------------------------------------------------------------------
    */

    'path' => base_path('Modules'),

    /*
    |--------------------------------------------------------------------------
    | Stubs Path
    |--------------------------------------------------------------------------
    |
    | Stub templates used by the "module:make*" generator commands. Publish them
    | (php artisan vendor:publish --tag=modules-stubs) to customize; any stub
    | missing here automatically falls back to the one bundled with the package.
    |
    */

    'stubs' => base_path('stubs/modules'),

    /*
    |--------------------------------------------------------------------------
    | Statuses File
    |--------------------------------------------------------------------------
    |
    | Tracks which modules are enabled or disabled. Modules are enabled by
    | default when no entry exists for them in this file.
    |
    */

    'statuses_file' => base_path('modules_statuses.json'),

    /*
    |--------------------------------------------------------------------------
    | Composer Metadata
    |--------------------------------------------------------------------------
    |
    | Fills the tokens of the "composer" stub when a module is scaffolded. Any
    | value left null falls back to the host app's composer.json: "vendor" to
    | the vendor segment of its package name, and the author details to its
    | first listed author.
    |
    */

    'composer' => [
        'vendor' => 'Ibrahimjml',
        'author' => 'ibrahim Ibrahimjml',
        'email' => 'ibrahim712@gmail.com',
    ],

];
