<?php

namespace Source\Models;

use DateTime;
use Exception;
use Source\DAO\LoteDAO;

class Lote
{
    private ?int $id_lote;
    private ?int $id_material;
    private ?int $lote;
    private ?string $vencimento;
    private int $quantidade;

    function __construct(
        ?int $id_lote = null,
        ?int $id_material = null,
        ?int $lote = null,
        ?string $vencimento = null,
        int $quantidade = 0
    ) {

        $this->setIdLote($id_lote);
        $this->setIdMaterial($id_material);
        $this->setLote($lote);
        $this->setVencimento($vencimento);
        $this->setQuantidade($quantidade);
    }

    /**
     * Get the value of id_lote
     */
    public function getIdLote(): ?int
    {
        return $this->id_lote;
    }

    /**
     * Set the value of id_lote
     */
    public function setIdLote(?int $id_lote): self
    {
        $this->id_lote = $id_lote;

        return $this;
    }

    /**
     * Get the value of id_material
     */
    public function getIdMaterial(): ?int
    {
        return $this->id_material;
    }

    /**
     * Set the value of id_material
     */
    public function setIdMaterial(?int $id_material): self
    {
        $this->id_material = $id_material;

        return $this;
    }

    /**
     * Get the value of lote
     */
    public function getLote(): ?int
    {
        return $this->lote;
    }

    /**
     * Set the value of lote
     */
    public function setLote(?int $lote): self
    {
        $this->lote = $lote;

        return $this;
    }

    /**
     * Get the value of vencimento
     */
    public function getVencimento(): ?string
    {
        return $this->vencimento;
    }

    /**
     * Set the value of vencimento
     */
    public function setVencimento(?string $vencimento): self
    {
        $this->vencimento = $vencimento;

        return $this;
    }

    /**
     * Get the value of quantidade
     */
    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    /**
     * Set the value of quantidade
     */
    public function setQuantidade(int $quantidade): self
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    public function getLotesByMaterial()
    {
        if (is_null($this->getIdMaterial())) throw new Exception("[ERRO 01][Lote Clss] ID de material nulo!", 1);

        $loteDAO = new LoteDAO();
        $lotes = $loteDAO->getLotesByMaterial($this->getIdMaterial());

        foreach ($lotes as $lote) {
            $date = new DateTime($lote["vencimento"]);
            $lote["vencimento"] = $date->format('d/m/Y');

            $ids = array_column($lotes, 'id_material');
            $key = array_search($lote["id_material"], $ids);
            $lotes[$key] = $lote;
        }

        return $lotes;
    }
}
