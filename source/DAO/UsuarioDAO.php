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

    public function getUsuarioById(int $id_usuario)
    {
        try {
            $sql = "SELECT 
            *
            FROM usuarios
            WHERE visibilidade = 1
            AND id_usuario = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Usuário DAO 04]" . $e->getMessage());
        }
    }

    public function consultarPonto(?int $ponto = null)
    {
        try {
            $sql = "SELECT 
            *
            FROM usuarios
            WHERE visibilidade = 1
            AND ponto = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, $ponto, PDO::PARAM_INT);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Usuário DAO 03]" . $e->getMessage());
        }
    }

    public function getUsuarios(int $offset = 0, string $search = "")
    {
        try {
            $sql = "SELECT 
            id_usuario, nome, ponto,
            DATE_FORMAT(data_criacao, '%d/%m/%Y %H:%i:%s') AS data_criacao,
            DATE_FORMAT(data_edicao, '%d/%m/%Y %H:%i:%s') AS data_edicao
            FROM usuarios
            WHERE visibilidade = 1";

            if (!empty($search)) {
                $sql .= " AND (nome LIKE :search OR ponto LIKE :search)";
            }

            $sql .= " ORDER BY nome ASC LIMIT 13 OFFSET :offset";

            $stmt = $this->connect->prepare($sql);

            if (!empty($search)) {
                $stmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
            }

            $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

            // $stmt->debugDumpParams();

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Usuário DAO 02]" . $e->getMessage());
        }
    }

    public function contarUsuarios(string $search = "")
    {
        try {
            $sql = "SELECT count(*) AS qtdUsuarios
            FROM usuarios
            WHERE visibilidade = 1";

            if (!empty($search)) {
                $sql .= " AND (nome LIKE :search OR ponto LIKE :search)";
            }

            $stmt = $this->connect->prepare($sql);

            if (!empty($search)) {
                $stmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
            }

            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Usuário DAO 05]" . $e->getMessage());
        }
    }

    public function criarUsuario(array $usuario)
    {
        try {
            $sql = "INSERT INTO usuarios (nome, ponto, senha, data_criacao, visibilidade)
            VALUES (?, ?, ?, ?, 1)";

            $stmt = $this->connect->prepare($sql);

            $senha = convertNull($usuario["senha"]);

            $stmt->bindValue(1, $usuario["nome"], PDO::PARAM_STR);
            $stmt->bindValue(2, $usuario["ponto"], PDO::PARAM_STR);
            $stmt->bindValue(3, $senha, is_null($senha) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(4, date('Y-m-d H:i:s'), PDO::PARAM_STR);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return [
                "message" => "Usuário criado com sucesso!",
                "newId" => $this->connect->lastInsertId()
            ];
        } catch (\Throwable $e) {
            $msg = "[ERRO][Usuário DAO 06]";
            $msg .= str_contains($e->getMessage(), "Duplicate entry") ? " Número de PONTO já existente! " : $e->getMessage();

            throw new Exception($msg);
        }
    }

    public function editarUsuario(array $usuario)
    {
        try {
            $sql = "UPDATE usuarios SET nome = ?, ponto = ?, senha = ?, data_edicao = ? WHERE id_usuario = ?";

            $stmt = $this->connect->prepare($sql);

            $senha = convertNull($usuario["senha"]);

            $stmt->bindValue(1, $usuario["nome"], PDO::PARAM_STR);
            $stmt->bindValue(2, $usuario["ponto"], PDO::PARAM_STR);
            $stmt->bindValue(3, $senha, is_null($senha) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(4, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(5, $usuario["id_usuario"], PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return "Usuário editado com sucesso!";
        } catch (\Throwable $e) {
            $msg = "[ERRO][Usuário DAO 07]";
            $msg .= str_contains($e->getMessage(), "Duplicate entry") ? " Número de PONTO já existente! " : $e->getMessage();

            throw new Exception($msg);
        }
    }

    public function excluirUsuario(int $id_usuario)
    {
        try {
            $sql = "UPDATE usuarios SET visibilidade = 0, data_edicao = ? WHERE id_usuario = ?";

            $stmt = $this->connect->prepare($sql);

            $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(2, $id_usuario, PDO::PARAM_INT);

            /* $stmt->debugDumpParams(); */

            $stmt->execute();

            return "Usuário excluído com sucesso!";
        } catch (PDOException $e) {
            throw new Exception("[ERRO][Usuário DAO 08]" . $e->getMessage());
        }
    }
}
