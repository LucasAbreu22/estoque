<?php

namespace Source\Models;

use Exception;
use Source\DAO\MaterialDAO;
use Throwable;

use function PHPSTORM_META\type;

class Material
{
    private  ?INT $id_material;
    private  ?Categoria $categoria;
    private  STRING $codigo;
    private  STRING $descricao;
    private  INT $quantidade;
    private  STRING $unidade_base;
    private  STRING $unidade_compra;
    private  STRING $fator_conversao;
    private  INT $quantidade_minima;
    private  ?FLOAT $custo_unitario;
    private  STRING $localizacao;
    private  STRING $data_cricao;
    private  STRING $data_edicao;
    private  BOOL $visibilidade;

    function __construct(
        ?INT $id_material = null,
        ?Categoria $categoria = null,
        STRING $codigo = "",
        STRING $descricao = "",
        INT $quantidade = 0,
        STRING $unidade_base = "",
        STRING $unidade_compra = "",
        STRING $fator_conversao = "",
        INT $quantidade_minima = 0,
        ?FLOAT $custo_unitario = 0.0,
        STRING $localizacao = "",
        STRING $data_cricao = "",
        STRING $data_edicao = "",
        BOOL $visibilidade = true
    ) {
        $this->setIdMaterial($id_material);
        $this->setCategoria($categoria);
        $this->setCodigo($codigo);
        $this->setDescricao($descricao);
        $this->setQuantidade($quantidade);
        $this->setUnidadeBase($unidade_base);
        $this->setUnidadeCompra($unidade_compra);
        $this->setFatorConversao($fator_conversao);
        $this->setQuantidadeMinima($quantidade_minima);
        $this->setCustoUnitario($custo_unitario);
        $this->setLocalizacao($localizacao);
        $this->setDataCricao($data_cricao);
        $this->setDataEdicao($data_edicao);
        $this->setVisibilidade($visibilidade);
    }

    /**
     * Get the value of id_material
     */
    public function getIdMaterial(): ?INT
    {
        return $this->id_material;
    }

    /**
     * Set the value of id_material
     */
    public function setIdMaterial(?INT $id_material): self
    {

        if (!is_null($id_material))
            if ($id_material < 1) throw new Exception("[ERRO][Material 02] Informação de ID de Material inválida!", 1);

        $this->id_material = $id_material;

        return $this;
    }

    /**
     * Get the value of categoria
     */
    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    /**
     * Set the value of categoria
     */
    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get the value of codigo
     */
    public function getCodigo(): STRING
    {
        return $this->codigo;
    }

