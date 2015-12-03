<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS
    |--------------------------------------------------------------------------
    |
    | You may opt to force an SSL connection when accessing the application.
    | Supported options are "none", "all", "public", "admin"
    |
    | NOTE: This configuration may be overridden by the Settings module.
    |
    */

    'force_https' => env('FORCE_HTTPS', 'none')
];
