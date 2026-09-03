<?php

namespace Source\App;

use Exception;
use Source\Models\Log;
use Source\Models\MovimentacaoEstoque;
use Source\Models\Usuario;

class Usuarios
{

    function getUsuarios($param): void
    {
        try {
            $offset = isset($param["offset"]) ? (int) $param["offset"] : 0;
            $search = isset($param["search"]) ? trim($param["search"]) : "";

            $usuario = new Usuario();

            $callback = [
                "code" => 200,
                "data" => [
                    "usuarios" => $usuario->getUsuarios($offset, $search),
                    "qtdUsuarios" => $usuario->contarUsuarios($search)
                ]
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }

    function salvarUsuario($param): void
    {
        try {
            $isEdicao = !empty($param["id_usuario"]);

            // Captura o estado anterior para o LOG de edição.
            $valorAntigo = null;
            if ($isEdicao) {
                $antigo = new Usuario();
                $antigo->setIdUsuario((int) $param["id_usuario"]);
                $antigo->getUsuarioById();

                $valorAntigo = [
                    "nome" => $antigo->getNome(),
                    "ponto" => $antigo->getPonto(),
                ];
            }

            $usuario = new Usuario();
            if ($isEdicao) $usuario->setIdUsuario((int) $param["id_usuario"]);
            $usuario->setNome(isset($param["nome"]) ? trim($param["nome"]) : "");
            $usuario->setPonto(empty($param["ponto"]) ? null : $param["ponto"]);
            $usuario->setSenha(empty($param["senha"]) ? null : $param["senha"]);

            $resultado = $usuario->salvarUsuario();

            // LOG da interação (criação ou edição).
            Log::registrar(
                usuarioLogadoId(),
                "usuarios",
                (int) $resultado["id_usuario"],
                $resultado["evento"],
                $valorAntigo,
                ["nome" => $usuario->getNome(), "ponto" => $usuario->getPonto()]
            );

            echo json_encode(["code" => 200, "message" => $resultado["message"]]);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }

    function excluirUsuario($param): void
    {
        try {
            if (empty($param["id_usuario"])) {
                throw new Exception("[ERRO][Usuários 01] Informação de ID de usuário não encontrada!", 1);
            }

            $usuario = new Usuario();
            $usuario->setIdUsuario((int) $param["id_usuario"]);
            $usuario->getUsuarioById();

            $valorAntigo = [
                "nome" => $usuario->getNome(),
                "ponto" => $usuario->getPonto(),
            ];

            $message = $usuario->excluirUsuario();

            // LOG da exclusão.
            Log::registrar(
                usuarioLogadoId(),
                "usuarios",
                (int) $param["id_usuario"],
                "DELETE",
                $valorAntigo,
                null
            );

            echo json_encode(["code" => 200, "message" => $message]);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }

    function consultarPonto($param): void
    {
        try {

            $ponto =  isset($param["ponto"]) ? $param["ponto"] : null;

            $usuario = new Usuario();
            $usuario->setPonto($ponto);

            $callback = [
                "code" => 200,
                "data" => [
                    $usuario->consultarPonto()
                ]
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
