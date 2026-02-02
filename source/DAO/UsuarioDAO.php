<?php

namespace Source\DAO;

use Exception;
use PDO;
use PDOException;

use Source\Connect;

class UsuarioDAO
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

    public function getUsuarioByPonto(string $ponto)
    {
        try {
            $sql = "SELECT 
            *
            FROM usuarios
            WHERE visibilidade = 1
            AND ponto = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $ponto, PDO::PARAM_STR);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Usuário DAO 01]" . $e->getMessage());
        }
    }

    public function getUsuarios(int $offset = 0)
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
            throw new Exception("[ERRO][Usuário DAO 02]" . $e->getMessage());
        }
    }
}
