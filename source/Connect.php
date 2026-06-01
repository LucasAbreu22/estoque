<?php

namespace Source;

use PDO;
use PDOException;

class Connect
{
    private static $instances = [];

    public static function getInstance()
    {
        if (!isset(self::$instances['almox'])) {
            try {
                $config = CONNECT_CONFIG;

                self::$instances['almox'] = new PDO(
                    "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['dbname']}",
                    $config['username'],
                    $config['passwd'],
                    $config['options']
                );
            } catch (PDOException $e) {
                die("Erro de conexão com o banco de dados: " . $e->getMessage());
            }
        }

        return self::$instances['almox'];
    }

    public static function getInstanceSaagi()
    {
        if (!isset(self::$instances['saagi'])) {
            try {
                $config = CONNECT_CONFIG_SAAGI;

                self::$instances['saagi'] = new PDO(
                    "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['dbname']}",
                    $config['username'],
                    $config['passwd'],
                    $config['options']
                );
            } catch (PDOException $e) {
                die("Erro de conexão com o banco de dados: " . $e->getMessage());
            }
        }

        return self::$instances['saagi'];
    }
}
