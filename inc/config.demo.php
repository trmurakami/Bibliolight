<?php
# Arquivo de Configuração

$library_name = "Biblioteca Bibliolight";

/* Exibir erros */ 
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);
// Definir Instituição
$instituicao = "";
/* Endereço do server, sem http:// */ 
$hosts = ['localhost'];
/* Configurações do Elasticsearch */
$index = "works";
/* Load libraries for PHP composer */ 
require (__DIR__.'/../vendor/autoload.php'); 
/* Load Elasticsearch Client */ 
$client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build(); 


?>