<?php

namespace Juanparati\LaravelBQ;

/**
 * Manage the BigQuery connections.
 */
class BigQueryManager
{

    /**
     * List of BigQuery clients.
     *
     * @var BigQueryConnection[]
     */
    protected array $connections = [];


    /**
     * Constructor.
     *
     * @param array $conf
     */
    public function __construct(private array $conf) {}


    /**
     * Get a connection.
     *
     * @param string|null $project
     * @return BigQueryConnection
     */
    public function project(?string $project = null) : BigQueryConnection {

        $project = $project ?: $this->conf['default_project'];

        if (isset($this->connections[$project]))
            return $this->connections[$project];

        $bqProjectConf = $this->conf['projects'][$project];

        return $this->connections[$project] = new BigQueryConnection(
            $bqProjectConf['project_id'],
            $bqProjectConf['location'] ?: '',
            $bqProjectConf['credentials'],
            $this->conf['client_options'] ?: [],
            $this->conf['cache_store'] ?: 'file',
        );
    }


    /**
     * Call the default connection methods.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments = []) : mixed
    {
        $connection = $this->project();
        return call_user_func_array([$connection, $name], $arguments);
    }
}