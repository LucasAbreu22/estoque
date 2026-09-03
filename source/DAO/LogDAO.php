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

    public function getLogs(int $offset = 0, string $tabela_afetada = "")
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

            if (!empty($tabela_afetada)) {
                $sql .= " AND lo.tabela_afetada = :tabela_afetada";
            }

            $sql .= " ORDER BY lo.id_log DESC LIMIT 14 OFFSET :offset";

            $stmt = $this->connect->prepare($sql);

            if (!empty($tabela_afetada)) {
                $stmt->bindValue(":tabela_afetada", $tabela_afetada, PDO::PARAM_STR);
            }

            $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Log DAO 02]" . $e->getMessage());
        }
    }
}
