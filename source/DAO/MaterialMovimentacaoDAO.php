<?php

namespace Source\DAO;

use Exception;
use PDO;
use PDOException;

use Source\Connect;

class MaterialMovimentacaoDAO
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

    public function salvarMaterialMov(int $id_movimentacao, int $id_material, int $id_lote, int $quantidade): string
    {
        try {
            $sql = "INSERT INTO materiais_movimentacao(id_material, id_movimentacao, id_lote, quantidade)
            VALUES (?, ?, ?, ?)";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_material, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_movimentacao, PDO::PARAM_INT);
            $stmt->bindValue(3, $id_lote, PDO::PARAM_INT);
            $stmt->bindValue(4, $quantidade, PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return 'Materiais salvos com sucesso!';
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Material Movimentação DAO 01]" . $e->getMessage());
        }
    }

    public function getMateriaisByMovimentacao(int $id_movimentacao): array
    {
        try {
            $sql = "SELECT 
            mm.id_material, mm.id_movimentacao, mm.id_lote, mm.quantidade,
            ma.descricao, lo.lote, lo.vencimento, ma.unidade_base
            FROM materiais_movimentacao mm
            INNER JOIN materiais ma ON ma.id_material = mm.id_material
            INNER JOIN lotes lo ON mm.id_lote = lo.id_lote
            WHERE 1=1
            AND mm.id_movimentacao = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_movimentacao, PDO::PARAM_INT);
            // $stmt->debugDumpParams();

            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Material Movimentação DAO 02]" . $e->getMessage());
        }
    }

    public function excluirMaterial(int $id_material, int $id_lote, int $id_movimentacao): string
    {
        try {
            $sql = "DELETE FROM materiais_movimentacao WHERE id_movimentacao = ? AND id_material = ? AND id_lote = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_movimentacao, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_material, PDO::PARAM_INT);
            $stmt->bindValue(3, $id_lote, PDO::PARAM_INT);
            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return "Material excluído com sucesso!";
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Material Movimentação DAO 03]" . $e->getMessage());
        }
    }
}
