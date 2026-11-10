<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Rotas de Clientes (Área do Cliente)
$routes->group('cliente', static function ($routes) {
    $routes->get('login', 'ClienteController::login');
    $routes->post('login', 'ClienteController::autenticar');
    $routes->get('cadastro', 'ClienteController::cadastro');
    $routes->post('cadastro', 'ClienteController::registrar');
    $routes->get('perfil', 'ClienteController::perfil');
    $routes->post('perfil', 'ClienteController::atualizarPerfil');
    $routes->get('logout', 'ClienteController::logout');
});

// Atalho rápido de cadastro
$routes->get('cadastro', 'ClienteController::cadastro');

// Rotas de Autenticação Administrativa
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::autenticar');
$routes->get('logout', 'AuthController::logout');

// Rotas do Painel de Gestão
$routes->group('painel', static function ($routes) {
    $routes->get('/', 'PainelController::index');
    $routes->post('produto/salvar', 'PainelController::salvarProduto');
    $routes->get('produto/excluir/(:num)', 'PainelController::excluirProduto/$1');
    $routes->post('categoria/salvar', 'PainelController::salvarCategoria');
    $routes->post('borda/salvar', 'PainelController::salvarBorda');
    $routes->post('usuario/salvar', 'PainelController::salvarUsuario');
    $routes->get('usuario/excluir/(:num)', 'PainelController::excluirUsuario/$1');
});
