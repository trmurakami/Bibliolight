<?php

require ('config.php'); 

/* Exibir erros */ 
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

/* Load libraries for PHP composer */ 
require (__DIR__.'/../vendor/autoload.php'); 

/* Connect to Elasticsearch */
try {

    $client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build(); 
    //print("<pre>".print_r($client,true)."</pre>");
    $indexParams['index']  = $index;   
    $testIndex = $client->indices()->exists($indexParams);
} catch (Exception $e) {    
    $error_connection_message = '<div class="alert alert-danger" role="alert">Elasticsearch não foi encontrado. Favor executar o arquivo elasticsearch.lnk.</div>';
}

/* Create index if not exists */

if (isset($testIndex) && $testIndex == false) {
    Elasticsearch::createIndex($index, $client);
    Elasticsearch::mappingsIndex($index, $client);
}


/**
 * Elasticsearch Class
 */
class Elasticsearch
{

    /**
     * Executa o commando get no Elasticsearch
     *
     * @param string   $_id               ID do documento.
     * @param string[] $fields            Informa quais campos o sistema precisa retornar. Se nulo, o sistema retornará tudo.
     * @param string   $alternative_index Caso use indice alternativo
     *
     */
    public static function get($_id, $fields, $alternative_index = "")
    {
        global $index;
        global $client;
        $params = [];

        if (strlen($alternative_index) > 0) {
            $params["index"] = $alternative_index;
        } else {
            $params["index"] = $index;
        }
        $params["id"] = $_id;        
        $params["_source"] = $fields;

        $response = $client->get($params);
        return $response;
    }

    /**
     * Executa o commando search no Elasticsearch
     *
     * @param string[] $fields Informa quais campos o sistema precisa retornar. Se nulo, o sistema retornará tudo.
     * @param int      $size   Quantidade de registros nas respostas
     * @param resource $body   Arquivo JSON com os parâmetros das consultas no Elasticsearch
     *
     */
    public static function search($fields, $size, $body, $alternative_index = "")
    {
        global $index;
        global $client;
        $params = [];

        if (strlen($alternative_index) > 0 ) {
            $params["index"] = $alternative_index;
        } else {
            $params["index"] = $index;
        }

        $params["_source"] = $fields;
        $params["size"] = $size;
        $params["body"] = $body;

        $response = $client->search($params);
        return $response;
    }

    /**
     * Executa o commando update no Elasticsearch
     *
     * @param string   $_id  ID do documento
     * @param resource $body Arquivo JSON com os parâmetros das consultas no Elasticsearch
     *
     */
    public static function update($_id, $body, $alternative_index = "")
    {
        global $index;
        global $client;
        $params = [];

        if (strlen($alternative_index) > 0) {
            $params["index"] = $alternative_index;
        } else {
            $params["index"] = $index;
        }
        $params["id"] = $_id;
        $params["body"] = $body;

        $response = $client->update($params);
        return $response;
    }

    /**
     * Executa o commando delete no Elasticsearch
     *
     * @param string $_id  ID do documento
     *
     */
    public static function delete($_id, $alternative_index = "")
    {
        global $index;
        global $client;
        $params = [];

        if (strlen($alternative_index) > 0) {
            $params["index"] = $alternative_index;
        } else {
            $params["index"] = $index;
        }
        $params["id"] = $_id;
        $params["client"]["ignore"] = 404;

        $response = $client->delete($params);
        return $response;
    }

    /**
     * Executa o commando delete_by_query no Elasticsearch
     *
     * @param string   $_id               ID do documento
     * @param resource $body              Arquivo JSON com os parâmetros das consultas no Elasticsearch
     * @param resource $alternative_index Se tiver indice alternativo
     * 
     * @return array Resposta do comando
     */
    public static function deleteByQuery($_id, $body, $alternative_index = "")
    {
        global $index;
        global $client;
        $params = [];

        if (strlen($alternative_index) > 0) {
            $params["index"] = $alternative_index;
        } else {
            $params["index"] = $index;
        }

        $params["id"] = $_id;
        $params["body"] = $body;

        $response = $client->deleteByQuery($params);
        return $response;
    }

    /**
     * Executa o commando update no Elasticsearch e retorna uma resposta em html
     *
     * @param string   $_id  ID do documento
     * @param resource $body Arquivo JSON com os parâmetros das consultas no Elasticsearch
     *
     */
    static function storeRecord($_id, $body)
    {
        $response = Elasticsearch::update($_id, $body);
        echo '<br/>Resultado: '.($response["_id"]).', '.($response["result"]).', '.($response["_shards"]['successful']).'<br/>';

    }

