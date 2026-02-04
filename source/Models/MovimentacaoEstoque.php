<?php

namespace Source\Models;

use DateTime;
use Exception;
use Source\DAO\MovimentacaoEstoqueDAO;

class MovimentacaoEstoque
{
    private  ?INT $id_movimentacao;
    private  ?INT $codigo_sigma;
    private  ?Material $material;
    private  ?Usuario $usuario;
    private  STRING $tipo;
    private  INT $quantidade;
    private  STRING $unidade_utilizada;
    private  ?INT $fator_conversao_aplicado;
    private  INT $quantidade_convertida;
    private  STRING $data_movimentacao;
    private  ?STRING $pontoSolicitante;
    private  ?STRING $nomeSolicitante;


    function __construct(
        ?INT $id_movimentacao = null,
        ?INT $codigo_sigma = null,
        ?Material $material = null,
        ?Usuario $usuario = null,
        STRING $tipo = "",
        INT $quantidade = 0,
        STRING $unidade_utilizada = "BASE",
        ?INT $fator_conversao_aplicado = 0,
        INT $quantidade_convertida = 0,
        STRING $data_movimentacao = "",
        ?STRING $pontoSolicitante = null,
        ?STRING $nomeSolicitante = null
    ) {

        $this->setIdMovimentacao($id_movimentacao);
        $this->setCodigoSigma($id_movimentacao);
        $this->setMaterial($material);
        $this->setUsuario($usuario);
        $this->setTipo($tipo);
        $this->setQuantidade($quantidade);
        $this->setUnidadeUtilizada($unidade_utilizada);
        $this->setFatorConversaoAplicado($fator_conversao_aplicado);
        $this->setQuantidadeConvertida($quantidade_convertida);
        $this->setDataMovimentacao($data_movimentacao);
        $this->setPontoSolicitante($pontoSolicitante);
        $this->setNomeSolicitante($nomeSolicitante);
    }

    /**
     * Get the value of id_movimentacao
     */
    public function getIdMovimentacao(): ?INT
    {
        return $this->id_movimentacao;
    }

    /**
     * Set the value of id_movimentacao
     */
    public function setIdMovimentacao(?INT $id_movimentacao): self
    {
        $this->id_movimentacao = $id_movimentacao;

        return $this;
    }

    /**
     * Get the value of codigo_sigma
     */
    public function getCodigoSigma(): ?INT
    {
        return $this->codigo_sigma;
    }

    /**
     * Set the value of codigo_sigma
     */
    public function setCodigoSigma(?INT $codigo_sigma): self
    {
        $this->codigo_sigma = $codigo_sigma;

        return $this;
    }

    /**
     * Get the value of material
     */
    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    /**
     * Set the value of material
     */
    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

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
     * Get the value of tipo
     */
    public function getTipo(): STRING
    {
        return $this->tipo;
    }

