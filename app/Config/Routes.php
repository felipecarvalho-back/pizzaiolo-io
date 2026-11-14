<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'HomeController::index');

// Rotas do Carrinho & HTMX
$routes->group('carrinho', static function ($routes) {
    $routes->get('drawer', 'CarrinhoController::drawer');
    $routes->get('contador', 'CarrinhoController::contador');
    $routes->get('modal-pizza/(:num)', 'CarrinhoController::modalPizza/$1');
    $routes->get('status-pedido/(:any)', 'CarrinhoController::statusPedido/$1');
    $routes->post('adicionar', 'CarrinhoController::adicionar');
    $routes->post('quantidade/(:num)', 'CarrinhoController::alterarQuantidade/$1');
    $routes->post('remover/(:num)', 'CarrinhoController::remover/$1');
    $routes->post('finalizar', 'CarrinhoController::finalizar');
});

// Rotas de Clientes (Área do Cliente)
$routes->group('cliente', static function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::autenticar');
    $routes->get('cadastro', 'ClienteController::cadastro');
    $routes->post('cadastro', 'ClienteController::registrar');
    $routes->get('perfil', 'ClienteController::perfil');
    $routes->post('perfil', 'ClienteController::atualizarPerfil');
    $routes->get('logout', 'ClienteController::logout');
});

// Atalho rápido de cadastro
$routes->get('cadastro', 'ClienteController::cadastro');

// Portal Único de Login e Logout
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::autenticar');
$routes->get('logout', 'AuthController::logout');

// Rotas do Painel de Gestão (Admin / Equipe)
$routes->group('painel', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'PainelController::index');
    $routes->post('pedido/status', 'PainelController::atualizarStatusPedido');
    $routes->post('produto/salvar', 'PainelController::salvarProduto', ['filter' => 'auth:admin']);
    $routes->post('produto/alternar/(:num)', 'PainelController::alternarProduto/$1', ['filter' => 'auth:admin']);
    $routes->post('produto/excluir/(:num)', 'PainelController::excluirProduto/$1', ['filter' => 'auth:admin']);
    $routes->post('categoria/salvar', 'PainelController::salvarCategoria', ['filter' => 'auth:admin']);
    $routes->post('categoria/excluir/(:num)', 'PainelController::excluirCategoria/$1', ['filter' => 'auth:admin']);
    $routes->post('borda/salvar', 'PainelController::salvarBorda', ['filter' => 'auth:admin']);
    $routes->post('borda/excluir/(:num)', 'PainelController::excluirBorda/$1', ['filter' => 'auth:admin']);
    $routes->post('usuario/salvar', 'PainelController::salvarUsuario', ['filter' => 'auth:admin']);
    $routes->post('usuario/excluir/(:num)', 'PainelController::excluirUsuario/$1', ['filter' => 'auth:admin']);
});
