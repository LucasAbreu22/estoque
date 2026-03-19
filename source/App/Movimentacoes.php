<?php

namespace Source\App;

use Exception;
use Source\Models\Lote;
use Source\Models\Material;
use Source\Models\MaterialMovimentacao;
use Source\Models\MovimentacaoEstoque;

class Movimentacoes
{

    function getMovimentacoes($param): void
    {
        try {
            $offset = (int)$param["offset"];
            $dataInicial = $param["dataInicial"];
            $dataFinal = $param["dataFinal"];
            $buscarCodSig = $param["buscarCodSig"];
            $buscarMaterial = $param["buscarMaterial"];
            $buscarPessoa = $param["buscarPessoa"];
            $fltrMovEntrada =  isset($param["fltrMovEntrada"]) ? filter_var($param["fltrMovEntrada"], FILTER_VALIDATE_BOOLEAN) : false;;
            $fltrMovSaida =  isset($param["fltrMovSaida"]) ? filter_var($param["fltrMovSaida"], FILTER_VALIDATE_BOOLEAN) : false;;



            $movimentacao = new MovimentacaoEstoque();

            $callback = [
                "code" => 200,
                "data" => [
                    "movimentacoes" => $movimentacao->getMovimentacoes($offset, $dataInicial, $dataFinal, $buscarCodSig, $buscarMaterial, $buscarPessoa, $fltrMovEntrada, $fltrMovSaida),
                    "qtdMovimentacoes" => $movimentacao->contarMovimentacoes($dataInicial, $dataFinal, $buscarCodSig, $buscarMaterial, $buscarPessoa, $fltrMovEntrada, $fltrMovSaida)
                ]
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }

    function excluirMaterial($param): void
    {
        try {

            if (!isset($param["id_movimentacao"]) || empty($param["id_movimentacao"])) throw new Exception("[ERRO][Movimentações 01] Informação de ID de movimentação não encontrada!", 1);
            if (!isset($param["id_material"]) || empty($param["id_material"])) throw new Exception("[ERRO][Movimentações 02] Informação de ID de material não encontrada!", 1);
            if (!isset($param["id_lote"]) || empty($param["id_lote"])) throw new Exception("[ERRO][Movimentações 03] Informação de ID de lote não encontrada!", 1);
            if (!isset($param["quantidade"]) || empty($param["quantidade"])) throw new Exception("[ERRO][Movimentações 01] Informação de quantidade não encontrada!", 1);

            $id_movimentacao = (int)$param["id_movimentacao"];
            $id_material = (int)$param["id_material"];
            $id_lote = (int)$param["id_lote"];
            $quantidade = (int)$param["quantidade"];

            $material = new Material($id_material);
            $lote = new Lote($id_lote);

            $materialMov = new MaterialMovimentacao();
            $materialMov->setIdMovimentacao($id_movimentacao);
            $materialMov->setMaterial($material);
            $materialMov->setLote($lote);
            $materialMov->setQuantidade($quantidade);

            $callback = [
                "code" => 200,
                "message" => $materialMov->excluirMaterial(),
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
