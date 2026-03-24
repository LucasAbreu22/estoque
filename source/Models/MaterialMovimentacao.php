<?php

namespace Source\Models;

use Exception;
use Source\DAO\LoteDAO;
use Source\DAO\MaterialMovimentacaoDAO;

use function PHPSTORM_META\type;

class MaterialMovimentacao
{
    private ?Material $material;
    private ?int $id_movimentacao;
    private ?Lote $lote;
    private ?int $quantidade;

    public function __construct()
    {
        $this->setMaterial(null);
        $this->setIdMovimentacao(null);
        $this->setLote(null);
        $this->setQuantidade(null);
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

        $this->quantidade = $quantidade;

        return $this;
    }

    public function salvarMaterialMov(): string
    {
        if (is_null($this->getIdMovimentacao()) || $this->getIdMovimentacao() < 1) throw new Exception("[ERRO][Material Movimentacao 02] Número de ID inválido!", 1);
        if (is_null($this->getMaterial())) throw new Exception("[ERRO][Material Movimentacao 03] Não tem MATERIAL informado!", 1);
        if (is_null($this->getLote())) throw new Exception("[ERRO][Material Movimentacao 03] Não tem LOTE informado!", 1);
        if (is_null($this->getQuantidade()) || $this->getQuantidade() < 1) throw new Exception("[ERRO][Material Movimentacao 04] Informação QUANTIDADE de vazia!", 1);

        $materialMovDAO = new MaterialMovimentacaoDAO();
        $callback  = $materialMovDAO->salvarMaterialMov($this->getIdMovimentacao(), $this->getMaterial()->getIdMaterial(), $this->getLote()->getIdLote(), $this->getQuantidade());

        return $callback;
    }

    public function getMateriaisByMovimentacao(): array
    {
        if (is_null($this->getIdMovimentacao()) || $this->getIdMovimentacao() < 1) throw new Exception("[ERRO][Material Movimentação Clss 06] Informação de ID de movimentação inválido!", 1);

        $materialMovDAO = new MaterialMovimentacaoDAO();
        $callback = $materialMovDAO->getMateriaisByMovimentacao($this->getIdMovimentacao());

        return $callback;
    }

    public function excluirMaterial(): string
    {

        try {
            if (is_null($this->getMaterial()) || is_null($this->getMaterial()->getIdMaterial()) || $this->getMaterial()->getIdMaterial() < 1) throw new Exception("[ERRO][Material Movimentação Clss 07] Sem informação de Material informada!", 1);
            if (is_null($this->getLote()) || is_null($this->getLote()->getIdLote()) || $this->getMaterial()->getIdMaterial() < 1) throw new Exception("[ERRO][Material Movimentação Clss 08] Sem informação de Lote informada!", 1);
            if (is_null($this->getIdMovimentacao()) || $this->getIdMovimentacao() < 1) throw new Exception("[ERRO][Material Movimentação Clss 09] Informação de  ID de Movimentação não informada!", 1);

            $movimentacaoObj = new MovimentacaoEstoque();
            $movimentacaoObj->setIdMovimentacao($this->getIdMovimentacao());

            $movimentacao = $movimentacaoObj->getMovimentacaoById();

            $materiaisMov = $this->getMateriaisByMovimentacao();

            $materialMovDAO = new MaterialMovimentacaoDAO();
            $materialMovDAO->beginTransaction();

            $callback = $materialMovDAO->excluirMaterial($this->getMaterial()->getIdMaterial(), $this->getLote()->getIdLote(), $this->getIdMovimentacao());

            $lote = $this->getLote()->getLoteById();

            if ($movimentacao->tipo === "ENTRADA") {

                $this->getLote()->excluirLote();

                if (count($materiaisMov) === 1) {
                    $movimentacaoObj->excluirMovimentacao();
                }
            } else {
                $this->getLote()->setQuantidade($lote["quantidade"] + $this->getQuantidade());
                $this->getLote()->atualizarEstoque();
            }

            // $materialMovDAO->rollBack();
            $materialMovDAO->commit();

            return $callback;
        } catch (\Throwable $e) {
            $materialMovDAO->rollBack();
            throw new Exception($e->getMessage(), 1);
        }
    }
}
