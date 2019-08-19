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
$index = "bibliolight";
/* Load libraries for PHP composer */ 
require (__DIR__.'/../vendor/autoload.php'); 
/* Load Elasticsearch Client */ 


$client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();

/* Test if index exists */
$indexParams['index']  = $index;   
$testIndex = $client->indices()->exists($indexParams);
if ($testIndex == false) {
    $createIndexParams = [
        'index' => $index,
        'body' => [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 1
            ]
        ]
    ];
    $responseCreateIndex = $client->indices()->create($createIndexParams);
}   

?>