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
            $dataInicial = $_GET["dataInicial"] ?? "";
            $dataFinal = $_GET["dataFinal"] ?? "";

            // Se ambos estiverem vazios, busca do ano atual
            if (empty($dataInicial) && empty($dataFinal)) {
                $dataInicial = date("Y-01-01");
                $dataFinal = date("Y-12-31");
            }

            // Ajusta para cobrir o dia inteiro se as datas existirem
            $start = !empty($dataInicial) ? $dataInicial . " 00:00:00" : "";
            $end = !empty($dataFinal) ? $dataFinal . " 23:59:59" : "";

            $movModel = new MovimentacaoEstoque();
            // Busca movimentações com os filtros (offset 0 para relatório completo)
            $movimentacoes = $movModel->getMovimentacoes(null, $start, $end);

            $documento = new Document();
            $documento->gerarRelatorioMovimentacao($movimentacoes);

            // echo json_encode($callback);
        } catch (\Throwable $th) {
            echo json_encode(["message" => $th->getMessage()]);
        }
    }
}
