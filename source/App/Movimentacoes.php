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

            $movimentacao = new MovimentacaoEstoque();

            $callback = [
                "code" => 200,
                "data" => [
                    "movimentacoes" => $movimentacao->getMovimentacoes($offset, $dataInicial, $dataFinal),
                    "qtdMovimentacoes" => $movimentacao->contarMovimentacoes($dataInicial, $dataFinal)
                ]
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
