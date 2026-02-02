<?php

namespace Source\DAO;

use Exception;
use PDO;
use PDOException;

use Source\Connect;

class CategoriaDAO
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

    public function getAllMateriais()
    {
        try {
            $sql = "SELECT 
            *
            FROM categorias
            WHERE visibilidade = 1
            ORDER BY nome ASC";

            $stmt = $this->connect->prepare($sql);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Categoria DAO 01]" . $e->getMessage());
        }
    }

    public function deleteMaterial(int $id_material)
    {
        try {
            $sql = "UPDATE materiais SET visibilidade = 0 WHERE id_material = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, convertNull($id_material), PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();
            return "Anotação excluída com sucesso!";
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Anotação DAO 05]" . $e->getMessage());
        }
    }

    public function salvarMaterial(array $material)
    {
        try {
            $sql = "INSERT INTO materiais(codigo, descricao, quantidade, unidade_base, unidade_compra, fator_conversao, quantidade_minima, custo_unitario, localizacao, id_categoria)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, convertNull($material[0]), PDO::PARAM_STR);
            $stmt->bindValue(2, convertNull($material[1]), PDO::PARAM_STR);
            $stmt->bindValue(3, convertNull($material[2]), PDO::PARAM_STR);
            $stmt->bindValue(4, convertNull($material[3]), PDO::PARAM_STR);
            $stmt->bindValue(5, convertNull($material[4]), PDO::PARAM_STR);
            $stmt->bindValue(6, convertNull($material[5]), PDO::PARAM_STR);
            $stmt->bindValue(7, convertNull($material[6]), PDO::PARAM_STR);
            $stmt->bindValue(8, convertNull($material[7]), PDO::PARAM_STR);
            $stmt->bindValue(9, convertNull($material[8]), PDO::PARAM_STR);
            $stmt->bindValue(10, convertNull($material[9]), PDO::PARAM_STR);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return "Dados salvos com sucesso!";
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Anotação DAO 02]" . $e->getMessage());
        }
    }

    public function getCategoriaById($id_cateogria)
    {
        try {
            $sql = "SELECT 
            *
            FROM categorias
            WHERE visibilidade = 1
            AND id_categoria = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_cateogria, PDO::PARAM_INT);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Categoria DAO 02]" . $e->getMessage());
        }
    }
}
