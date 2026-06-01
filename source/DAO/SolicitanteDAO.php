<?php

namespace Source\DAO;

use Exception;
use PDO;
use PDOException;

use Source\Connect;

class SolicitanteDAO
{
    private $connect;

    public function __construct()
    {
        $this->connect = Connect::getInstanceSaagi();
    }


    public function getSolicitante(int $ponto = 0)
    {
        try {
            $sql = "SELECT 
            nome
            FROM usuario
            WHERE visibilidade = 1
            AND ponto LIKE :ponto
            ORDER BY nome ASC";

            $stmt = $this->connect->prepare($sql);

            // $stmt->debugDumpParams();
            $stmt->bindValue(":ponto", "%$ponto%", PDO::PARAM_STR);

            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Solicitante DAO 01]" . $e->getMessage());
        }
    }
}
