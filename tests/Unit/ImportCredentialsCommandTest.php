<?php

namespace Unit;

use Juanparati\LaravelBQ\Providers\BigQueryManagerProvider;
use Orchestra\Testbench\TestCase;

class ImportCredentialsCommandTest extends TestCase
{

    /**
     * Load service providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return string[]
     */
    protected function getPackageProviders($app)
    {
        return [BigQueryManagerProvider::class];
    }


    public function testImportCredentialsCommand()
    {
        $this->artisan(
            'bigquery:credentials',
            [
                'service_file' => __DIR__ . '/../Fixtures/fake_service.json',
                '--projectId'    => 'test_project',
                '--location'     => 'EU'
            ]
        )
            ->expectsOutputToContain("'location' => 'EU'")
            ->assertExitCode(0);
    }

}