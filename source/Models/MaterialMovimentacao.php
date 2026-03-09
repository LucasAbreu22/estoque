<?php

namespace Source\Models;

use Exception;

class MaterialMovimentacao
{
    private ?Material $material;
    private ?int $id_movimentacao;
    private ?Lote $lote;
    private ?int $quantidade;

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
     * Get the value of id_movimentacao
     */
    public function getIdMovimentacao(): ?int
    {
        return $this->id_movimentacao;
    }

    /**
     * Set the value of id_movimentacao
     */
    public function setIdMovimentacao(?int $id_movimentacao): self
    {
        $this->id_movimentacao = $id_movimentacao;

        return $this;
    }

    /**
     * Get the value of lote
     */
    public function getLote(): ?Lote
    {
        return $this->lote;
    }

    /**
     * Set the value of lote
     */
    public function setLote(?Lote $lote): self
    {
        $this->lote = $lote;

        return $this;
    }

    /**
     * Get the value of quantidade
     */
    public function getQuantidade(): ?int
    {
        return $this->quantidade;
    }

    /**
     * Set the value of quantidade
     */
    public function setQuantidade(?int $quantidade = 1): self
    {

        if ($quantidade < 1) throw new Exception("[ERRO][Movimentacao 04] Informação QUANTIDADE de vazia!", 1);

        $this->quantidade = $quantidade;

        return $this;
    }
}
