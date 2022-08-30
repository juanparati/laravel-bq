<?php

namespace Unit;

use Illuminate\Foundation\AliasLoader;
use Juanparati\LaravelBQ\BigQueryManager;
use Juanparati\LaravelBQ\Facades\BigQueryManagerFacade;
use Juanparati\LaravelBQ\Providers\BigQueryManagerProvider;
use Orchestra\Testbench\TestCase;

class BigQueryConnectionTest extends TestCase
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



    /**
     * Prepare the environment and configuration.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app) {

        $app['config']->set('bigquery.projects', [
            'default' => [
                'project_id'  => '',
                'location'    => 'US',
                'credentials' => __DIR__ . '/../.secrets/service.json'
            ],
            'secondary' => [
                'project_id'  => '',
                'location'    => 'EU',
                'credentials' => __DIR__ . '/../.secrets/service.json'
            ]
        ]);

        $loader = AliasLoader::getInstance();
        $loader->alias('BQ', BigQueryManagerFacade::class);
    }


    /**
     * Test the default connection.
     *
     * @return void
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    public function testDefaultConnection() {
        $bq = $this->app->make(BigQueryManager::class);
        $res = $bq->query('SELECT TRUE as result');
        $this->assertTrue($res->value('result'));
    }


    /**
     * Test the secondary connection.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testSecondaryConnection() {
        $bq = $this->app->make(BigQueryManager::class);
        $res = $bq->project('secondary')->query('SELECT TRUE as result');
        $this->assertTrue($res->value('result'));
    }



    /**
     * Test the default connection using the facade.
     *
     * @return void
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    public function testDefaultFacadeConnection() {
        $res = \BQ::query('SELECT TRUE as result');
        $this->assertTrue($res->value('result'));
    }


    public function testSecondaryFacadeConnection() {
        $res = \BQ::project('secondary')->query('SELECT TRUE as result');
        $this->assertTrue($res->value('result'));
    }

}