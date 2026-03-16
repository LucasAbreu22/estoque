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
        if (is_null($this->getIdMaterial())) throw new Exception("[ERRO][Lote Clss 01] ID de material nulo!", 1);

        $loteDAO = new LoteDAO();
        $lotes = $loteDAO->getLotesByMaterial($this->getIdMaterial());

        foreach ($lotes as $lote) {
            $date = new DateTime($lote["vencimento"]);
            $lote["vencimentoFormatted"] = $date->format('d/m/Y');

            $ids = array_column($lotes, 'id_lote');
            $key = array_search($lote["id_lote"], $ids);

            $lotes[$key] = $lote;
        }

        return $lotes;
    }

    public function getLoteById()
    {
        if (is_null($this->getIdLote()) || $this->getIdLote() < 0) throw new Exception("[ERRO][Lote Clss 02] ID de lote inválido!", 1);

        $loteDAO = new LoteDAO();
        return $loteDAO->getLoteById($this->getIdLote());
    }


    public function salvarLote(): ?string
    {
        $msg = null;
        $loteDAO = new LoteDAO();

        if (is_null($this->getIdLote())) {
            if (is_null($this->getIdMaterial()) || $this->getIdMaterial() < 1) throw new Exception("[ERRO][Lote Clss 03] Informação de ID de Material vazia!", 1);
            if (is_null($this->getLote()) || $this->getLote() < 1) throw new Exception("[ERRO][Lote Clss 04] Informação de LOTE inválido!", 1);
            if (is_null($this->getVencimento()) || empty($this->getVencimento())) throw new Exception("[ERRO][Lote Clss 05] Informação de VENCIMENTO vazia!", 1);
            if ($this->getQuantidade() < 1) throw new Exception("[ERRO][Lote Clss 06] Informação de QUANTIDADE inválido!", 1);

            $idLote = $loteDAO->salvarLote($this->getIdMaterial(), $this->getLote(), $this->getVencimento(), $this->getQuantidade());

            $this->setIdLote($idLote);

            $msg = "Lote criado com sucesso!";
        } else {
            $msg = $loteDAO->editarLote($this->getIdLote(), $this->getIdMaterial(), $this->getLote(), $this->getVencimento(), $this->getQuantidade());
        }

        return $msg;
    }

    public function atualizarEstoque(): string
    {
        if (is_null($this->getIdLote()) || $this->getIdLote() < 1) throw new Exception("[ERRO][Lote Clss 03] ID de lote nulo!", 1);
        if ($this->getQuantidade() < 0) throw new Exception("[ERRO][Lote Clss 04] Quantidade do lote inválida!", 1);

        $loteDAO = new LoteDAO();
        $callback = $loteDAO->atualizarEstoque($this->getIdLote(), $this->getQuantidade());
        return $callback;
    }

    public function excluirLote(): string
    {
        if (is_null($this->getIdLote())) throw new Exception("[ERRO][Lote Clss 09] Não há informação de lote informada!", 1);

        $loteDAO = new LoteDAO();
        $callback = $loteDAO->excluirLote($this->getIdLote());

        return $callback;
    }
}
