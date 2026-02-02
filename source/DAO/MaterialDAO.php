<?php

namespace Source\DAO;

use Exception;
use PDO;
use PDOException;

use Source\Connect;

class MaterialDAO
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

    public function getMateriais(int $offset = 0)
    {
        try {
            $sql = "SELECT 
            ma.id_material, ma.id_categoria, ma.codigo,
            ma.descricao, ma.quantidade, ma.unidade_base, ma.unidade_compra,
            ma.fator_conversao, ma.quantidade_minima, ma.custo_unitario,
            CASE
                WHEN ma.quantidade = 0 THEN 'Sem Estoque'
                WHEN ma.quantidade < ma.quantidade_minima THEN 'Acabando'
                ELSE 'Normal'
            END AS status,
            ma.localizacao, DATE_FORMAT(ma.data_criacao, '%d/%m/%Y %H:%i:%s') AS data_criacao, DATE_FORMAT(ma.data_edicao, '%d/%m/%Y %H:%i:%s') AS data_edicao,
            ca.nome AS categoria
            FROM materiais ma
            INNER JOIN
            categorias ca
            ON
            ma.id_categoria = ca.id_categoria
            WHERE ma.visibilidade = 1
            ORDER BY ma.descricao ASC
            LIMIT 13 OFFSET ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $offset, PDO::PARAM_INT);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Material DAO 01]" . $e->getMessage());
        }
    }

    public function getMaterialById(int $id_material)
    {
        try {
            $sql = "SELECT 
            *
            FROM 
            materiais
            WHERE
            id_material = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_material, PDO::PARAM_INT);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetch();
        } catch (\Throwable $e) {
            throw new Exception("[ERRO][Material DAO 06]" . $e->getMessage());
        }
    }

    public function contarMateriais()
    {
        try {
            $sql = "SELECT 
            count(*) AS qtdMateriais
            FROM materiais ma
            INNER JOIN
            categorias ca
            ON
            ma.id_categoria = ca.id_categoria
            WHERE ma.visibilidade";

            $stmt = $this->connect->prepare($sql);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetch()->qtdMateriais;
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Material DAO 02]" . $e->getMessage());
        }
    }

    public function excluirMaterial(int $id_material)
    {
        try {
            $sql = "UPDATE materiais SET visibilidade = 0, data_edicao = ? WHERE id_material = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(2, convertNull($id_material), PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();
            return "Material excluído com sucesso!";
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Material DAO 05]" . $e->getMessage());
        }
    }

    public function criarMaterial(array $material)
    {
        try {
            $sql = "INSERT INTO materiais(codigo, descricao, quantidade, unidade_base, unidade_compra, fator_conversao, quantidade_minima, custo_unitario, localizacao, id_categoria)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, convertNull($material["codigo"]), PDO::PARAM_STR);
            $stmt->bindValue(2, convertNull($material["descricao"]), PDO::PARAM_STR);
            $stmt->bindValue(3, $material["quantidade"], PDO::PARAM_INT);
            $stmt->bindValue(4, convertNull($material["unidade_base"]), PDO::PARAM_STR);
            $stmt->bindValue(5, convertNull($material["unidade_compra"]), PDO::PARAM_STR);
            $stmt->bindValue(6, convertNull($material["fator_conversao"]), PDO::PARAM_STR);
            $stmt->bindValue(7, convertNull($material["quantidade_minima"]), PDO::PARAM_INT);
            $stmt->bindValue(8, convertNull($material["custo_unitario"]), PDO::PARAM_STR);
            $stmt->bindValue(9, convertNull($material["localizacao"]), PDO::PARAM_STR);
            $stmt->bindValue(10, convertNull($material["id_categoria"]), PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return "Dados criados com sucesso!";
        } catch (PDOException $e) {
            $msg = "[ERRO][Material DAO 03] ";
            $msg .= str_contains($e->getMessage(), "Duplicate entry") ? "Código de material já existente!" : $e->getMessage();

            throw new Exception($msg);
        }
    }

    public function editarMaterial(array $material)
    {
        try {
            $sql = "UPDATE materiais SET codigo = ?, descricao = ?, 
            quantidade = ?, unidade_base = ?, unidade_compra = ?, 
            fator_conversao = ?, quantidade_minima = ?, 
            custo_unitario = ?, localizacao = ?, data_edicao = ?, id_categoria = ?
            WHERE id_material = ?";


            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, convertNull($material["codigo"]), PDO::PARAM_STR);
            $stmt->bindValue(2, convertNull($material["descricao"]), PDO::PARAM_STR);
            $stmt->bindValue(3, $material["quantidade"], PDO::PARAM_INT);
            $stmt->bindValue(4, convertNull($material["unidade_base"]), PDO::PARAM_STR);
            $stmt->bindValue(5, convertNull($material["unidade_compra"]), PDO::PARAM_STR);
            $stmt->bindValue(6, convertNull($material["fator_conversao"]), PDO::PARAM_STR);
            $stmt->bindValue(7, convertNull($material["quantidade_minima"]), PDO::PARAM_INT);
            $stmt->bindValue(8, convertNull($material["custo_unitario"]), PDO::PARAM_STR);
            $stmt->bindValue(9, convertNull($material["localizacao"]), PDO::PARAM_STR);
            $stmt->bindValue(10, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(11, convertNull($material["id_categoria"]), PDO::PARAM_INT);
            $stmt->bindValue(12, convertNull($material["id_material"]), PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return "Dados salvos com sucesso!";
        } catch (PDOException $e) {
            $msg = "[ERRO][Material DAO 04] ";
            $msg .= str_contains($e->getMessage(), "Duplicate entry") ? "Código de material já existente!" : $e->getMessage();

            throw new Exception($msg);
        }
    }
}