    /**
     * Cria o indice
     *
     * @param string   $indexName  Nome do indice
     *
     */
    static function createIndex($indexName, $client)
    {
        $createIndexParams = [
            'index' => $indexName,
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
                                    'lowercase', 
                                    'my_ascii_folding',
                                    'portuguese_stop',
                                    'portuguese_stemmer'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $responseCreateIndex = $client->indices()->create($createIndexParams);
    } 
    
    /**
     * Cria o indice
     *
     * @param string   $indexName  Nome do indice
     *
     */
    static function mappingsIndex($indexName, $client)
    {
        // Set the index and type
        $mappingsParams = [
            'index' => $indexName,
            'body' => [
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
                    'contributor' => [
                        'type' => 'text',
                        'analyzer' => 'portuguese',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],
                    'editions' => [
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
                        'type' => 'integer'
                    ],
                    'languages' => [
                        'properties' => [
                            'name' => [
                                'type' => 'text',
                                'analyzer' => 'portuguese',
                                'fields' => [
                                    'keyword' => [
                                        'type' => 'keyword',
                                        'ignore_above' => 256
                                    ]
                                ]                                
                            ],
                            'code' => [
                                'type' => 'text'                             
                            ]                            
                        ]
                    ],
                    'physicalDescriptions' => [
                        'type' => 'text',
                        'analyzer' => 'portuguese',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ]
                    ],                                          
                ]
            ]
        ];

        // Update the index mapping
        $client->indices()->putMapping($mappingsParams);
    }    
    

}


class Requests
{

    static function getParser($get)
    {
        global $antiXss;
        $query = [];        

        if (!empty($get['fields'])) {
            $query["query"]["bool"]["must"]["query_string"]["fields"] = $get['fields'];
        } else {
            $query["query"]["bool"]["must"]["query_string"]["default_field"] = "*";
        }

        /* Pagination */
        if (isset($get['page'])) {
            $page = $get['page'];
            unset($get['page']);
        } else {
            $page = 1;
        }

        /* Pagination variables */
        $limit = 20;
        $skip = ($page - 1) * $limit;
        $next = ($page + 1);
        $prev = ($page - 1);

        $i_filter = 0;
        if (!empty($get['filter'])) {
            foreach ($get['filter'] as $filter) {
                $filter_array = explode(":", $filter);
                $filter_array_term = str_replace('"', "", (string)$filter_array[1]);
                $query["query"]["bool"]["filter"][$i_filter]["term"][(string)$filter_array[0].".keyword"] = $filter_array_term;
                $i_filter++;
            }

        }

        if (!empty($get['notFilter'])) {
            $i_notFilter = 0;
            foreach ($get['notFilter'] as $notFilter) {
                $notFilterArray = explode(":", $notFilter);
                $notFilterArrayTerm = str_replace('"', "", (string)$notFilterArray[1]);
                $query["query"]["bool"]["must_not"][$i_notFilter]["term"][(string)$notFilterArray[0].".keyword"] = $notFilterArrayTerm;
                $i_notFilter++;
            }
        }

        if (!empty($get['search'])) {

            $resultSearchTermsComplete = [];
            foreach ($get['search'] as $getSearch) {
                if (empty($getSearch)) {
                    $query["query"]["bool"]["must"]["query_string"]["query"] = "*";
                } else {
                    $query["query"]["bool"]["must"]["query_string"]["query"] = "$getSearch";
                } 
            }

        }

        if (!empty($get['range'])) {
            $query["query"]["bool"]["must"]["query_string"]["query"] = $get['range'][0];
        }         
        
        if (!isset($query["query"]["bool"]["must"]["query_string"]["query"])) {
            $query["query"]["bool"]["must"]["query_string"]["query"] = "*";
        }

        //$query["query"]["bool"]["must"]["query_string"]["default_operator"] = "AND";
        //$query["query"]["bool"]["must"]["query_string"]["analyzer"] = "portuguese";
        //$query["query"]["bool"]["must"]["query_string"]["phrase_slop"] = 10;
        
        return compact('page', 'query', 'limit', 'skip');
    }

}

