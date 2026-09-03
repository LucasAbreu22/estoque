<?php

namespace Source\Models;

use Exception;
use Source\DAO\UsuarioDAO;

class Usuario
{
    private INT $id_usuario;
    private STRING $nome;
    private ?INT $ponto;
    private ?STRING $senha;
    private ?STRING $data_criacao;
    private ?STRING $data_edicao;
    private BOOL $visibilidade;

    function __construct(
        INT $id_usuario = 0,
        STRING $nome = "",
        ?INT $ponto = null,
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
    public function getPonto(): INT
    {
        return $this->ponto;
    }

    /**
     * Set the value of ponto
     */
    public function setPonto(?INT $ponto): self
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
    public function setDataCriacao(?STRING $data_criacao): self
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
    public function setDataEdicao(?STRING $data_edicao): self
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

    public function getUsuarioById()
    {

        if (empty($this->getIdUsuario())) throw new Exception("[ERRO][Usuário 03] Informação de ID vazia!", 1);

        $usuarioDAO = new UsuarioDAO();
        $data = $usuarioDAO->getUsuarioById($this->getIdUsuario());

        if (empty($data)) throw new Exception("Nenhum usuário encontrado com o ID!", 1);

        $this->setIdUsuario($data->id_usuario);
        $this->setNome($data->nome);
        $this->setPonto($data->ponto);
        $this->setSenha($data->senha);
        $this->setDataCriacao($data->data_criacao);
        $this->setDataEdicao($data->data_edicao);
        $this->setVisibilidade($data->visibilidade);
    }

    public function consultarPonto()
    {
        if (empty($this->getPonto())) throw new Exception("[ERRO][Usuário 02] Informação de PONTO vazia!", 1);
        $usuarioDAO = new UsuarioDAO();
        return $usuarioDAO->consultarPonto($this->getPonto());
    }

    public function getUsuarios(int $offset = 0, string $search = "")
    {
        $usuarioDAO = new UsuarioDAO();
        return $usuarioDAO->getUsuarios($offset, $search);
    }

    public function contarUsuarios(string $search = "")
    {
        $usuarioDAO = new UsuarioDAO();
        return $usuarioDAO->contarUsuarios($search);
    }

    public function salvarUsuario(): array
    {
        // NOME e PONTO são NOT NULL no banco -> obrigatórios.
        // SENHA é anulável -> não obrigatória (não há login por senha).
        if (empty($this->getNome())) throw new Exception("[ERRO][Usuário 04] Informação de NOME vazia!", 1);
        if (empty($this->getPonto())) throw new Exception("[ERRO][Usuário 05] Informação de PONTO vazia!", 1);

        $usuarioDAO = new UsuarioDAO();

        $usuario = [
            "id_usuario" => $this->getIdUsuario(),
            "nome" => $this->getNome(),
            "ponto" => $this->getPonto(),
            "senha" => $this->getSenha(),
        ];

        if (empty($this->getIdUsuario())) {
            $callback = $usuarioDAO->criarUsuario($usuario);
            $this->setIdUsuario((int) $callback["newId"]);

            return [
                "message" => $callback["message"],
                "id_usuario" => $this->getIdUsuario(),
                "evento" => "INSERT"
            ];
        }

        $message = $usuarioDAO->editarUsuario($usuario);

        return [
            "message" => $message,
            "id_usuario" => $this->getIdUsuario(),
            "evento" => "UPDATE"
        ];
    }

    public function excluirUsuario(): string
    {
        if (empty($this->getIdUsuario())) throw new Exception("[ERRO][Usuário 06] Informação de ID vazia!", 1);

        $usuarioDAO = new UsuarioDAO();
        return $usuarioDAO->excluirUsuario($this->getIdUsuario());
    }
}
