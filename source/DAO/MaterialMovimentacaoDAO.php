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

    public function salvarMaterialMov(int $id_material, int $id_movimentacao, int $id_lote, int $quantidade): string
    {
        try {
            $sql = "INSERT INTO materiais_movimentacao(id_material, id_movimentacao, id_lote, quantidade)
            VALUES (?, ?, ?, ?)";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_material, PDO::PARAM_INT); // Formulário
            $stmt->bindValue(2, $id_movimentacao, PDO::PARAM_INT); // EVENTO
            $stmt->bindValue(3, $id_lote, PDO::PARAM_INT);
            $stmt->bindValue(4, $quantidade, PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return 'Materiais salvos com sucesso!';
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Material Movimentação DAO 01]" . $e->getMessage());
        }
    }
}
