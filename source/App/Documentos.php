<?php

namespace Source\App;

use Source\Models\Document;

use Exception;
use Source\Models\Material;
use Source\Models\MovimentacaoEstoque;

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

    function gerarRelatorioEstoque(): void
    {
        try {

            $produtos = [];

            $m = new Material();

            $arrayMaterial = $m->getAllMateriais();

            foreach ($arrayMaterial as $produto) {
                $material = new Material();
                $material->setDescricao($produto["descricao"]);
                $material->setUnidadeBase($produto["unidade_base"]);
                $material->setQuantidade($produto["saldo"] ?? 0);

                $produtos[] = $material;
            }

            $documento = new Document();
            $documento->gerarRelatorioEstoque($produtos);

            // echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }

    function gerarRelatorioMovimentacao(): void
    {
        try {

            $material = new Material();
            $materiais = $material->getComparacaoSaldo();

            $documento = new Document();
            $documento->gerarRelatorioMovimentacao($materiais);

            // echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
