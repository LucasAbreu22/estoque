<?php

namespace Source;

use PDO;
use PDOException;

class Connect
{
    private static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            try {
                $config = CONNECT_CONFIG;

                self::$instance = new PDO(
                    "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['dbname']}",
                    $config['username'],
                    $config['passwd'],
                    $config['options']
                );
            } catch (PDOException $e) {
                die("Erro de conexão com o banco de dados: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
