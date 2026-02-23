<?php

namespace Source\App;

use Exception;
use Source\Models\MovimentacaoEstoque;
use Source\Models\Usuario;

class Usuarios
{

    function consultarPonto($param): void
    {
        try {

            $ponto =  isset($param["ponto"]) ? $param["ponto"] : null;

            $usuario = new Usuario();
            $usuario->setPonto($ponto);

            $callback = [
                "code" => 200,
                "data" => [
                    $usuario->consultarPonto()
                ]
            ];

            echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
