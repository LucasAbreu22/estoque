<?php

namespace Source\App;

use Source\Models\MovimentacaoEstoque;

class Movimentacoes
{

    function getMovimentacoes($param): void
    {
        try {
            $offset = (int)$param["offset"];

            $movimentacao = new MovimentacaoEstoque();

            $callback = [
                "code" => 200,
                "data" => [
                    "movimentacoes" => $movimentacao->getMovimentacoes($offset),
                    "qtdMovimentacoes" => $movimentacao->contarMovimentacoes()
                ]
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