    /**
     * Set the value of codigo
     */
    public function setCodigo(STRING $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get the value of descricao
     */
    public function getDescricao(): STRING
    {
        return $this->descricao;
    }

    /**
     * Set the value of descricao
     */
    public function setDescricao(STRING $descricao): self
    {

        $this->descricao = $descricao;

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
        if ($quantidade < 0) throw new Exception("[ERRO][Material 06] Informação de QUANTIDADE inválida!", 1);

        $this->quantidade = $quantidade;

        return $this;
    }

    /**
     * Get the value of unidade_base
     */
    public function getUnidadeBase(): STRING
    {
        return $this->unidade_base;
    }

    /**
     * Set the value of unidade_base
     */
    public function setUnidadeBase(STRING $unidade_base): self
    {

        $this->unidade_base = $unidade_base;

        return $this;
    }

    /**
     * Get the value of unidade_compra
     */
    public function getUnidadeCompra(): STRING
    {
        return $this->unidade_compra;
    }

    /**
     * Set the value of unidade_compra
     */
    public function setUnidadeCompra(STRING $unidade_compra): self
    {

        $this->unidade_compra = $unidade_compra;

        return $this;
    }

    /**
     * Get the value of fator_conversao
     */
    public function getFatorConversao(): STRING
    {
        return $this->fator_conversao;
    }

    /**
     * Set the value of fator_conversao
     */
    public function setFatorConversao(STRING $fator_conversao): self
    {
        $this->fator_conversao = $fator_conversao;

        return $this;
    }

    /**
     * Get the value of quantidade_minima
     */
    public function getQuantidadeMinima(): INT
    {
        return $this->quantidade_minima;
    }

    /**
     * Set the value of quantidade_minima
     */
    public function setQuantidadeMinima(INT $quantidade_minima): self
    {
        $this->quantidade_minima = $quantidade_minima;

        return $this;
    }

    /**
     * Get the value of custo_unitario
     */
    public function getCustoUnitario(): ?FLOAT
    {
        return $this->custo_unitario;
    }

    /**
     * Set the value of custo_unitario
     */
    public function setCustoUnitario(?FLOAT $custo_unitario): self
    {
        if ($custo_unitario < 0) throw new Exception("[ERRO][Material 12] Informação de CUSTO UNITÁRIO vazia!", 1);
        $this->custo_unitario = $custo_unitario;

        return $this;
    }

    /**
     * Get the value of localizacao
     */
    public function getLocalizacao(): STRING
    {
        return $this->localizacao;
    }

    /**
     * Set the value of localizacao
     */
    public function setLocalizacao(STRING $localizacao): self
    {
        $this->localizacao = $localizacao;

        return $this;
    }

    /**
     * Get the value of data_cricao
     */
    public function getDataCricao(): STRING
    {
        return $this->data_cricao;
    }

    /**
     * Set the value of data_cricao
     */
    public function setDataCricao(STRING $data_cricao): self
    {
        $this->data_cricao = $data_cricao;

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

    public function getMateriais(int $offset = 0, string $search = "", bool $fltrStatusNormal = false, bool $fltrStatusAcabando = false, bool $fltrStatusSemEstoque = false)
    {

        $materialDAO = new MaterialDAO();
        return $materialDAO->getMateriais($offset, $search, $fltrStatusNormal, $fltrStatusAcabando, $fltrStatusSemEstoque);
    }

    public function contarMateriais(string $search = "", bool $fltrStatusNormal = false, bool $fltrStatusAcabando = false, bool $fltrStatusSemEstoque = false)
    {
        $materialDAO = new MaterialDAO();
        return $materialDAO->contarMateriais($search, $fltrStatusNormal, $fltrStatusAcabando, $fltrStatusSemEstoque);
    }

    public function salvarMaterial(): string
    {

        if (is_null($this->getCategoria())) throw new Exception("[ERRO][Material 13] Informação de CATEGORIA vazia!", 1);
        if (empty($this->getCodigo())) throw new Exception("[ERRO][Material 14] Informação de CÓDIGO vazia!", 1);
        if (empty($this->getDescricao())) throw new Exception("[ERRO][Material 15] Informação de DESCRIÇÃO vazia!", 1);
        if (empty($this->getUnidadeBase())) throw new Exception("[ERRO][Material 16] Informação de UNIDADE BASE vazia!", 1);
        if (empty($this->getUnidadeCompra())) throw new Exception("[ERRO][Material 16] Informação de UNIDADE DE COMPRA vazia!", 1);
        if (empty($this->getFatorConversao())) throw new Exception("[ERRO][Material 16] Informação de FATOR DE CONVERSÃO vazia!", 1);

        $materialDAO = new MaterialDAO();

        $material = [
            "id_material" => $this->getIdMaterial(),
            "codigo" => $this->getCodigo(),
            "descricao" => $this->getDescricao(),
            "quantidade" => $this->getQuantidade(),
            "unidade_base" => $this->getUnidadeBase(),
            "unidade_compra" => $this->getUnidadeCompra(),
            "fator_conversao" => $this->getFatorConversao(),
            "quantidade_minima" => $this->getQuantidadeMinima(),
            "custo_unitario" => $this->getCustoUnitario(),
            "localizacao" => $this->getLocalizacao(),
            "id_categoria" => $this->getCategoria()->getIdCategoria()
        ];

        if (is_null($this->getIdMaterial())) {
            $callback = $materialDAO->criarMaterial($material);

            $this->setIdMaterial($callback["newId"]);

            return $callback["message"];
        } else  return $materialDAO->editarMaterial($material);
    }

    public function excluirMaterial(): string
    {

        if (is_null($this->getIdMaterial()) || empty($this->getIdMaterial())) {
            throw new Exception("[ERRO][Materiais 02] Informação vazia ou inválida de MATERIAL!");
        }

        $materialDAO = new MaterialDAO();
        return $materialDAO->excluirMaterial($this->getIdMaterial());
    }

    public function getMaterialById()
    {
        if (is_null($this->getIdMaterial()) || empty($this->getIdMaterial())) {
            throw new Exception("[ERRO][Materiais 02] Informação vazia ou inválida de MATERIAL!");
        }

        $categoriaDAO = new MaterialDAO();
        $data = $categoriaDAO->getMaterialById($this->getIdMaterial());

        if (empty($data)) throw new Exception("Nenhum material encontrado com o ID!", 1);


        $categoria = new Categoria($data->id_categoria);
        $categoria->getCategoriaById();

        $this->setIdMaterial($data->id_material);
        $this->setCodigo($data->codigo);
        $this->setDescricao($data->descricao);
        $this->setQuantidade($data->quantidade);
        $this->setUnidadeBase($data->unidade_base);
        $this->setUnidadeCompra($data->unidade_compra);
        $this->setFatorConversao($data->fator_conversao);
        $this->setQuantidadeMinima($data->quantidade_minima);
        $this->setCustoUnitario($data->custo_unitario);
        $this->setLocalizacao($data->localizacao);
        $this->setCategoria($categoria);
    }
}
