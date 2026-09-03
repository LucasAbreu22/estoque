<?php

namespace Source\Models;

use Source\DAO\LogDAO;

class Log
{
    /**
     * Registra uma interação de dados (criação, edição, exclusão ou movimentação).
     *
     * O log é auxiliar ao sistema: qualquer falha no registro NUNCA deve
     * interromper a operação principal que o originou.
     *
     * @param int|null      $id_usuario     Usuário responsável pela interação.
     * @param string        $tabela_afetada Tabela onde ocorreu a interação.
     * @param int           $id_registro    Identificação do registro afetado.
     * @param string        $evento         INSERT | UPDATE | DELETE | ENTRADA | SAIDA.
     * @param mixed         $valor_antigo   Estado anterior (array/obj vira JSON).
     * @param mixed         $valor_novo     Estado posterior (array/obj vira JSON).
     */
    public static function registrar(?int $id_usuario, string $tabela_afetada, int $id_registro, string $evento, $valor_antigo = null, $valor_novo = null): void
    {
        try {
            // Sem usuário identificado não há como registrar (FK obrigatória).
            if (empty($id_usuario)) return;

            $logDAO = new LogDAO();
            $logDAO->salvarLog(
                $id_usuario,
                $tabela_afetada,
                $id_registro,
                $evento,
                self::normalizarValor($valor_antigo),
                self::normalizarValor($valor_novo)
            );
        } catch (\Throwable $e) {
            // Log é auxiliar: falhas aqui são silenciadas para não afetar a operação.
        }
    }

    private static function normalizarValor($valor): ?string
    {
        if (is_null($valor)) return null;
        if (is_array($valor) || is_object($valor)) return json_encode($valor, JSON_UNESCAPED_UNICODE);

        return (string) $valor;
    }

    private INT $id_log;
    private ?Usuario $usuario;
    private INT $id_registro;
    private STRING $tabela_afetada;
    private STRING $evento;
    private STRING $valor_antigo;
    private STRING $valor_novo;
    private STRING $data_evento;


    function __construct(
        INT $id_log = 0,
        ?Usuario $usuario = null,
        INT $id_registro = 0,
        STRING $tabela_afetada = "",
        STRING $evento = "",
        STRING $valor_antigo = "",
        STRING $valor_novo = "",
        STRING $data_evento = ""
    ) {
        $this->id_log = $id_log;
        $this->usuario = $usuario;
        $this->id_registro = $id_registro;
        $this->tabela_afetada = $tabela_afetada;
        $this->evento = $evento;
        $this->valor_antigo = $valor_antigo;
        $this->valor_novo = $valor_novo;
        $this->data_evento = $data_evento;
    }

    /**
     * Get the value of id_log
     */
    public function getIdLog(): INT
    {
        return $this->id_log;
    }

    /**
     * Set the value of id_log
     */
    public function setIdLog(INT $id_log): self
    {
        $this->id_log = $id_log;

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
     * Get the value of id_registro
     */
    public function getIdRegistro(): INT
    {
        return $this->id_registro;
    }

    /**
     * Set the value of id_registro
     */
    public function setIdRegistro(INT $id_registro): self
    {
        $this->id_registro = $id_registro;

        return $this;
    }

    /**
     * Get the value of tabela_afetada
     */
    public function getTabelaAfetada(): STRING
    {
        return $this->tabela_afetada;
    }

    /**
     * Set the value of tabela_afetada
     */
    public function setTabelaAfetada(STRING $tabela_afetada): self
    {
        $this->tabela_afetada = $tabela_afetada;

        return $this;
    }

    /**
     * Get the value of evento
     */
    public function getEvento(): STRING
    {
        return $this->evento;
    }

    /**
     * Set the value of evento
     */
    public function setEvento(STRING $evento): self
    {
        $this->evento = $evento;

        return $this;
    }

    /**
     * Get the value of valor_antigo
     */
    public function getValorAntigo(): STRING
    {
        return $this->valor_antigo;
    }

    /**
     * Set the value of valor_antigo
     */
    public function setValorAntigo(STRING $valor_antigo): self
    {
        $this->valor_antigo = $valor_antigo;

        return $this;
    }

    /**
     * Get the value of valor_novo
     */
    public function getValorNovo(): STRING
    {
        return $this->valor_novo;
    }

    /**
     * Set the value of valor_novo
     */
    public function setValorNovo(STRING $valor_novo): self
    {
        $this->valor_novo = $valor_novo;

        return $this;
    }

    /**
     * Get the value of data_evento
     */
    public function getDataEvento(): STRING
    {
        return $this->data_evento;
    }

    /**
     * Set the value of data_evento
     */
    public function setDataEvento(STRING $data_evento): self
    {
        $this->data_evento = $data_evento;

        return $this;
    }
}
