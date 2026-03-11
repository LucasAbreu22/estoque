<?php

namespace Source\App;

use Exception;
use Source\Models\Categoria;
use Source\Models\Lote;
use Source\Models\Material;
use Source\Models\MaterialMovimentacao;
use Source\Models\MovimentacaoEstoque;
use Source\Models\Usuario;

class Materiais
{

    function getMateriais($param): void
    {
        try {
            $offset = (int)$param["offset"];
            $search = $param["search"];
            $fltrCategoria = isset($param["fltrCategoria"]) && is_numeric($param["fltrCategoria"]) ? (int) $param["fltrCategoria"] : null;

            $fltrStatusNormal = isset($param["fltrStatusNormal"]) ? filter_var($param["fltrStatusNormal"], FILTER_VALIDATE_BOOLEAN) : false;
            $fltrStatusAcabando = isset($param["fltrStatusAcabando"]) ? filter_var($param["fltrStatusAcabando"], FILTER_VALIDATE_BOOLEAN) : false;
            $fltrStatusSemEstoque = isset($param["fltrStatusSemEstoque"]) ? filter_var($param["fltrStatusSemEstoque"], FILTER_VALIDATE_BOOLEAN) : false;

            $material = new Material();
            $callback = [
                "code" => 200,
                "data" => [
                    "materiais" => $material->getMateriais($offset, $search, $fltrCategoria, $fltrStatusNormal, $fltrStatusAcabando, $fltrStatusSemEstoque),
                    "qtdMateriais" => $material->contarMateriais($search, $fltrCategoria, $fltrStatusNormal, $fltrStatusAcabando, $fltrStatusSemEstoque)
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
                [],
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

            $materiais = $param["materiais"];

            $formatedMateriais = [];

            foreach ($materiais as $material) {

                $loteObj = new Lote();

                if ($param["tipo"] === "ENTRADA") {
                    $loteObj->setIdMaterial((int)$material["id_material"]);
                    $loteObj->setLote((int)$material["lote"]);
                    $loteObj->setVencimento($material["vencimento"]);
                } else {
                    $loteObj->setIdLote((int)$material["id_lote"]);
                }

                $loteObj->setQuantidade($material["quantidadeMov"]);

                if (!isset($formatedMateriais[$material["id_material"]])) {
                    $materialObj = new Material($material["id_material"]);
                    $materialObj->getMaterialById();
                    $materialObj->setLotes([$loteObj]);

                    $formatedMateriais[$material["id_material"]] = $materialObj;
                } else {

                    $newListLote = $formatedMateriais[$material["id_material"]]->getLotes();

                    array_push($newListLote, $loteObj);

                    $formatedMateriais[$material["id_material"]]->setLotes($newListLote);
                }
            }

            $msg = "";
            $lotesList = [];

            foreach ($formatedMateriais as $material) {

                $movimentacao = new MovimentacaoEstoque();
                $movimentacao->setCodigoSigma(empty($param["codigoSigma"]) ? null : $param["codigoSigma"]);
                $movimentacao->setUsuario($usuario);
                $movimentacao->setTipo($param["tipo"]);
                $movimentacao->setPontoSolicitante($param["pontoSolicitante"]);
                $movimentacao->setNomeSolicitante($param["nomeSolicitante"]);
                $movimentacao->setUnidadeUtilizada('BASE');

                foreach ($material->getLotes() as $lote) {
                    $materialMovimentacao = new MaterialMovimentacao();
                    $materialMovimentacao->setMaterial($material);
                    $materialMovimentacao->setLote($lote);
                    $materialMovimentacao->setQuantidade($lote->getQuantidade());

                    array_push($lotesList, $materialMovimentacao);
                }

                $movimentacao->setMateriais($lotesList);

                $msg = $movimentacao->criarMovimentacao();
            }

            $callback = [
                "code" => 200,
                "message" => $msg,
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }
}
