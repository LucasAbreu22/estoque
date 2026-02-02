<?php

namespace Source\Models;

use Exception;
use Source\DAO\UsuarioDAO;

class Usuario
{
    private INT $id_usuario;
    private STRING $nome;
    private STRING $ponto;
    private ?STRING $senha;
    private STRING $data_criacao;
    private STRING $data_edicao;
    private BOOL $visibilidade;

    function __construct(
        INT $id_usuario = 0,
        STRING $nome = "",
        STRING $ponto = "",
        ?STRING $senha = null,
        STRING $data_criacao = "",
        STRING $data_edicao = "",
        BOOL $visibilidade = false
    ) {
        $this->id_usuario = $id_usuario;
        $this->nome = $nome;
        $this->ponto = $ponto;
        $this->senha = $senha;
        $this->data_criacao = $data_criacao;
        $this->data_edicao = $data_edicao;
        $this->visibilidade = $visibilidade;
    }

    /**
     * Get the value of id_usuario
     */
    public function getIdUsuario(): INT
    {
        return $this->id_usuario;
    }

    /**
     * Set the value of id_usuario
     */
    public function setIdUsuario(INT $id_usuario): self
    {
        $this->id_usuario = $id_usuario;

        return $this;
    }

    /**
     * Get the value of nome
     */
    public function getNome(): STRING
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     */
    public function setNome(STRING $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of ponto
     */
    public function getPonto(): STRING
    {
        return $this->ponto;
    }

    /**
     * Set the value of ponto
     */
    public function setPonto(STRING $ponto): self
    {
        $this->ponto = $ponto;

        return $this;
    }

    /**
     * Get the value of senha
     */
    public function getSenha(): ?STRING
    {
        return $this->senha;
    }

    /**
     * Set the value of senha
     */
    public function setSenha(?STRING $senha): self
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Get the value of data_criacao
     */
    public function getsetDataCriacao(): STRING
    {
        return $this->data_criacao;
    }

    /**
     * Set the value of data_criacao
     */
    public function setDataCriacao(STRING $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    /**
     * Get the value of data_edicao
     */
    public function getDataEdicao(): STRING
    {
        return $this->data_edicao;
    }

    /**
     * Set the value of data_edicao
     */
    public function setDataEdicao(STRING $data_edicao): self
    {
        $this->data_edicao = $data_edicao;

        return $this;
    }

    /**
     * Get the value of visibilidade
     */
    public function getVisibilidade(): BOOL
    {
        return $this->visibilidade;
    }

    /**
     * Set the value of visibilidade
     */
    public function setVisibilidade(BOOL $visibilidade): self
    {
        $this->visibilidade = $visibilidade;

        return $this;
    }

    public function getUsuarioByPonto()
    {

        if (empty($this->getPonto())) throw new Exception("[ERRO][Usuário 01] Informação de PONTO vazia!", 1);

        $usuarioDAO = new UsuarioDAO();
        $data = $usuarioDAO->getUsuarioByPonto($this->getPonto());

        if (empty($data)) throw new Exception("Nenhum usuário encontrado com o PONTO!", 1);

        $this->setIdUsuario($data->id_usuario);
        $this->setNome($data->nome);
        $this->setPonto($data->ponto);
        $this->setSenha($data->senha);
        $this->setDataCriacao($data->data_criacao);
        $this->setDataEdicao($data->data_edicao);
        $this->setVisibilidade($data->visibilidade);
    }
}
