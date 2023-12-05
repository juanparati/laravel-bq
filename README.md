# BigQuery Connection Manager for Laravel

## What is it?

A Laravel library that allows to manage BigQuery connections and to perform queries in an easy way.

This library is a wrapper of the original [Google BigQuery library](https://github.com/googleapis/google-cloud-php-bigquery).

Features:

- Multi-project and multi-credentials.
- Cache adapter for Laravel.
- Abstraction methods.
- Secure inline credentials.


## Installation

    composer require juanparati/laravel-bq

Facade registration (optional):

    'aliases' => [
        ...
        'BQ' => \Juanparati\LaravelBQ\Facades\BigQueryManagerFacade::class,
        ...
    ]


## Configuration

Generate configuration file:

    artisan vendor:publish --provider="Juanparati\LaravelBQ\Providers\BigQueryManagerProvider"


There are two ways of provide the credentials:

1) Defining the service account configuration file as path. Example:
```
    'projects' => [
        'default' => [
            'project_id'  => env('BIGQUERY_DEFAULT_PROJECT_ID'),
            'location'    => env('BIGQUERY_DEFAULT_LOCATION', ''),
            'credentials' => '../../bigquery_service.json'  // Path to service account configuration
        ]
    ],
```

2) Providing the credentials inline. Example:

```
    'projects' => [
        'default' => [
            'project_id'  => env('BIGQUERY_DEFAULT_PROJECT_ID'),
            'location'    => env('BIGQUERY_DEFAULT_LOCATION', ''),
            'credentials' => [           
                  'type': 'service_account',
                  'project_id': 'foobar',
                  'private_key_id': '123456',
                  'private_key': "-----BEGIN PRIVATE KEY-----\nFOOBAR\nFOOBAR=\n-----END PRIVATE KEY-----\n",
                  'client_email': 'bigquery@test.iam.gserviceaccount.com',
                  'client_id': '1234567890',
                  'auth_uri': 'https://accounts.google.com/o/oauth2/auth',
                  'token_uri': 'https://oauth2.googleapis.com/token',
                  'auth_provider_x509_cert_url': 'https://www.googleapis.com/oauth2/v1/certs',
                  'client_x509_cert_url': 'https://www.googleapis.com/robot/v1/metadata/x509/bigquery%40test.iam.gserviceaccount.com'
            ]
        ]
    ],
```

In order to generate secure inline credentials use the artisan command `bigquery:credentials`.

Example:

    artisan bigquery:credentials ../bigquery_service.json --projectId=foo --location=EU

The previous command will generate the inline credentials with the private key encrypted using your project encryption key.


## Usage

### Get BigQuery client for the default project

    BQ::getClient();


### Get BigQery client for another project

    BQ::project('second_project')->getClient();


### Run a query a return the results 
    
    $results = BQ::query('SELECT TRUE as result');

or for another project:

    $results = BQ::project('second_project')->query('SELECT TRUE as result');  // Query another project
