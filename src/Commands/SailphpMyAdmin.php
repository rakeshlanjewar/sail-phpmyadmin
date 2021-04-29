<?php

namespace Rakeshlanjewar\SailPhpmyadmin\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class SailphpMyAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'sail:phpmyadmin';


    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Add phpMyAdmin to sail\'s docker-compose.yml';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking if docker-compose.yml exists...');
        $composeFilePath = base_path('docker-compose.yml');
        if (!file_exists($composeFilePath)) {
            return $this->error('Sorry, docker-compose.yml does not exists');
        }

        $choice = $this->choice('Do you want to backup original docker-compose.yml', [
            'Yes', 'No'
        ], 0);

        if ($choice == 'Yes') {
            $isCopied = $this->backupDockerYml($composeFilePath, base_path('backup-docker-compose.yml'));
            if (!$isCopied) {
                return $this->error('Failed to backup the docker-compose.yml');
            }
            $this->info('backup-docker-compose.yml saved successfully');
        }

        $originalYaml = Yaml::parseFile($composeFilePath);

        $originalYaml['services']['sail-phpmyadmin'] = $this->addServiceToDockerCompose();

        $yaml = Yaml::dump($originalYaml, 5);

        file_put_contents($composeFilePath, $yaml);
        $this->info('phpMyAdmin added successfully to docker-compose.yml');
    }

    private function backupDockerYml(string $sourcePath, string $backupPath): bool
    {
        return copy($sourcePath, $backupPath);
    }

    private function addServiceToDockerCompose(): array
    {
        return [
            "image" => "phpmyadmin:latest",
            "ports" =>  ["8080:80"],
            "environment" => [
                "MYSQL_ROOT_PASSWORD" => "\${DB_PASSWORD}",
                "UPLOAD_LIMIT" => "300M"
            ],
            "links" =>  [
                0 => "mysql:db"
            ],
            "depends_on" =>  [
                0 => "mysql"
            ],
            "networks" => [
                0 => "sail"
            ]
        ];
    }
}
