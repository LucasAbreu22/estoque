<?php

namespace Source\App;

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
}
