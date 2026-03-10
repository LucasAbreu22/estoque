<?php

namespace Source\Models;

use DateTime;
use Exception;
use Source\DAO\MovimentacaoEstoqueDAO;

use function PHPSTORM_META\type;

class MovimentacaoEstoque
{
    private int $id_movimentacao;
    private ?Usuario $usuario;
    private ?int $codigo_sigma;
    private ?array $materiais;
    private ?string $tipo;
    private ?string $unidade_utilizada;
    private ?int $fator_conversao_aplicado;
    private ?int $quantidade_convertida;
    private ?string $ponto_solicitante;
    private ?string $nome_solicitante;
    private ?string $data_movimentacao;

    function __construct()
    {
        $this->setIdMovimentacao(0);
        $this->setUsuario(null);
        $this->setCodigoSigma(null);
        $this->setmateriais(null);
        $this->settipo(null);
        $this->setUnidadeUtilizada(null);
        $this->setFatorConversaoAplicado(null);
        $this->setQuantidadeConvertida(null);
        $this->setPontoSolicitante(null);
        $this->setNomeSolicitante(null);
        $this->setDataMovimentacao(null);
    }


    public function getMovimentacoes(int $offset = 0, string $dataInicial = "", string $dataFinal = "", string $buscarCodSig = "", string $buscarMaterial = "", string $buscarPessoa = "", bool $fltrMovEntrada = false, bool $fltrMovSaida = false)
    {

        $movimentacaoDAO = new MovimentacaoEstoqueDAO();
        $movimentacoes = $movimentacaoDAO->getMovimentacoes($offset, $dataInicial, $dataFinal, $buscarCodSig, $buscarMaterial, $buscarPessoa, $fltrMovEntrada, $fltrMovSaida);

        foreach ($movimentacoes as $movimentacao) {
            $date = new DateTime($movimentacao->data_movimentacao);
            $movimentacao->data_movimentacao = $date->format('d/m/Y H:i:s');
        }

        return $movimentacoes;
    }

    public function contarMovimentacoes(string $dataInicial = "", string $dataFinal = "", string $buscarCodSig = "", string $buscarMaterial = "", string $buscarPessoa = "", bool $fltrMovEntrada = false, bool $fltrMovSaida = false)
    {
        $movimentacaoDAO = new MovimentacaoEstoqueDAO();
        return $movimentacaoDAO->contarMovimentacoes($dataInicial, $dataFinal, $buscarCodSig, $buscarMaterial, $buscarPessoa, $fltrMovEntrada, $fltrMovSaida);
    }


    public function criarMovimentacao()
    {
        if (empty($this->getMateriais())) throw new Exception("[ERRO][Movimentacao 01] Informação MATERIAL de vazia!", 1);
        if (empty($this->getUsuario()) || is_null($this->getUsuario())) throw new Exception("[ERRO][Movimentacao 02] Informação USUÁRIO de vazia!", 1);

        if (empty($this->getTipo())) throw new Exception("[ERRO][Movimentacao 03] Informação TIPO de vazia!", 1);


        if (empty($this->getUnidadeUtilizada())) throw new Exception("[ERRO][Movimentacao 05] Informação UNIDADE de vazia!", 1);

        if ($this->getTipo() === "SAIDA" && $this->getMateriais()[0]->getQuantidade() === 0) throw new Exception("[ERRO][Movimentacao 06] Material sem estoque!", 1);

        $movimentacaoDAO =  new MovimentacaoEstoqueDAO();
        try {
            // USAR TRANSACTION PARA ESSA AÇÃO PARA CASO DÊ ERRO DE CRIAR A MOVIMENTAÇÃO
            $movimentacao = [
                "id_usuario" => $this->getUsuario()->getIdUsuario(),
                "codigoSigma" => $this->getCodigoSigma(),
                "tipo" => $this->getTipo(),
                "quantidade_convertida" => $this->getQuantidadeConvertida(),
                "ponto_solicitante" => $this->getPontoSolicitante(),
                "nome_solicitante" => $this->getNomeSolicitante(),
            ];

            $movimentacaoDAO->beginTransaction();

            $this->setIdMovimentacao($movimentacaoDAO->criarMovimentacao($movimentacao));

            foreach ($this->getMateriais() as $materialMov) {

                $loteCallback =  $materialMov->getLote()->getLoteById();

                if (empty($loteCallback["quantidade"])) throw new Exception("[ERRO][Movimentacao 08] Lote não encontrado!", 1);

                if ($this->getTipo() === "SAIDA" && $materialMov->getQuantidade() > $loteCallback["quantidade"]) throw new Exception("[ERRO][Movimentacao 07] Quantidade maior que há no estoque!", 1);

                $novoEstoque = 0;

                if ($this->getTipo() === "ENTRADA") $novoEstoque = $loteCallback["quantidade"] + $materialMov->getQuantidade();

                else $novoEstoque = $loteCallback["quantidade"] - $materialMov->getQuantidade();

                $materialMov->setIdMovimentacao($this->getIdMovimentacao());
                $materialMov->salvarMaterialMov();

                $materialMov->getLote()->setQuantidade($novoEstoque);
                $materialMov->getLote()->atualizarEstoque();
            }

            // $movimentacaoDAO->rollBack();

            $movimentacaoDAO->commit();

            $callback = "Movimentação criada com sucesso!";

            return $callback;
        } catch (\Throwable $th) {
            $movimentacaoDAO->rollBack();
            throw new Exception($th->getMessage(), 1);
        }
    }

