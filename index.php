<?php

use CoffeeCode\Router\Router;

require __DIR__ . "/vendor/autoload.php";

$router = new Router(URL_BASE);

$router->namespace("Source\App");

/* 
    EXEMPLO ROTA

    $router->group(null);
    $router->get("/", "Form:home", "form.home"); 
    $router->get("/{FILTER}", "Form:filter", "form.contato"); 

*/

/* ROTA RAIZ */
$router->group(null);
$router->get("/", "Web:home");
$router->post("/", "Materiais:getMateriais");
$router->post("/salvarMaterial", "Materiais:salvarMaterial");
$router->post("/excluirMaterial", "Materiais:excluirMaterial");
$router->post("/criarMovimentacao", "Materiais:criarMovimentacao");
$router->post("/salvarLote", "Materiais:salvarLote");
$router->post("/excluirLote", "Materiais:excluirLote");

/* ROTA MOVIMENTAÇÃO */
$router->group("movimentacoes");
$router->get("/", "Web:movimentacoes");
$router->post("/", "Movimentacoes:getMovimentacoes");
$router->post("/excluirMaterial", "Movimentacoes:excluirMaterial");

/* ROTA USUARIO */
$router->group("usuario");
// $router->get("/", "Web:movimentacoes");
$router->post("/consultarPonto", "Usuarios:consultarPonto");

$router->group("documento");
$router->get("/comprovanteSaida", "Documentos:getComprovanteSaida");


/* ROTA DE ERRO */
$router->group("error");
$router->get("/{errcode}", "Web:error");

$router->dispatch();

if ($router->error()) {
    $router->redirect("/error/{$router->error()}");
}
