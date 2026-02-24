<?php

namespace Source\DAO;

use Exception;
use PDO;
use PDOException;

use Source\Connect;

class LoteDAO
{
    private $connect;

    public function __construct()
    {
        $this->connect = Connect::getInstance();
    }

    public function beginTransaction()
    {
        return $this->connect->beginTransaction();
    }

    public function commit()
    {
        return $this->connect->commit();
    }

    public function getLotesByMaterial(int $id_material)
    {
        try {
            $sql = "SELECT * FROM lotes WHERE id_material = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_material, PDO::PARAM_INT);
            $stmt->execute();

            // $stmt->debugDumpParams();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw new Exception("[ERRO 01][Lote DAO] " . $th->getMessage(), 1);
        }
    }
}
