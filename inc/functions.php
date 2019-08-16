<?php


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

        $params["type"] = $index;
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
                    $getSearchClean = $antiXss->xss_clean($getSearch);
                    if (preg_match_all('/"([^"]+)"/', $getSearchClean, $multipleWords)) {
                        //Result is storaged in $multipleWords
                    }
                    $queryRest = preg_replace('/"([^"]+)"/', "", $getSearchClean);
                    $parsedRest = explode(' ', $queryRest);
                    $resultSearchTerms = array_merge($multipleWords[1], $parsedRest);
                    $resultSearchTerms = array_filter($resultSearchTerms);
                    $resultSearchTermsComplete = array_merge($resultSearchTermsComplete, $resultSearchTerms);
                    $getSearchResult = implode("\) AND \(", $resultSearchTermsComplete);
                    $query["query"]["bool"]["must"]["query_string"]["query"] = "\($getSearchResult\)";
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
        $query["query"]["bool"]["must"]["query_string"]["analyzer"] = "portuguese";
        $query["query"]["bool"]["must"]["query_string"]["phrase_slop"] = 10;
        
        return compact('page', 'query', 'limit', 'skip');
    }

}

class Facets
{
    public function facet($field, $size, $field_name, $sort, $sort_type, $get_search, $open = false)
    {
        global $url_base;
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
                echo '<a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $facets['key']).'&quot;"  title="E" style="color:#0040ff;font-size: 90%">'.$facets['key'].'</a>
                      <span class="badge badge-primary badge-pill">'.number_format($facets['doc_count'], 0, ',', '.').'</span>';
                echo '</li>';
            };
            echo '</ul>';

        } else {
            $i = 0;
            echo '<li class="uk-parent '.($open == true ? "uk-open" : "").'">';
            echo '<a href="#" style="color:#333">'.$field_name.'</a>';
            echo ' <ul class="uk-nav-sub">';
            while ($i < 5) {

                echo '<li>';
                echo '<div uk-grid>
                    <div class="uk-width-expand uk-text-small" style="color:#333">
                        <a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $response["aggregations"]["counts"]["buckets"][$i]['key']).'&quot;"  title="E" style="color:#0040ff;font-size: 90%">'.$response["aggregations"]["counts"]["buckets"][$i]['key'].'</a>
                    </div>
                    <div class="uk-width-auto" style="color:#333">
                        <span class="uk-badge" style="font-size:80%">'.number_format($response["aggregations"]["counts"]["buckets"][$i]['doc_count'], 0, ',', '.').'</span>
                    </div>
                    <div class="uk-width-auto" style="color:#333">
                        <a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&notFilter[]='.$field.':&quot;'.$response["aggregations"]["counts"]["buckets"][$i]['key'].'&quot;" title="Ocultar">-</a>
                    </div>';
                echo '</div></li>';  
                
                $i++;

                
            }

            echo '<a href="#'.str_replace(".", "_", $field).'" uk-toggle>mais >></a>';
            echo   '</ul></li>';


            echo '
            <div id="'.str_replace(".", "_", $field).'" uk-modal="center: true">
                <div class="uk-modal-dialog">
                    <button class="uk-modal-close-default" type="button" uk-close></button>
                    <div class="uk-modal-header">
                        <h2 class="uk-modal-title">'.$field_name.'</h2>
                    </div>
                    <div class="uk-modal-body">
                    <ul class="uk-list">
            ';

            foreach ($response["aggregations"]["counts"]["buckets"] as $facets) {
                if ($facets['key'] == "Não preenchido") {
                    echo '<li>';
                    echo '<div uk-grid>
                        <div class="uk-width-3-3 uk-text-small" style="color:#333"><a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&search[]=-_exists_:'.$field.'">'.$facets['key'].' <span class="uk-badge">'.number_format($facets['doc_count'], 0, ',', '.').'</span></a></div>';
                    echo '</div></li>';
                } else {
                    if ($facets['key'] == "Não preenchido") {
                        echo '<li>';
                        echo '<div uk-grid>
                            <div class="uk-width-expand uk-text-small" style="color:#333">
                                <a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $facets['key']).'&quot;">'.$facets['key'].'</a></div>
                            <div class="uk-width-auto" style="color:#333">
                            <a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&notFilter[]='.$field.':&quot;'.$facets['key'].'&quot;">Ocultar</a>
                            ';
                        echo '</div></div></li>';
                    } else {
                        echo '<li><div uk-grid>
                                <div class="uk-width-expand" style="color:#333">
                                    <a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&filter[]='.$field.':&quot;'.str_replace('&', '%26', $facets['key']).'&quot;">'.$facets['key'].'</a></div>
                                <div class="uk-width-auto" style="color:#333">
                                    <span class="uk-badge">'.number_format($facets['doc_count'], 0, ',', '.').'</span>
                                </div>
                                <div class="uk-width-auto" style="color:#333" uk-tooltip="Ocultar">
                                    <a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.$_SERVER["QUERY_STRING"].'&notFilter[]='.$field.':&quot;'.$facets['key'].'&quot;">-</a>
                                </div>
                            </div>
                            </li>';
                    }

                }
            };
            echo '</ul>';
            echo '<p><a href="'.$url_base.'/tools/export.php?format=field&field='.$field.'">Exportar valores da faceta</a></p>';
            echo '
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-default uk-modal-close" type="button">Fechar</button>
            </div>
            </div>
            </div>
            ';

        }
        echo '</li>';

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
        return $response["hits"]["total"];

    }    

}

?>