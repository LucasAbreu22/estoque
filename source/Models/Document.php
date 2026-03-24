<?php

namespace Source\Models;

use Dompdf\Dompdf;
use Exception;

final class Document
{

    public function getComprovanteSaida(int $id_movimentacao = 0)
    {

        if ($id_movimentacao < 1) throw new Exception("[ERRO][Document Clss 01] Informação de ID de movimentação inválida!", 1);

        $movimentacaoObj = new MovimentacaoEstoque();
        $movimentacaoObj->setIdMovimentacao($id_movimentacao);

        $movimentacao = $movimentacaoObj->getMovimentacaoById();

        if (empty($movimentacao)) throw new Exception("[ERRO][Document Clss 02] Nenhuma movimentação encontrada!", 1);

        $materialMovimentacaoObj = new MaterialMovimentacao();
        $materialMovimentacaoObj->setIdMovimentacao($movimentacao->id_movimentacao);

        $materiais = $materialMovimentacaoObj->getMateriaisByMovimentacao();

        if (empty($materiais)) throw new Exception("[ERRO][Document Clss 03] Nenhum material encontrado!", 1);

        $usuarioObj = new Usuario();
        $usuarioObj->setIdUsuario($movimentacao->id_usuario);
        $usuarioObj->getUsuarioById();

        $formatedDate = $movimentacao->data_movimentacao;
        $formatedDate = date('d/m/Y H:i', strtotime($formatedDate));

        $dompdf = new Dompdf(['enable_remote' => true]);

        $html = '
        <head>
            <style>
                @font-face{
                    font-family: "stone-sans"
                    src: local("MyCustomFont"), 
                    url(' . url("fonts/Stone Sans Regular.ttf") . ') format("ttf"),
                    font-weight: normal;
                    font-style: normal;
                    font-display: swap; /* Recommended to prevent render-blocking */
                }

                *{
                    font-family: "stone-sans", sans-serif;
                }

                #cabecalho{
                    width: auto !important;
                }

                #cabecalho,
                #cabecalho td,
                #cabecalho tr,
                #cabecalho th{
                    border: none !important;
                    font-size: 16px;
                }

                table{
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 14px;
                }

                #materiais,
                #materiais td,
                #materiais tr,
                #materiais th{
                    border: solid 1px #000;
                }
                
                table tbody td{
                    padding-left: 5px;
                }

                th{
                    padding: 2px;
                }

                #assinatura{
                    margin: 0 auto;
                    position: fixed;
                    left: 0;
                    bottom: 10;
                }

                #assinatura,
                #assinatura td,
                #assinatura tr,
                #assinatura th{
                    border: none !important;
                }
                
                #assinatura span,
                p{
                    font-size: 14px;
                }

                .center{
                    text-align: center;
                }

                #assinatura div{
                    width: 70%;
                    margin: 0 auto;
                    background-color: #000;
                }
            </style>
        </head>

            <table id="cabecalho">
                <tbody>
                    <tr>
                        <td rowspan="2">
                            <img src="' . url("/theme/assets/img/brasao.jpg") . '" style="width:80px" alt="" srcset="" alt="logo_camara"/>
                        </td>
                        <td>
                            <span>CÂMARA DOS DEPUTADOS</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <h2>Requisição: #' . $movimentacao->id_movimentacao . '</h2>

            <p><b>Data da requisição:</b> ' . $formatedDate . '</p>
            
            <fieldset>
                <legend>Reponsável</legend>

            <table>
                <tbody>
                    <tr>
                        <td style="width: 30%">
                            <b>Nome: </b><span>' . $usuarioObj->getNome() . '</span>
                        </td>
                        <td>
                            <b>Ponto: </b><span>P_' . $usuarioObj->getPonto() . '</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </fieldset>
            
            <fieldset>
                <legend>Solicitante</legend>
                <table>
                    <tbody>
                        <tr>
                            <td style="width: 30%">
                                <b>Nome: </b><span>' . $movimentacao->nome_solicitante . '</span>
                            </td>
                            <td>
                                <b>Ponto: </b><span>P_' . $movimentacao->ponto_solicitante . '</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            
            <h3>Materiais</h3>

            <table id="materiais">
                <thead>
                    <th style="width: 8%">
                        Item
                    </th>
                    <th style="width: 12%">
                        Código
                    </th>
                    <th>
                        Descrição
                    </th>
                    <th style="width: 15%">
                    Lote
                    </th>
                    <th style="width: 15%">
                        Unidade
                    </th>
                    <th style="width: 10%">
                        Quantidade
                    </th>
                </thead>
                <tbody>';

        $idx = 1;
        foreach ($materiais as $material) {
            $html .= '
                        <tr>
                            <td class="center">
                                <span>' . $idx . '</span>
                            </td>
                            <td>
                                <span>' . $material->codigo . '</span>
                            </td>
                            <td>
                                <span>' . $material->descricao . '</span>
                            </td>
                            <td>
                                <span>' . $material->lote . '</span>
                            </td>
                            <td>
                                <span>' . $material->unidade_base . '</span>
                            </td>
                            <td>
                                <span>' . $material->quantidade . '</span>
                            </td>
                        </tr>';

            $idx++;
        }

        $html .= '
                </tbody>
            </table>

            <table id="assinatura">
                <tr>
                    <td style="width: 50%">
                        <div>
                        </div>
                    </td>
                    <td>
                        <div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="center">
                        <span>
                            Responsável
                        </span>
                    </td>
                    <td class="center">
                        <span>
                            Solicitante
                        </span>
                    </td>
                </tr>
            </table>
        ';
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream("documento.pdf", array("Attachment" => 0));
    }
}
