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

            $movimentacao = new MovimentacaoEstoque();

            $callback = [
                "code" => 200,
                "data" => [
                    "movimentacoes" => $movimentacao->getMovimentacoes($offset, $dataInicial, $dataFinal, $buscarCodSig, $buscarMaterial, $buscarPessoa),
                    "qtdMovimentacoes" => $movimentacao->contarMovimentacoes($dataInicial, $dataFinal, $buscarCodSig, $buscarMaterial, $buscarPessoa)
                ]
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
