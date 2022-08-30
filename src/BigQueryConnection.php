<?php
declare(strict_types=1);

namespace Juanparati\LaravelBQ;

use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Core\Exception\GoogleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Symfony\Component\Cache\Adapter\Psr16Adapter;

class BigQueryConnection
{

    /**
     * BigQuery client.
     *
     * @var BigQueryClient
     */
    protected BigQueryClient $client;


    /**
     * Constructor.
     *
     * @param string $projectId
     * @param string $location
     * @param array|string $credentials
     * @param array $clientOptions
     * @param string $cacheStore
     */
    public function __construct(
        string $projectId,
        string $location,
        array|string $credentials,
        array $clientOptions,
        string $cacheStore = 'file'
    ) {

        $conf = array_merge([
            'projectId' => $projectId,
            'location'  => $location,
            'authCache' => static::prepareCacheStore($cacheStore, $projectId),
        ], $clientOptions);

        if (is_string($credentials))
            $conf['keyFilePath'] = $credentials;
        else {
            $conf['keyFile'] = $credentials;

            if (!Str::startsWith($conf['private_key'], '-----BEGIN PRIVATE KEY'))
                $conf['keyFile']['private_key'] = Crypt::decryptString($conf['keyFile']);
        }

        $this->client = new BigQueryClient($conf);
    }


    /**
     * Obtain the original BigQuery client.
     *
     * @return BigQueryClient
     */
    public function getClient() : BigQueryClient {
        return $this->client;
    }


    /**
     * Run a BigQuery query in a synchronous way.
     * Note: This method is not recommended when you tried to pool a large quantity
     * of rows.
     *
     * @param string $query
     * @param array $params
     * @param array $options
     *
     * @return Collection
     * @throws GoogleException
     */
    public function query(string $query, array $params = [], array $options = []) : Collection
    {
        $qry = $this->getClient()->query($query);

        if (!empty($params))
            $qry->parameters($params);

        $queryResults = $this->getClient()->runQuery($qry, $options);

        while (!$queryResults->isComplete()) {
            usleep(500_000);
            $queryResults->reload();
        }

        foreach ($queryResults->rows() as $row) {
            $data[] = $row;
        }

        return collect($data ?? []);
    }



    /**
     * Prepare cache adapter for the BigQuery authentication cache.
     *
     * @param string $cacheStore
     * @param string $projectId
     * @return Psr16Adapter
     */
    protected static function prepareCacheStore(string $cacheStore, string $projectId): Psr16Adapter
    {
        return new Psr16Adapter(
            Cache::store($cacheStore),
            'bigquery_project_' . $projectId
        );
    }

}