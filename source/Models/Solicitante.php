<?php

namespace Source\Models;

use Exception;
use Source\DAO\SolicitanteDAO;

class Solicitante
{
    public function getSolicitante(int $ponto = 0)
    {
        if ($ponto < 1) throw new Exception("[ERRO][Solicitante 01] Informação de PONTO de Solicitante inválida!", 1);

        $dao = new SolicitanteDAO();
        return $dao->getSolicitante($ponto);
    }
}
