<?php

namespace Source\App;

use Source\Models\Log;

class Logs
{
    function getLogs($param): void
    {
        try {
            $offset = isset($param["offset"]) ? (int) $param["offset"] : 0;
            $tabela = isset($param["tabela"]) ? trim($param["tabela"]) : "";
            $evento = isset($param["evento"]) ? trim($param["evento"]) : "";
            $buscarUsuario = isset($param["buscarUsuario"]) ? trim($param["buscarUsuario"]) : "";
            $dataInicial = isset($param["dataInicial"]) ? trim($param["dataInicial"]) : "";
            $dataFinal = isset($param["dataFinal"]) ? trim($param["dataFinal"]) : "";

            $log = new Log();

            $callback = [
                "code" => 200,
                "data" => [
                    "logs" => $log->getLogs($offset, $tabela, $evento, $buscarUsuario, $dataInicial, $dataFinal),
                    "qtdLogs" => $log->contarLogs($tabela, $evento, $buscarUsuario, $dataInicial, $dataFinal)
                ]
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["code" => 501, "message" => $th->getMessage()]);
        }
    }
}
