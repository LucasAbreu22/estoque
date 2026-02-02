<?php

namespace Source\App;

use League\Plates\Engine;
use Source\Models\Categoria;
use Source\Models\Material;
use Source\Models\Usuario;

class Web
{
    private $view;

    public function __construct($router)
    {
        $this->view = new Engine(dirname(__DIR__, 2) . '/theme', "php");
        $this->view->addData(["router" => $router]);
    }

    public function home(): void
    {
        try {
            $categoria = new Categoria();
            echo $this->view->render("home", [
                "title" => "Gerenciamento Estoque",
                "categorias" => $categoria->getAllCategorias()
            ]);
        } catch (\Throwable $e) {
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    public function movimentacoes(): void
    {
        try {

            echo $this->view->render("movimentacoes", [
                "title" => "Movimentações Estoque",
            ]);
        } catch (\Throwable $e) {
            echo json_encode(["message" => $e->getMessage()]);
        }
    }

    public function error($error): void
    {
        try {

            $errosRedirecionamento = [
                300 => "<b>Múltiplas escolhas:</b> O servidor oferece várias opções de resposta para a requisição.",
                301 => "<b>Movido permanentemente:</b> A página foi movida permanentemente para um novo endereço.",
                302 => ":<b>Encontrado (Movido temporariamente):</b> A página foi movida temporariamente para outro endereço.",
                303 => "<b>Ver outros:</b> A resposta para a requisição pode ser encontrada em outro URI usando o método GET.",
                304 => "<b>Não modificado:</b> Indica que a página não foi modificada desde a última solicitação.",
                305 => "<b>Use proxy:</b> O acesso ao recurso deve ser feito através de um proxy.",
                307 => "<b>Redirecionamento temporário:</b> O recurso foi movido temporariamente para outro URI.",
                308 => "<b>Redirecionamento permanente:</b> O recurso foi movido permanentemente para outro URI.",

                // Erros de cliente
                400 => "<b>Requisição inválida:</b> O servidor não entendeu a requisição devido a sintaxe inválida.",
                401 => "<b>Não autorizado:</b> Autenticação é necessária para acessar o recurso.",
                403 => "<b>Proibido:</b> O servidor recusou a requisição.",
                404 => "<b>Não encontrado:</b> O recurso solicitado não foi encontrado no servidor.",
                405 => "<b>Método não permitido:</b> O método de requisição não é suportado para este recurso.",
                408 => "<b>Tempo de requisição esgotado:</b> O servidor encerrou a conexão devido à inatividade.",

                // Erros de servidor
                500 => "<b>Erro interno do servidor:</b> O servidor encontrou uma situação com a qual não sabe lidar.",
                502 => "<b>Bad Gateway:</b> O servidor, ao atuar como gateway, recebeu uma resposta inválida.",
                503 => "<b>Serviço indisponível:</b> O servidor não está pronto para manipular a requisição.",
                504 => "<b>Gateway Timeout:</b> O servidor, ao atuar como gateway, não obteve resposta a tempo.",
                505 => "<b>Versão HTTP não suportada:</b> O servidor não suporta a versão do protocolo HTTP utilizada."
            ];

            echo $this->view->render("error", [
                "title" => "ERRO",
                "error" => $error,
                "erroMsg" => $errosRedirecionamento[$error["errcode"]]
            ]);
        } catch (\Throwable $e) {
            echo json_encode(["message" => $e->getMessage()]);
        }
    }
}