    /**
     * Set the value of tipo
     */
    public function setTipo(STRING $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get the value of quantidade
     */
    public function getQuantidade(): INT
    {
        return $this->quantidade;
    }

    /**
     * Set the value of quantidade
     */
    public function setQuantidade(INT $quantidade): self
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    /**
     * Get the value of unidade_utilizada
     */
    public function getUnidadeUtilizada(): STRING
    {
        return $this->unidade_utilizada;
    }

    /**
     * Set the value of unidade_utilizada
     */
    public function setUnidadeUtilizada(STRING $unidade_utilizada): self
    {
        $this->unidade_utilizada = $unidade_utilizada;

        return $this;
    }

    /**
     * Get the value of fator_conversao_aplicado
     */
    public function getFatorConversaoAplicado(): ?INT
    {
        return $this->fator_conversao_aplicado;
    }

    /**
     * Set the value of fator_conversao_aplicado
     */
    public function setFatorConversaoAplicado(?INT $fator_conversao_aplicado): self
    {
        $this->fator_conversao_aplicado = $fator_conversao_aplicado;

        return $this;
    }

    /**
     * Get the value of quantidade_convertida
     */
    public function getQuantidadeConvertida(): INT
    {
        return $this->quantidade_convertida;
    }

    /**
     * Set the value of quantidade_convertida
     */
    public function setQuantidadeConvertida(INT $quantidade_convertida): self
    {
        $this->quantidade_convertida = $quantidade_convertida;

        return $this;
    }

    /**
     * Get the value of data_movimentacao
     */
    public function getDataMovimentacao(): STRING
    {
        return $this->data_movimentacao;
    }

    /**
     * Set the value of data_movimentacao
     */
    public function setDataMovimentacao(STRING $data_movimentacao): self
    {
        $this->data_movimentacao = $data_movimentacao;

        return $this;
    }

    /**
     * Get the value of pontoSolicitante
     */
    public function getPontoSolicitante(): ?STRING
    {
        return $this->pontoSolicitante;
    }

    /**
     * Set the value of pontoSolicitante
     */
    public function setPontoSolicitante(?STRING $pontoSolicitante): self
    {
        $this->pontoSolicitante = $pontoSolicitante;

        return $this;
    }

    /**
     * Get the value of nomeSolicitante
     */
    public function getNomeSolicitante(): ?STRING
    {
        return $this->nomeSolicitante;
    }

    /**
     * Set the value of nomeSolicitante
     */
    public function setNomeSolicitante(?STRING $nomeSolicitante): self
    {
        $this->nomeSolicitante = $nomeSolicitante;

        return $this;
    }

    public function getMovimentacoes(int $offset = 0)
    {

        $movimentacaoDAO = new MovimentacaoEstoqueDAO();
        $movimentacoes = $movimentacaoDAO->getMovimentacoes($offset);

        foreach ($movimentacoes as $movimentacao) {
            $date = new DateTime($movimentacao->data_movimentacao);
            $movimentacao->data_movimentacao = $date->format('d/m/Y H:i:s');
        }

        return $movimentacoes;
    }

    public function contarMovimentacoes()
    {
        $movimentacaoDAO = new MovimentacaoEstoqueDAO();
        return $movimentacaoDAO->contarMovimentacoes();
    }


    public function criarMovimentacao()
    {
        if (empty($this->getMaterial()) || is_null($this->getMaterial())) throw new Exception("[ERRO][Movimentacao 01] Informação MATERIAL de vazia!", 1);
        if (empty($this->getUsuario()) || is_null($this->getUsuario())) throw new Exception("[ERRO][Movimentacao 02] Informação USUÁRIO de vazia!", 1);

        if (empty($this->getTipo())) throw new Exception("[ERRO][Movimentacao 03] Informação TIPO de vazia!", 1);

        if (empty($this->getQuantidade()) || $this->getQuantidade() < 1) throw new Exception("[ERRO][Movimentacao 04] Informação QUANTIDADE de vazia!", 1);

        if (empty($this->getUnidadeUtilizada())) throw new Exception("[ERRO][Movimentacao 05] Informação UNIDADE de vazia!", 1);

        if ($this->getTipo() === "SAIDA" && $this->getMaterial()->getQuantidade() === 0) throw new Exception("[ERRO][Movimentacao 06] Material sem estoque!", 1);
        if ($this->getTipo() === "SAIDA" && $this->getQuantidade() > $this->getMaterial()->getQuantidade()) throw new Exception("[ERRO][Movimentacao 07] Quantidade maior que há no estoque!", 1);

        $material = $this->getMaterial();

        $novoEstoque = 0;

        if ($this->getTipo() === "ENTRADA") $novoEstoque = $material->getQuantidade() + $this->getQuantidade();
        else $novoEstoque = $material->getQuantidade() - $this->getQuantidade();

        $movimentacaoDAO =  new MovimentacaoEstoqueDAO();
        $movimentacao = [
            "id_material" => $material->getIdMaterial(),
            "id_usuario" => $this->getUsuario()->getIdUsuario(),
            "codigoSigma" => $this->getCodigoSigma(),
            "tipo" => $this->getTipo(),
            "quantidade" => $this->getQuantidade(),
            "quantidade_convertida" => $this->getQuantidadeConvertida(),
            "ponto_solicitante" => $this->getPontoSolicitante(),
            "nome_solicitante" => $this->getNomeSolicitante(),
        ];

        $callback = $movimentacaoDAO->criarMovimentacao($movimentacao);

        $material->setQuantidade($novoEstoque);
        $material->salvarMaterial();

        return $callback;
    }
}
