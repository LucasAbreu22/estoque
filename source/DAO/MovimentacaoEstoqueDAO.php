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

    public function rollBack()
    {
        return $this->connect->rollBack();
    }

    public function getMovimentacaoById(int $id_movimentacao)
    {
        try {
            $sql = "SELECT 
            *
            FROM movimentacoes_estoque 
            WHERE id_movimentacao = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_movimentacao, PDO::PARAM_INT);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Movimentação DAO 04]" . $e->getMessage());
        }
    }

    public function getMovimentacoes(?int $offset = 0, string $dataInicial = "", string $dataFinal = "", string $buscarCodSig = "", string $buscarMaterial = "", string $buscarPessoa = "", bool $fltrMovEntrada = false, bool $fltrMovSaida = false)
    {
        try {
            $sql = "SELECT 
            me.id_movimentacao, me.codigo_sigma, me.tipo, me.ponto_solicitante, 
            me.nome_solicitante, me.data_movimentacao,
            us.ponto, us.nome
            FROM movimentacoes_estoque me 
            INNER JOIN usuarios us ON me.id_usuario = us.id_usuario 
            INNER JOIN materiais_movimentacao mm ON me.id_movimentacao = mm.id_movimentacao
            INNER JOIN materiais ma ON mm.id_material = ma.id_material
            WHERE 1=1";

            if (!empty($dataInicial)) {
                $sql .= " AND me.data_movimentacao >= :dataInicial";
            }
            if (!empty($dataFinal)) {

                $sql .= " AND me.data_movimentacao <= :dataFinal";
            }

            if (!empty($buscarCodSig)) {
                $sql .= " AND me.codigo_sigma LIKE :codigo_sigma";
            }

            if (!empty($buscarMaterial)) {
                $sql .= " AND ma.descricao LIKE :buscarMaterial";
            }

            if (!empty($buscarPessoa)) {
                $sql .= " AND (me.nome_solicitante LIKE :buscarPessoa OR me.ponto_solicitante LIKE :buscarPessoa OR us.ponto LIKE :buscarPessoa OR us.nome LIKE :buscarPessoa)";
            }

            if ($fltrMovEntrada && $fltrMovSaida) {
                $sql .= " AND (me.tipo = 'ENTRADA' OR me.tipo = 'SAIDA')";
            } else {
                if ($fltrMovEntrada) $sql .= " AND me.tipo = 'ENTRADA'";

                else if ($fltrMovSaida) $sql .= " AND me.tipo = 'SAIDA'";
            }

            $sql .= " 
            GROUP BY me.id_movimentacao
            ORDER BY me.id_movimentacao DESC";

            if (!is_null($offset)) {
                $sql .= " LIMIT 14 OFFSET :offset";
            }

            $stmt = $this->connect->prepare($sql);

            if (!empty($dataInicial)) {
                $stmt->bindValue(":dataInicial", $dataInicial, PDO::PARAM_STR);
            }
            if (!empty($dataFinal)) {

                $stmt->bindValue(":dataFinal", $dataFinal, PDO::PARAM_STR);
            }

            if (!empty($buscarCodSig)) {
                $stmt->bindValue(":codigo_sigma", "%$buscarCodSig%", PDO::PARAM_STR);;
            }

            if (!empty($buscarMaterial)) {
                $stmt->bindValue(":buscarMaterial", "%$buscarMaterial%", PDO::PARAM_STR);;
            }

            if (!empty($buscarPessoa)) {
                $stmt->bindValue(":buscarPessoa", "%$buscarPessoa%", PDO::PARAM_STR);;
            }

            if (!is_null($offset)) {
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
            }


            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Movimentação DAO 01]" . $e->getMessage());
        }
    }

    public function contarMovimentacoes(string $dataInicial = "", string $dataFinal = "", string $buscarCodSig = "", string $buscarMaterial = "", string $buscarPessoa = "", bool $fltrMovEntrada = false, bool $fltrMovSaida = false)
    {
        try {
            $sql = "SELECT 
            count(DISTINCT me.id_movimentacao) AS qtdMovimentacoes
            FROM movimentacoes_estoque me
            INNER JOIN usuarios us ON me.id_usuario = us.id_usuario 
            INNER JOIN materiais_movimentacao mm ON me.id_movimentacao = mm.id_movimentacao
            INNER JOIN materiais ma ON mm.id_material = ma.id_material
            WHERE 1=1";

            if (!empty($dataInicial)) {
                $sql .= " AND data_movimentacao >= :dataInicial ";
            }

            if (!empty($dataFinal)) {

                $sql .= " AND data_movimentacao <= :dataFinal";
            }

            if (!empty($buscarCodSig)) {
                $sql .= " AND me.codigo_sigma LIKE :codigo_sigma";
            }

            if (!empty($buscarMaterial)) {
                $sql .= " AND ma.descricao LIKE :buscarMaterial";
            }

            if (!empty($buscarPessoa)) {
                $sql .= " AND (me.nome_solicitante LIKE :buscarPessoa OR me.ponto_solicitante LIKE :buscarPessoa OR us.ponto LIKE :buscarPessoa OR us.nome LIKE :buscarPessoa)";
            }

            if ($fltrMovEntrada && $fltrMovSaida) {
                $sql .= " AND (me.tipo = 'ENTRADA' OR me.tipo = 'SAIDA')";
            } else {
                if ($fltrMovEntrada) $sql .= " AND me.tipo = 'ENTRADA'";

                else if ($fltrMovSaida) $sql .= " AND me.tipo = 'SAIDA'";
            }

            $stmt = $this->connect->prepare($sql);

            if (!empty($dataInicial)) {

                $stmt->bindValue(":dataInicial", $dataInicial, PDO::PARAM_STR);
            }
            if (!empty($dataFinal)) {

                $stmt->bindValue(":dataFinal", $dataFinal, PDO::PARAM_STR);
            }

            if (!empty($buscarCodSig)) {
                $stmt->bindValue(":codigo_sigma", "%$buscarCodSig%", PDO::PARAM_STR);;
            }

            if (!empty($buscarMaterial)) {
                $stmt->bindValue(":buscarMaterial", "%$buscarMaterial%", PDO::PARAM_STR);;
            }

            if (!empty($buscarPessoa)) {
                $stmt->bindValue(":buscarPessoa", "%$buscarPessoa%", PDO::PARAM_STR);;
            }
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
            $sql = "INSERT INTO movimentacoes_estoque( id_usuario, tipo, ponto_solicitante, nome_solicitante, codigo_sigma)
            VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $movimentacao["id_usuario"], PDO::PARAM_STR); // Formulário
            $stmt->bindValue(2, $movimentacao["tipo"], PDO::PARAM_STR); // EVENTO
            $stmt->bindValue(3, $movimentacao["ponto_solicitante"], PDO::PARAM_STR); // Formulário
            $stmt->bindValue(4, $movimentacao["nome_solicitante"], PDO::PARAM_STR); // Formulário
            $stmt->bindValue(5, $movimentacao["codigoSigma"], PDO::PARAM_STR); // Formulário

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return $this->connect->lastInsertId();
        } catch (PDOException $e) {

            throw new Exception("[ERRO][Movimentação DAO 03]" . $e->getMessage(), 1);
        }
    }

    public function excluirMovimentacao(int $id_movimentacao)
    {
        try {
            $sql = "DELETE FROM movimentacoes_estoque WHERE id_movimentacao = ?";

            $stmt = $this->connect->prepare($sql);
            $stmt->bindValue(1, $id_movimentacao, PDO::PARAM_INT);

            // $stmt->debugDumpParams();

            $stmt->execute();

            return "Movimentação excluída com sucesso!";
        } catch (\Throwable $e) {
            throw new Exception("[ERRO][Movimentação DAO 05]" . $e->getMessage(), 1);
        }
    }
}
