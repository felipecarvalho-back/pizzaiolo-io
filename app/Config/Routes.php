<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Rotas do Carrinho & HTMX
$routes->group('carrinho', static function ($routes) {
    $routes->get('drawer', 'CarrinhoController::drawer');
    $routes->get('contador', 'CarrinhoController::contador');
    $routes->get('modal-pizza/(:num)', 'CarrinhoController::modalPizza/$1');
    $routes->post('adicionar', 'CarrinhoController::adicionar');
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
$routes->group('painel', static function ($routes) {
    $routes->get('/', 'PainelController::index');
    $routes->post('produto/salvar', 'PainelController::salvarProduto');
    $routes->get('produto/excluir/(:num)', 'PainelController::excluirProduto/$1');
    $routes->post('categoria/salvar', 'PainelController::salvarCategoria');
    $routes->post('borda/salvar', 'PainelController::salvarBorda');
    $routes->post('usuario/salvar', 'PainelController::salvarUsuario');
    $routes->get('usuario/excluir/(:num)', 'PainelController::excluirUsuario/$1');
});
