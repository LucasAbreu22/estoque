<?php

namespace Source\DAO;

use Exception;
use PDO;
use PDOException;

use Source\Connect;

class MovimentacaoEstoqueDAO
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

    public function getMovimentacoes(int $offset = 0)
    {
        try {
            $sql = "SELECT 
            me.id_movimentacao, me.tipo, me.quantidade, me.ponto_solicitante, 
            me.nome_solicitante, me.data_movimentacao,
            us.ponto, us.nome,
            ma.codigo, ma.descricao
            FROM movimentacoes_estoque me 
            INNER JOIN usuarios us 
            ON me.id_usuario = us.id_usuario 
            INNER JOIN materiais ma 
            ON me.id_material = ma.id_material
            LIMIT 13 OFFSET ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $offset, PDO::PARAM_INT);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Movimentação DAO 01]" . $e->getMessage());
        }
    }

    public function contarMovimentacoes()
    {
        try {
            $sql = "SELECT 
            count(*) AS qtdMovimentacoes
            FROM movimentacoes_estoque";

            $stmt = $this->connect->prepare($sql);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetch()->qtdMovimentacoes;
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Material DAO 02]" . $e->getMessage());
        }
    }

    public function criarMovimentacao(array $movimentacao)
    {
        try {
            $sql = "INSERT INTO movimentacoes_estoque(id_material, id_usuario, tipo, quantidade, unidade_utilizada, fator_conversao_aplicado, quantidade_convertida, ponto_solicitante, nome_solicitante)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $movimentacao["id_material"], PDO::PARAM_STR); // Formulário
            $stmt->bindValue(2, $movimentacao["id_usuario"], PDO::PARAM_STR); // Formulário
            $stmt->bindValue(3, $movimentacao["tipo"], PDO::PARAM_STR); // EVENTO
            $stmt->bindValue(4, $movimentacao["quantidade"], PDO::PARAM_STR); // Formulário
            $stmt->bindValue(5, "BASE", PDO::PARAM_STR);
            $stmt->bindValue(6, NULL, PDO::PARAM_STR);
            $stmt->bindValue(7, $movimentacao["quantidade_convertida"], PDO::PARAM_STR); // CALCULADO
            $stmt->bindValue(8, $movimentacao["ponto_solicitante"], PDO::PARAM_STR); // Formulário
            $stmt->bindValue(9, $movimentacao["nome_solicitante"], PDO::PARAM_STR); // Formulário


            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return "Dados criados com sucesso!";
        } catch (PDOException $e) {
            $msg = "[ERRO][Movimentação DAO 02] ";
            $msg .= str_contains($e->getMessage(), "Duplicate entry") ? "Código de movimentação já existente!" : $e->getMessage();

            throw new Exception($msg);
        }
    }
}
