<?php

namespace Source\Models;

use Exception;
use Source\DAO\CategoriaDAO;

class Categoria
{
    private int $id_categoria;
    private string $nome;
    private BOOL $visibilidade;

    function __construct(
        int $id_categoria = 0,
        string $nome = "",
        BOOL $visibilidade = true
    ) {
        $this->setIdCategoria($id_categoria);
        $this->setNome($nome);
        $this->setVisibilidade($visibilidade);
    }

    /**
     * Get the value of id_categoria
     */
    public function getIdCategoria(): int
    {
        return $this->id_categoria;
    }

    /**
     * Set the value of id_categoria
     */
    public function setIdCategoria(int $id_categoria): self
    {
        if ($id_categoria < 0) throw new Exception("[ERRO][Categoria 02] Informação de ID de Categoria inválida!", 1);
        $this->id_categoria = $id_categoria;

        return $this;
    }

    /**
     * Get the value of nome
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     */
    public function setNome(string $nome): self
    {

        $this->nome = $nome;

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

    public function getAllCategorias(): array
    {
        $categoriaDAO = new CategoriaDAO();
        return $categoriaDAO->getAllMateriais();
    }

    public function getCategoriaById()
    {

        $categoriaDAO = new CategoriaDAO();
        return $categoriaDAO->getCategoriaById($this->getIdCategoria());
    }
}
