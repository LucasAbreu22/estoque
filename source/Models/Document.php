<?php

namespace Source\Models;

use Dompdf\Dompdf;

final class Document
{

    public function getComprovanteSaida()
    {
        // instantiate and use the dompdf class
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
            </style>
        </head>
        <div>
            <table>
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
        </div>
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
