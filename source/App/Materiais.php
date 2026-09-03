<?php

namespace Source\App;

use Exception;
use Source\Models\Categoria;
use Source\Models\Log;
use Source\Models\Lote;
use Source\Models\Material;
use Source\Models\MaterialMovimentacao;
use Source\Models\MovimentacaoEstoque;
use Source\Models\Solicitante;
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

            $isEdicao = !empty($param["id_material"]);

            // Captura o estado anterior para o LOG de edição.
            $valorAntigo = null;
            if ($isEdicao) {
                $antigo = new Material((int)$param["id_material"]);
                $antigo->getMaterialById();

                $valorAntigo = [
                    "descricao" => $antigo->getDescricao(),
                    "unidade_base" => $antigo->getUnidadeBase(),
                    "unidade_compra" => $antigo->getUnidadeCompra(),
                    "fator_conversao" => $antigo->getFatorConversao(),
                    "quantidade_minima" => $antigo->getQuantidadeMinima(),
                    "custo_unitario" => $antigo->getCustoUnitario(),
                    "localizacao" => $antigo->getLocalizacao(),
                    "id_categoria" => $antigo->getCategoria() ? $antigo->getCategoria()->getIdCategoria() : null,
                ];
            }

            $material = new Material(
                empty($param["id_material"]) ? null : (int)$param["id_material"],
                $categoria,
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

            // LOG da interação (criação ou edição).
            Log::registrar(
                usuarioLogadoId(),
                "materiais",
                $material->getIdMaterial(),
                $isEdicao ? "UPDATE" : "INSERT",
                $valorAntigo,
                [
                    "descricao" => $material->getDescricao(),
                    "unidade_base" => $material->getUnidadeBase(),
                    "unidade_compra" => $material->getUnidadeCompra(),
                    "fator_conversao" => $material->getFatorConversao(),
                    "quantidade_minima" => $material->getQuantidadeMinima(),
                    "custo_unitario" => $material->getCustoUnitario(),
                    "localizacao" => $material->getLocalizacao(),
                    "id_categoria" => $categoria->getIdCategoria(),
                ]
            );

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

            // Captura o estado anterior para o LOG de exclusão.
            $valorAntigo = null;
            try {
                $material->getMaterialById();
                $valorAntigo = ["descricao" => $material->getDescricao()];
            } catch (\Throwable $e) {
                $valorAntigo = null;
            }

            $callback = [
                "code" => 200,
                "message" => $material->excluirMaterial(),
            ];

            // LOG da exclusão.
            Log::registrar(
                usuarioLogadoId(),
                "materiais",
                (int)$param["id_material"],
                "DELETE",
                $valorAntigo,
                null
            );

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

            $movimentacao = new MovimentacaoEstoque();
            $movimentacao->setCodigoSigma(empty($param["codigoSigma"]) ? null : $param["codigoSigma"]);
            $movimentacao->setUsuario($usuario);
            $movimentacao->setTipo($param["tipo"]);
            $movimentacao->setPontoSolicitante($param["pontoSolicitante"]);
            $movimentacao->setNomeSolicitante($param["nomeSolicitante"]);
            $movimentacao->setUnidadeUtilizada('BASE');

            $materiais = $param["materiais"];

            foreach ($materiais as $material) {
                $materialObj = new Material($material["id_material"]);
                $materialObj->getMaterialById();

                foreach ($material["loteList"] as $lote) {
                    $loteObj = new Lote();
                    $loteObj->setIdMaterial((int)$material["id_material"]);
                    $loteObj->setIdLote(isset($lote["id_lote"]) ? (int)$lote["id_lote"] : null);
                    $loteObj->setLote((int)$lote["lote"]);
                    $loteObj->setVencimento($lote["vencimento"]);
                    $loteObj->setQuantidade($lote["quantidade"]);

                    $result = array_merge($materialObj->getLotes(), [$loteObj]);
                    $materialObj->setLotes($result);
                }

                foreach ($materialObj->getLotes() as $lote) {
                    $materialMovimentacao = new MaterialMovimentacao();
                    $materialMovimentacao->setMaterial($materialObj);
                    $materialMovimentacao->setLote($lote);
                    $materialMovimentacao->setQuantidade($lote->getQuantidade());

                    $result = array_merge($movimentacao->getMateriais(), [$materialMovimentacao]);
                    $movimentacao->setMateriais($result);
                }
            }

            $msg = $movimentacao->criarMovimentacao();

            // LOG da movimentação de estoque (ENTRADA ou SAIDA).
            Log::registrar(
                $usuario->getIdUsuario(),
                "movimentacoes_estoque",
                $movimentacao->getIdMovimentacao(),
                $param["tipo"],
                null,
                [
                    "tipo" => $param["tipo"],
                    "codigo_sigma" => empty($param["codigoSigma"]) ? null : $param["codigoSigma"],
                    "ponto_solicitante" => $param["pontoSolicitante"],
                    "nome_solicitante" => $param["nomeSolicitante"],
                    "qtd_itens" => count($materiais),
                ]
            );

            $callback = [
                "code" => 200,
                "message" => $msg,
                "id_movimentacao" => $movimentacao->getIdMovimentacao()
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }

    function salvarLote($param): void
    {
        try {
            if (!isset($param["id_material"])) {
                throw new Exception("[ERRO][Materiais 01] Informação de ID de MATERIAL não encontrada!", 1);
            }
            if (!isset($param["id_lote"])) {
                throw new Exception("[ERRO][Materiais 02] Informação de ID de LOTE não encontrada!", 1);
            }
            if (!isset($param["lote"])) {
                throw new Exception("[ERRO][Materiais 03] Informação de LOTE não encontrada!", 1);
            }
            if (!isset($param["quantidade"])) {
                throw new Exception("[ERRO][Materiais 04] Informação de QUANTIDADE não encontrada!", 1);
            }
            if (!isset($param["vencimento"])) {
                throw new Exception("[ERRO][Materiais 05] Informação de VENCIMENTO não encontrada!", 1);
            }

            $loteObj = new Lote();
            $loteObj->setIdLote((int)$param["id_lote"]);
            $loteObj->setIdMaterial((int)$param["id_material"]);
            $loteObj->setLote((int)$param["lote"]);
            $loteObj->setQuantidade((int)$param["quantidade"]);
            $loteObj->setVencimento($param["vencimento"]);

            echo json_encode(["code" => 200, "message" => $loteObj->salvarLote()]);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }

    function excluirLote($param): void
    {
        try {
            if (!isset($param) || empty($param)) {
                throw new Exception("[ERRO][Materiais 08] Informação de ID de LOTE não encontrada!", 1);
            }

            $loteObj = new Lote();
            $loteObj->setIdLote((int)$param);

            echo json_encode(["code" => 200, "message" => $loteObj->excluirLote()]);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }

    function getSolicitante($param): void
    {
        try {
            if (!isset($param) || empty($param)) {
                throw new Exception("[ERRO][Materiais 09] Informação de ponto de solicitante não encontrada!", 1);
            }

            if (!isset($param["ponto"]) || empty($param["ponto"])) {
                throw new Exception("[ERRO][Materiais 09] Informação de ponto de solicitante não encontrada!", 1);
            }

            $ponto = $param["ponto"];

            $solicitante = new Solicitante();
            $callback = $solicitante->getSolicitante($ponto);

            echo json_encode(["code" => 200, "data" => $callback]);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }
}
