<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'oauth/*'],

    'allowed_methods' => ['POST', 'GET', 'OPTIONS', 'PUT', 'DELETE',],

    'allowed_origins' => ['https://dashboard-sinau.unjani.ac.id', 'http://dashboard-sinau.unjani.ac.id', 'https://dash-sinaureg.seculab.space', 'https://dash-sinau.seculab.space', 'http://dash-sinau.seculab.space', 'http://localhost:4200', 'https://dashboard-sinau.unjani.co.id', 'http://dashboard-sinau.unjani.co.id'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Access-Control-Allow-Origin', 'Content-Type', 'Authorization'],

    'exposed_headers' => [],

    'max_age' => 60 * 5 * 1,

    'supports_credentials' => false,

];