class Facets
{
    public function facet($field, $size, $field_name, $sort, $sort_type, $get_search, $open = false)
    {
        global $url_base;
        if (!empty($get_search)) {
            $queryComplete = http_build_query($get_search);
        } else {
            $queryComplete = "";
        }
        
        $query = $this->query;
        $query["aggs"]["counts"]["terms"]["field"] = "$field.keyword";
        if (!empty($_SESSION['oauthuserdata'])) {
            $query["aggs"]["counts"]["terms"]["missing"] = "Não preenchido";
        }
        if (isset($sort)) {
            $query["aggs"]["counts"]["terms"]["order"][$sort_type] = $sort;
        }
        $query["aggs"]["counts"]["terms"]["size"] = $size;

        $response = Elasticsearch::search(null, 0, $query);

        $result_count = count($response["aggregations"]["counts"]["buckets"]);

        if ($result_count == 0) {

        } elseif (($result_count != 0) && ($result_count < 5)) {

            echo '<a href="#" class="list-group-item list-group-item-action active">'.$field_name.'</a>';
            echo '<ul class="list-group list-group-flush">';
            foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                echo '<a href="search.php?'.$queryComplete.'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $facets['key']).'&quot;"  title="E" style="color:#0040ff;font-size: 90%">'.$facets['key'].'</a>
                      <span class="badge badge-primary badge-pill">'.number_format($facets['doc_count'], 0, ',', '.').'</span>';
                echo '</li>';
            };
            echo '</ul>';

        } else {
            $i = 0;
            echo '<a href="#" class="list-group-item list-group-item-action active">'.$field_name.'</a>';
            echo '<ul class="list-group list-group-flush">';
            while ($i < 5) {

                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                echo '<a href="search.php?'.$queryComplete.'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $response["aggregations"]["counts"]["buckets"][$i]['key']).'&quot;"  title="E" style="color:#0040ff;font-size: 90%">'.$response["aggregations"]["counts"]["buckets"][$i]['key'].'</a>
                        <span class="badge badge-primary badge-pill">'.number_format($response["aggregations"]["counts"]["buckets"][$i]['doc_count'], 0, ',', '.').'</span>';
                echo '</li>';  
                
                $i++;

                
            }
            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
            echo '<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#'.$field.'Modal">ver todos >>></button>  ';
            echo '</li>';
            echo '</ul>';

            echo '<div class="modal fade" id="'.$field.'Modal" tabindex="-1" role="dialog" aria-labelledby="'.$field.'ModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="'.$field.'ModalLabel">'.$field_name.'</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">';

                    foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {
                        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                        echo '<a href="search.php?'.$queryComplete.'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $facets['key']).'&quot;"  title="E" style="color:#0040ff;font-size: 90%">'.$facets['key'].'</a>
                            <span class="badge badge-primary badge-pill">'.number_format($facets['doc_count'], 0, ',', '.').'</span>';
                        echo '</li>';
                    }

            echo '</ul>';

             echo '
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
                </div>
            </div></div></div>
            ';
        }
    }
}

class UI {
   
    static function pagination($page, $total, $limit)
    {
        echo '<nav>';
        echo '<ul class="pagination">';
        if ($page == 1) {
            echo '<li class="page-item disabled"><a class="page-link" href="#"> Anterior</a></li>';
        } else {
            $_GET["page"] = $page-1 ;
            echo '<li class="page-item"><a class="page-link" href="'.http_build_query($_GET).'"> Anterior</a></li>';
        }
        echo '<li class="page-item disabled"><a class="page-link" href="#">Página '.number_format($page, 0, ',', '.') .'</a></li>';
        echo '<li class="page-item disabled"><a class="page-link" href="#">'.number_format($total, 0, ',', '.') .'&nbsp;registros</a></li>';
        if ($total/$limit > $page) {
            $_GET["page"] = $page+1;
            echo '<li class="page-item"><a class="page-link" href="http://'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'"> Próxima</a></li>';
        } else {
            echo '<li class="page-item disabled"><a class="page-link" href="#">Próxima</a></li>';
        }
        echo '</ul>';
        echo '</nav>';
    }
}


Class Homepage 
{

    static function numberOfRecords()
    {
        $body = '
        {
            "query": {
                "match_all": {}
            }
        }
        '; 
        $response = Elasticsearch::search(null, 0, $body);
        return $response["hits"]["total"]["value"];

    }
    
    static function numberCirc()
    {
        $body = '
        {
            "query": {
                "exists": {
                    "field": "circ.name"
                }
            }
        }
        '; 
        $response = Elasticsearch::search(null, 0, $body);
        return $response["hits"]["total"]["value"];

    }      

}

?>