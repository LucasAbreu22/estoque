<?php

namespace Source\App;

use Source\Models\Document;

use Exception;

class Documentos
{

    function getComprovanteSaida($param): void
    {
        try {
            if (empty($param) || isset($param["id_movimentacao"]) && $param["id_movimentacao"] < 0) throw new Exception("[ERRO][Documentos 01] Informação de ID de movimentação inválida!", 1);

            $documento = new Document();
            $documento->getComprovanteSaida((int)$param["id_movimentacao"]);

            // echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
