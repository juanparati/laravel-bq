<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Client Auth Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the auth cache connection that gets used.
    |
    | Supported: "apc", "array", "database", "file", "memcached", "redis"
    |
    */
    'cache_store' => 'file',

    /*
    |--------------------------------------------------------------------------
    | Client Options
    |--------------------------------------------------------------------------
    |
    | Here you may configure additional parameters that
    | the underlying BigQueryClient will use.
    |
    | Optional parameters: "authCacheOptions", "authHttpHandler", "httpHandler", "retries", "scopes", "returnInt64AsObject"
    */

    'client_options' => [
        'retries' => 3, // Default
    ],


    /*
    |--------------------------------------------------------------------------
    | Default project
    |--------------------------------------------------------------------------
    |
    | Define the default project.
    |
    */
    'default_project' => env('BIGQUERY_DEFAULT_PROJECT', 'default'),


    /*
    |--------------------------------------------------------------------------
    | BigQuery projects
    |--------------------------------------------------------------------------
    |
    | Define the configuration for multiple projects.
    |
    */
    'projects' => [
        'default' => [
            'project_id'  => env('BIGQUERY_DEFAULT_PROJECT_ID'),
            'location'    => env('BIGQUERY_DEFAULT_LOCATION', ''),
            'credentials' => env('BIGQUERY_DEFAULT_CREDENTIALS_FILE')
        ]
    ],

];