    /**
     * Get the value of id_movimentacao
     */
    public function getIdMovimentacao(): int
    {
        return $this->id_movimentacao;
    }

    /**
     * Set the value of id_movimentacao
     */
    public function setIdMovimentacao(int $id_movimentacao): self
    {
        $this->id_movimentacao = $id_movimentacao;

        return $this;
    }

    /**
     * Get the value of usuario
     */
    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    /**
     * Set the value of usuario
     */
    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get the value of codigo_sigma
     */
    public function getCodigoSigma(): ?int
    {
        return $this->codigo_sigma;
    }

    /**
     * Set the value of codigo_sigma
     */
    public function setCodigoSigma(?int $codigo_sigma): self
    {
        $this->codigo_sigma = $codigo_sigma;

        return $this;
    }

    /**
     * Get the value of materiais
     */
    public function getMateriais(): ?array
    {
        return $this->materiais;
    }

    /**
     * Set the value of materiais
     */
    public function setMateriais(?array $materiais): self
    {

        if (!empty($materiais)) {
            $index = 0;
            foreach ($materiais as $material) {
                if (!($material instanceof  MaterialMovimentacao)) throw new Exception("[ERRO][Movimentação  08] Classe de material não reconhecida no index $index!", 1);
                $index++;
            }
        }

        $this->materiais = $materiais;

        return $this;
    }

    /**
     * Get the value of tipo
     */
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    /**
     * Set the value of tipo
     */
    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get the value of unidade_utilizada
     */
    public function getUnidadeUtilizada(): ?string
    {
        return $this->unidade_utilizada;
    }

    /**
     * Set the value of unidade_utilizada
     */
    public function setUnidadeUtilizada(?string $unidade_utilizada): self
    {
        $this->unidade_utilizada = $unidade_utilizada;

        return $this;
    }

    /**
     * Get the value of fator_conversao_aplicado
     */
    public function getFatorConversaoAplicado(): ?int
    {
        return $this->fator_conversao_aplicado;
    }

    /**
     * Set the value of fator_conversao_aplicado
     */
    public function setFatorConversaoAplicado(?int $fator_conversao_aplicado): self
    {
        $this->fator_conversao_aplicado = $fator_conversao_aplicado;

        return $this;
    }

    /**
     * Get the value of quantidade_convertida
     */
    public function getQuantidadeConvertida(): ?int
    {
        return $this->quantidade_convertida;
    }

    /**
     * Set the value of quantidade_convertida
     */
    public function setQuantidadeConvertida(?int $quantidade_convertida): self
    {
        $this->quantidade_convertida = $quantidade_convertida;

        return $this;
    }

    /**
     * Get the value of ponto_solicitante
     */
    public function getPontoSolicitante(): ?string
    {
        return $this->ponto_solicitante;
    }

    /**
     * Set the value of ponto_solicitante
     */
    public function setPontoSolicitante(?string $ponto_solicitante): self
    {
        $this->ponto_solicitante = $ponto_solicitante;

        return $this;
    }

    /**
     * Get the value of nome_solicitante
     */
    public function getNomeSolicitante(): ?string
    {
        return $this->nome_solicitante;
    }

    /**
     * Set the value of nome_solicitante
     */
    public function setNomeSolicitante(?string $nome_solicitante): self
    {
        $this->nome_solicitante = $nome_solicitante;

        return $this;
    }

    /**
     * Get the value of data_movimentacao
     */
    public function getDataMovimentacao(): ?string
    {
        return $this->data_movimentacao;
    }

    /**
     * Set the value of data_movimentacao
     */
    public function setDataMovimentacao(?string $data_movimentacao): self
    {
        $this->data_movimentacao = $data_movimentacao;

        return $this;
    }
}
