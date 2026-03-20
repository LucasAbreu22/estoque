<?php

namespace Source\App;

use Source\Models\Document;

use Exception;

class Documentos
{

    function getComprovanteSaida($param): void
    {
        try {
            if (empty($param) || $param < 0) throw new Exception("[ERRO][Documentos 01] Informação de ID de movimentação inválida!", 1);

            $documento = new Document();
            $documento->getComprovanteSaida(67);

            // echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
