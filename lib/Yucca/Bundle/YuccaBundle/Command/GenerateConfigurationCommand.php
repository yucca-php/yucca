<?php
/*
 * This file was delivered to you as part of the Yucca package.
 *
 * (c) Rémi JANOT <r.janot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yucca\Bundle\YuccaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateConfigurationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('yucca:generate-configuration')
            ->setDescription('Generates configuration')
            ->addArgument('path', InputArgument::REQUIRED, 'Path into wich put config')
            ->addArgument('namespace', InputArgument::REQUIRED, 'Model namespace')
            ->addArgument('dbname', InputArgument::REQUIRED, 'Database name')
            ->addArgument('user', InputArgument::OPTIONAL, 'Database user','root')
            ->addArgument('password', InputArgument::OPTIONAL, 'Database password','')
            ->addArgument('host', InputArgument::OPTIONAL, 'Database Host', 'localhost')
            ->addArgument('driver', InputArgument::OPTIONAL, 'Database driver', 'pdo_mysql')
            ->addArgument('port', InputArgument::OPTIONAL, 'Database port', '');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Get File Path
        $realPath = array(
            rtrim($input->getArgument('path'),DIRECTORY_SEPARATOR)
        );
        $realPath = implode('/',$realPath).'/yucca.yml';
        if(file_exists($realPath)) {
            throw new \Exception("File $realPath already exists");
        }

        $connectionOptions = array(
            'driver' => $input->getArgument('driver'),
            'host' => $input->getArgument('host'),
            'port' => $input->getArgument('port'),
            'dbname' => $input->getArgument('dbname'),
            'user' => $input->getArgument('user'),
            'password' => $input->getArgument('password'),
            'charset' => 'UTF8'
        );
        $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionOptions);
        $config = $this->getConnectionConfiguration($connectionOptions);
        $config .= $this->getSchema($connection);
        $config .= $this->getSources($connection);
        $config .= $this->getMapping($connection, $input->getArgument('namespace'));
        $config .= $this->getSelectors($connection, $input->getArgument('namespace'));

        file_put_contents($realPath, $config);
    }

    protected function getConnectionConfiguration(array $connectionOptions) {
return <<<EOT
#Yucca configuration
yucca:
    #DB connections
    connections:
        memcache_general:
            type: memcache
            options:
                servers:
                    - {port: %memcache_memcache_general_port%, host: %memcache_memcache_general_host%}
        default:
            type: doctrine
            options:
                driver:   {$connectionOptions['driver']}
                host:     {$connectionOptions['host']}
                port:     {$connectionOptions['port']}
                dbname:   {$connectionOptions['dbname']}
                user:     {$connectionOptions['user']}
                password: {$connectionOptions['password']}
                charset:  UTF8



EOT;
    }

    protected function getSchema(\Doctrine\DBAL\Connection $connection) {
        $tables = array();
        foreach($connection->fetchAll('SHOW TABLES') as $table) {
            $table = current($table);
            $tables [] = <<<EOT
        $table:
            sharding_strategy: modulo
            shards:
                - default
EOT;
        }

        $tables  = implode("\n",$tables);
        return <<<EOT
    #DB Schema
    schema:
$tables




EOT;
    }

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @return string
     */
    protected function getSources(\Doctrine\DBAL\Connection $connection) {
        $tables = array();
        foreach($connection->fetchAll('SHOW TABLES') as $table) {
            $table = current($table);
            $fields = array();
            foreach($connection->fetchAll('DESC '.$table) as $field) {
                if('date' == strtolower($field['Type'])) {
                    $fields[] = $field['Field'].': {type: \'date\'}';
                } elseif('datetime' == strtolower($field['Type'])) {
                    $fields[] = $field['Field'].': {type: \'datetime\'}';
                } elseif('pri' == strtolower($field['Key'])) {
                    $fields[] = $field['Field'].': {type: \'identifier\'}';
                } else {
                    $fields[] = $field['Field'].': ~';
                }
            }
            $fields = implode(', ',$fields);
            $tables [] = <<<EOT
        $table:
            default_params:
                fields: { $fields }
            handlers:
                -
                    type: memcache
                    connection_name: memcache_general
                -
                    type: database_single_row
                    table_name: $table
EOT;
        }

        $tables  = implode("\n",$tables);
        return <<<EOT
    #Sources
    sources:
$tables




EOT;
    }

    /**
     * @param $string
     * @param bool $ucFirst
     * @return string
     */
    protected function underscoreToCamelcase($string, $ucFirst) {

        $parts = explode('_', $string);
        $parts = $parts ? array_map('strtolower', $parts) : array($string);
        $parts = $parts ? array_map('ucfirst', $parts) : array($string);
        $parts[0] = $ucFirst ? ucfirst($parts[0]) : lcfirst($parts[0]);
        return implode('', $parts);
    }

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @param $namespace
     * @return string
     */
    protected function getMapping(\Doctrine\DBAL\Connection $connection, $namespace) {
        $models = array();
        foreach($connection->fetchAll('SHOW TABLES') as $table) {
            $table = current($table);
            $mappings = array();
            foreach($connection->fetchAll('DESC '.$table) as $field) {
                if(false !== strpos($field['Field'], '_id')) {
                    $fieldName = $field['Field'];
                    $propertyName = str_replace('_id','',$field['Field']);
                    $mappings[] = <<<EOT
                $propertyName:
                    field: $fieldName
EOT;

                }
            }
            $mappings = implode("\n",$mappings);
            $modelName = $namespace.'\\Model\\'.$this->underscoreToCamelcase($table,true);
            $models [] = <<<EOT
        $modelName:
            mapper_class_name: ~
            properties:
$mappings
            sources:
                - $table
EOT;
        }

        $models  = implode("\n",$models);
        return <<<EOT
    #Sources
    mapping:
$models




EOT;
    }

    protected function getSelectors(\Doctrine\DBAL\Connection $connection, $namespace) {
        $tables = array();
        foreach($connection->fetchAll('SHOW TABLES') as $table) {
            $table = current($table);
            $selectorName = $namespace.'\\Selector\\'.$this->underscoreToCamelcase($table,true);
            $tables [] = <<<EOT
        $selectorName:
            sources:
                - database
EOT;
        }

        $tables  = implode("\n",$tables);
        return <<<EOT
    #Selectors
    selectors:
$tables




EOT;
    }
}
