<?php

namespace Source\DAO;

use Exception;
use PDO;
use PDOException;

use Source\Connect;

class LogDAO
{
    private $connect;

    public function __construct()
    {
        $this->connect = Connect::getInstance();
    }

    public function salvarLog(int $id_usuario, string $tabela_afetada, int $id_registro, string $evento, ?string $valor_antigo = null, ?string $valor_novo = null): string
    {
        try {
            $sql = "INSERT INTO logs_sistema (id_usuario, tabela_afetada, id_registro, evento, valor_antigo, valor_novo)
            VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);
            $stmt->bindValue(2, $tabela_afetada, PDO::PARAM_STR);
            $stmt->bindValue(3, $id_registro, PDO::PARAM_INT);
            $stmt->bindValue(4, $evento, PDO::PARAM_STR);
            $stmt->bindValue(5, $valor_antigo, is_null($valor_antigo) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(6, $valor_novo, is_null($valor_novo) ? PDO::PARAM_NULL : PDO::PARAM_STR);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return $this->connect->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Log DAO 01]" . $e->getMessage());
        }
    }

    public function getLogs(int $offset = 0, string $tabela_afetada = "", string $evento = "", string $buscarUsuario = "", string $dataInicial = "", string $dataFinal = "")
    {
        try {
            $sql = "SELECT 
            lo.id_log, lo.tabela_afetada, lo.id_registro, lo.evento,
            lo.valor_antigo, lo.valor_novo,
            DATE_FORMAT(lo.data_evento, '%d/%m/%Y %H:%i:%s') AS data_evento,
            us.nome, us.ponto
            FROM logs_sistema lo
            INNER JOIN usuarios us ON lo.id_usuario = us.id_usuario
            WHERE 1=1";

            $sql .= $this->montarFiltros($tabela_afetada, $evento, $buscarUsuario, $dataInicial, $dataFinal);

            $sql .= " ORDER BY lo.id_log DESC LIMIT 14 OFFSET :offset";

            $stmt = $this->connect->prepare($sql);

            $this->vincularFiltros($stmt, $tabela_afetada, $evento, $buscarUsuario, $dataInicial, $dataFinal);

            $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Log DAO 02]" . $e->getMessage());
        }
    }

    public function contarLogs(string $tabela_afetada = "", string $evento = "", string $buscarUsuario = "", string $dataInicial = "", string $dataFinal = "")
    {
        try {
            $sql = "SELECT count(*) AS qtdLogs
            FROM logs_sistema lo
            INNER JOIN usuarios us ON lo.id_usuario = us.id_usuario
            WHERE 1=1";

            $sql .= $this->montarFiltros($tabela_afetada, $evento, $buscarUsuario, $dataInicial, $dataFinal);

            $stmt = $this->connect->prepare($sql);

            $this->vincularFiltros($stmt, $tabela_afetada, $evento, $buscarUsuario, $dataInicial, $dataFinal);

            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Log DAO 03]" . $e->getMessage());
        }
    }

    private function montarFiltros(string $tabela_afetada, string $evento, string $buscarUsuario, string $dataInicial, string $dataFinal): string
    {
        $sql = "";

        if (!empty($tabela_afetada)) {
            $sql .= " AND lo.tabela_afetada = :tabela_afetada";
        }
        if (!empty($evento)) {
            $sql .= " AND lo.evento = :evento";
        }
        if (!empty($buscarUsuario)) {
            $sql .= " AND (us.nome LIKE :buscarUsuario OR us.ponto LIKE :buscarUsuario)";
        }
        if (!empty($dataInicial)) {
            $sql .= " AND lo.data_evento >= :dataInicial";
        }
        if (!empty($dataFinal)) {
            $sql .= " AND lo.data_evento <= :dataFinal";
        }

        return $sql;
    }

    private function vincularFiltros($stmt, string $tabela_afetada, string $evento, string $buscarUsuario, string $dataInicial, string $dataFinal): void
    {
        if (!empty($tabela_afetada)) {
            $stmt->bindValue(":tabela_afetada", $tabela_afetada, PDO::PARAM_STR);
        }
        if (!empty($evento)) {
            $stmt->bindValue(":evento", $evento, PDO::PARAM_STR);
        }
        if (!empty($buscarUsuario)) {
            $stmt->bindValue(":buscarUsuario", "%$buscarUsuario%", PDO::PARAM_STR);
        }
        if (!empty($dataInicial)) {
            $stmt->bindValue(":dataInicial", $dataInicial, PDO::PARAM_STR);
        }
        if (!empty($dataFinal)) {
            $stmt->bindValue(":dataFinal", $dataFinal, PDO::PARAM_STR);
        }
    }
}
