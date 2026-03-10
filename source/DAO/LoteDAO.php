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
            throw new Exception("[ERRO][Lote DAO 01] " . $th->getMessage(), 1);
        }
    }

    public function getLoteById(int $id_lote)
    {
        try {
            $sql = "SELECT * FROM lotes WHERE id_lote = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_lote, PDO::PARAM_INT);
            $stmt->execute();

            // $stmt->debugDumpParams();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            throw new Exception("[ERRO][Lote DAO 02] " . $th->getMessage(), 1);
        }
    }


    public function atualizarEstoque(int $id_lote, int $quantidade)
    {
        try {
            $sql = "UPDATE lotes SET quantidade = ?
            WHERE id_lote = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, convertNull($quantidade), PDO::PARAM_INT);
            $stmt->bindValue(2, convertNull($id_lote), PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return "Estoque atualizado com sucesso!";
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Lote DAO 03]" . $e->getMessage());
        }
    }
}
