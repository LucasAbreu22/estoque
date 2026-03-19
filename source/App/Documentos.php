<?php

namespace Source\App;

use Source\Models\Document;

use Exception;

class Documentos
{

    function getComprovanteSaida($param): void
    {
        try {
            // echo "Teste";
            $documento = new Document();
            $documento->getComprovanteSaida();

            // echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
