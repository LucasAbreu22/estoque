<?php

namespace Source\App;

use Exception;
use Source\Models\Categoria;
use Source\Models\Material;
use Source\Models\MovimentacaoEstoque;
use Source\Models\Usuario;

class Materiais
{

    function getMateriais($param): void
    {
        try {
            $offset = (int)$param["offset"];

            $material = new Material();
            $callback = [
                "code" => 200,
                "data" => [
                    "materiais" => $material->getMateriais($offset),
                    "qtdMateriais" => $material->contarMateriais()
                ]
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }

    function salvarMaterial($param): void
    {
        try {

            $categoria = new Categoria((int)$param["id_categoria"]);

            if (empty($categoria->getCategoriaById())) throw new Exception("[ERRO][Materiais 01] Categoria não encontrada!", 1);

            $material = new Material(
                empty($param["id_material"]) ? null : (int)$param["id_material"],
                $categoria,
                $param["codigo"],
                $param["descricao"],
                (int)$param["quantidade"],
                $param["unidade_base"],
                $param["unidade_compra"],
                $param["fator_conversao"],
                $param["quantidade_minima"],
                $param["custo_unitario"],
                $param["localizacao"],
            );

            $callback = [
                "code" => 200,
                "message" => $material->salvarMaterial(),
                "data" => ["qtdMateriais" => $material->contarMateriais()]
            ];

            if (empty($param["id_material"])) $callback["data"]["newId"] = $material->getIdMaterial();

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }

    function excluirMaterial($param): void
    {
        try {

            if (!isset($param["id_material"]) || empty($param["id_material"])) {
                throw new Exception("[ERRO][Materiais 02] Informação inválida de MATERIAL!");
            }

            $material =  new Material((int)$param["id_material"]);

            $callback = [
                "code" => 200,
                "message" => $material->excluirMaterial(),
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }

    function criarMovimentacao($param): void
    {
        try {

            $usuario = new Usuario();
            $usuario->setPonto($param["pontoResponsavel"]);
            $usuario->getUsuarioByPonto();

            $material = new Material($param["id_material"]);
            $material->getMaterialById();

            $movimentacao = new MovimentacaoEstoque();
            $movimentacao->setMaterial($material);
            $movimentacao->setUsuario($usuario);
            $movimentacao->setTipo($param["tipo"]);
            $movimentacao->setQuantidade($param["quantidade"]);
            $movimentacao->setPontoSolicitante($param["pontoSolicitante"]);
            $movimentacao->setNomeSolicitante($param["nomeSolicitante"]);

            $callback = [
                "code" => 200,
                "message" => $movimentacao->criarMovimentacao(),
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }
}
