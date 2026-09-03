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

    public function rollBack()
    {
        return $this->connect->rollBack();
    }

    public function getLotesByMaterial(int $id_material, bool $all = false)
    {
        try {
            $sql = "SELECT * FROM lotes WHERE id_material = ?";

            if (!$all) {
                $sql .= " AND quantidade > 0";
            }

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

    public function salvarLote(int $id_material, int $lote, ?string $vencimento, int $quantidade)
    {
        try {
            $sql = "INSERT INTO lotes (id_material, lote, vencimento, quantidade)
            VALUE (?, ?, ?, ?)";

            $stmt = $this->connect->prepare($sql);

            $vencimento = convertNull($vencimento);

            $stmt->bindValue(1, $id_material, PDO::PARAM_INT);
            $stmt->bindValue(2, $lote, PDO::PARAM_INT);
            $stmt->bindValue(3, $vencimento, is_null($vencimento) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(4, $quantidade, PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return $this->connect->lastInsertId();
        } catch (\Throwable $e) {
            $msg = "[ERRO][Lote DAO 04]";
            $msg .= str_contains($e->getMessage(), "Duplicate entry") ? " Número de LOTE já existente! " : $e->getMessage();

            throw new Exception($msg);
        }
    }

    public function editarLote(int $id_lote, int $id_material, int $lote, string $vencimento, int $quantidade)
    {
        try {
            $sql = "UPDATE lotes SET id_material = ?, lote = ?, vencimento = ?, quantidade = ? WHERE id_lote = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_material, PDO::PARAM_INT);
            $stmt->bindValue(2, $lote, PDO::PARAM_INT);
            $stmt->bindValue(3, $vencimento, PDO::PARAM_STR);
            $stmt->bindValue(4, $quantidade, PDO::PARAM_INT);
            $stmt->bindValue(5, $id_lote, PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return "Lote editado com sucesso!";
        } catch (\Throwable $e) {
            $msg = "[ERRO][Lote DAO 05]";
            $msg .= str_contains($e->getMessage(), "Duplicate entry") ? " Número de LOTE já existente! " : $e->getMessage();

            throw new Exception($msg);
        }
    }

    public function excluirLote(int $id_lote)
    {
        try {
            $sql = "DELETE FROM lotes WHERE id_lote = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_lote, PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return "Lote excluído com sucesso!";
        } catch (PDOException $e) {
            $msg = "[ERRO][Lote DAO 06]";
            $msg .= str_contains($e->getMessage(), " Cannot delete or update a parent row: a foreign key constraint fails") ? " Lote não pôde ser apagado, pois há movimentação vinculada! " : $e->getMessage();

            throw new Exception($msg);
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
