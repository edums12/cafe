<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'Controller';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login'] = 'Controller/login';
$route['sair'] = 'Controller';

$route['configuracoes'] = 'Controller/configuracoes';
$route['configuracoes/gasto'] = 'Controller/configuracoes_gasto';

$route['consumo/(:num)'] = 'Controller/consumo/$1';
$route['consumo/detalhado'] = 'Controller/consumo_detalhado';
$route['consumo/desfazer/(:num)'] = 'Controller/desfazer_consumo/$1';
$route['consumo/pagar'] = 'Controller/realizar_pagamento';
$route['consumo/mensal'] = 'Controller/consumo_mensal';