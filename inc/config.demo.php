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
$type = "bibliolight";
/* Load libraries for PHP composer */ 
require (__DIR__.'/../vendor/autoload.php'); 
/* Load Elasticsearch Client */ 


$client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();

// Get settings for one index
//$params = ['index' => $index];
//$response = $client->indices()->getMapping($params);
//print_r($response);

// Delete
//$params = ['index' => $index];
//$response = $client->indices()->delete($params);

/* Test if index exists */
$indexParams['index']  = $index;   
$testIndex = $client->indices()->exists($indexParams);
if ($testIndex == false) {
    $createIndexParams = [
        'index' => $index,
        'body' => [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'filter' => [
                        'portuguese_stop' => [
                            'type' => 'stop',
                            'stopwords' => 'portuguese'
                        ],
                        'my_ascii_folding' => [
                            'type' => 'asciifolding',
                            'preserve_original' => true
                        ],
                        'portuguese_stemmer' => [
                            'type' => 'stemmer',
                            'language' =>  'light_portuguese'
                        ]
                    ],
                    'analyzer' => [
                        'portuguese' => [
                            'tokenizer' => 'standard',
                            'filter' =>  [ 
                                "standard", 
                                "lowercase", 
                                "my_ascii_folding",
                                "portuguese_stop",
                                "portuguese_stemmer"
                            ]
                        ]
                    ]
                ]
            ],
            'mappings' => [ 
                'properties' => [
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'portuguese',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'publisher' => [
                        'type' => 'text',
                        'analyzer' => 'portuguese',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'date' => [
                        'type' => 'text',
                        'analyzer' => 'portuguese',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ]                     
                ]
            ]                            
        ]
    ];
    $responseCreateIndex = $client->indices()->create($createIndexParams);
}   

?>