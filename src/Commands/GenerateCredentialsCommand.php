<?php

namespace Juanparati\LaravelBQ\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class GenerateCredentialsCommand extends Command
{

    /**
     * Required service account structure elements.
     */
    protected const STRUCTURE_ELEMENTS = [
        'type',
        'private_key_id',
        'private_key',
        'client_email',
        'client_id',
        'auth_uri',
        'token_uri',
        'auth_provider_x509_cert_url',
        'client_x509_cert_url'
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bigquery:credentials {service_file}       
        {--projectId=        : Project Id used by the credentials (Default: autodetect)}
        {--location=US       : Default project location (Default US)}
        {--J|--asJSON        : Print as JSON}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate embedded/secure credentials for the BigQuery configuration.';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $filePath = $this->argument('service_file');
        $filePath = $filePath[0] === '~' ? str_replace('~', getenv('HOME'), $filePath) : $filePath;
        $filePath = realpath($filePath);

        if (!file_exists($filePath)) {
            $this->error('Service file not found.');
            return -1;
        }

        $config = json_decode(file_get_contents($filePath), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->error('Service file is malformed or is not a valid JSON file.');
            return -1;
        }

        if (!$this->checkFileStructure($config))
            return -1;

        $config['private_key'] = Crypt::encryptString($config['private_key']);


        $project = $this->option('projectId') ?: $config['project_id'];

        $output = [
            $project => [
                'location'    => $this->option('location') ?: 'US',
                'project_id'  => $project,
                'credentials' => $config
            ]
        ];

        $this->info('Copy the following snippet into the BigQuery project configuration:');
        $this->line('------');
        $this->newLine();

        if ($this->option('asJSON')) {
            $this->line(json_encode($output, JSON_PRETTY_PRINT));
        } else {
            $this->line(var_export($output, true) . ',');
        }

        $this->newLine();
        $this->line('------');

        return 0;
    }


    /**
     * Check if the service account has the required elements.
     *
     * @param array $structure
     * @return bool
     */
    protected function checkFileStructure(array $structure): bool
    {
        foreach (static::STRUCTURE_ELEMENTS as $element) {
            if (empty($structure[$element])) {
                $this->error('Service account file requires ' . $element);
                return false;
            }
        }

        return true;
    }

}