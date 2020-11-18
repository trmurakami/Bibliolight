<?php

if (isset($_GET["format"])) {

    if ($_GET["format"] == "table") {

        $file = "bibliolight_export.tsv";
        header('Content-type: text/tab-separated-values; charset=utf-8');
        header("Content-Disposition: attachment; filename=$file");

        // Set directory to ROOT
        chdir('../');
        // Include essencial files
        include 'inc/config.php';
        include 'inc/functions.php';

        if (!empty($_GET)) {
            $result_get = Requests::getParser($_GET);
            $query = $result_get['query'];
            $params = [];
            $params["index"] = $index;
            $params["size"] = 50;
            $params["scroll"] = "30s";
            $params["body"] = $query;

            $cursor = $client->search($params);
            $total = $cursor["hits"]["total"];

            $content[] = "ID\tTítulo\tAutor\tEditora\tAno de publicação\tEdição\tISBN\tAssuntos\tLocalização física";

            foreach ($cursor["hits"]["hits"] as $r) {
                unset($fields);

                $fields[] = $r["_id"];

                $fields[] = $r["_source"]["title"];

                if (!empty($r["_source"]["contributor"])) {
                    $fields[] = implode('; ', $r["_source"]["contributor"]);
                } else {
                    $fields[] = "";
                }                

                if (!empty($r["_source"]["publisher"])) {
                    $fields[] = $r["_source"]["publisher"];
                } else {
                    $fields[] = "";
                }
                
                if (!empty($r["_source"]["date"])) {
                    $fields[] = $r["_source"]["date"];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]["editions"])) {
                    $fields[] = $r["_source"]["editions"];
                } else {
                    $fields[] = "";
                }

                if (!empty($r["_source"]["identifier"])) {
                    $fields[] = $r["_source"]["identifier"][0]["value"];
                } else {
                    $fields[] = "";
                }
                
                if (!empty($r["_source"]["subjects"])) {
                    $fields[] = implode('; ', $r["_source"]["subjects"]);
                } else {
                    $fields[] = "";
                }
                
                if (!empty($r["_source"]["classifications"])) {
                    $fields[] = $r["_source"]["classifications"];
                } else {
                    $fields[] = "";
                }
                $content[] = implode("\t", $fields);
                unset($fields);


            }


            while (isset($cursor['hits']['hits']) && count($cursor['hits']['hits']) > 0) {
                $scroll_id = $cursor['_scroll_id'];
                $cursor = $client->scroll(
                    [
                    "scroll_id" => $scroll_id,
                    "scroll" => "30s"
                    ]
                );

                foreach ($cursor["hits"]["hits"] as $r) {
                    unset($fields);

                    $fields[] = $r["_id"];

                    $fields[] = $r["_source"]["title"];
    
                    if (!empty($r["_source"]["contributor"])) {
                        $fields[] = $r["_source"]["contributor"][0];
                    } else {
                        $fields[] = "";
                    }                
    
                    if (!empty($r["_source"]["publisher"])) {
                        $fields[] = $r["_source"]["publisher"];
                    } else {
                        $fields[] = "";
                    }
                    
                    if (!empty($r["_source"]["date"])) {
                        $fields[] = $r["_source"]["date"];
                    } else {
                        $fields[] = "";
                    }
    
                    if (!empty($r["_source"]["editions"])) {
                        $fields[] = $r["_source"]["editions"];
                    } else {
                        $fields[] = "";
                    }
    
                    if (!empty($r["_source"]["identifier"])) {
                        $fields[] = $r["_source"]["identifier"][0]["value"];
                    } else {
                        $fields[] = "";
                    }
                    
                    if (!empty($r["_source"]["subjects"])) {
                        $fields[] = implode('; ', $r["_source"]["subjects"]);
                    } else {
                        $fields[] = "";
                    }
                    
                    if (!empty($r["_source"]["classifications"])) {
                        $fields[] = $r["_source"]["classifications"];
                    } else {
                        $fields[] = "";
                    }  

                    $content[] = implode("\t", $fields);
                    unset($fields);


                }
            }
            echo implode("\n", $content);

        }

    } elseif ($_GET["format"] == "ris") {

        $file="export_bdpi.ris";
        header('Content-type: application/x-research-info-systems');
        header("Content-Disposition: attachment; filename=$file");

        // Set directory to ROOT
        chdir('../');
        // Include essencial files
        include 'inc/config.php';


        $result_get = Requests::getParser($_GET);
        $query = $result_get['query'];
        $limit = $result_get['limit'];
        $page = $result_get['page'];
        $skip = $result_get['skip'];

        if (isset($_GET["sort"])) {
            $query['sort'] = [
                ['name.keyword' => ['order' => 'asc']],
            ];
        } else {
            $query['sort'] = [
                ['datePublished.keyword' => ['order' => 'desc']],
            ];
        }

        $params = [];
        $params["index"] = $index;
        $params["type"] = $type;
        $params["size"] = 50;
        $params["scroll"] = "30s";
        $params["body"] = $query;

        $cursor = $client->search($params);
        foreach ($cursor["hits"]["hits"] as $r) {
            /* Exportador RIS */
            $record_blob[] = Exporters::RIS($r);
        }

        while (isset($cursor['hits']['hits']) && count($cursor['hits']['hits']) > 0) {
            $scroll_id = $cursor['_scroll_id'];
            $cursor = $client->scroll(
                [
                "scroll_id" => $scroll_id,
                "scroll" => "30s"
                ]
            );

            foreach ($cursor["hits"]["hits"] as $r) {
                /* Exportador RIS */
                $record_blob[] = Exporters::RIS($r);
            }
        }
        foreach ($record_blob as $record) {
            $record_array = explode('\n', $record);
            echo implode("\n", $record_array);
        }

    } elseif ($_GET["format"] == "bibtex") {

        $file="export_bdpi.bib";
        header('Content-type: text/plain');
        header("Content-Disposition: attachment; filename=$file");

        // Set directory to ROOT
        chdir('../');
        // Include essencial files
        include 'inc/config.php';


        $result_get = Requests::getParser($_GET);
        $query = $result_get['query'];
        $limit = $result_get['limit'];
        $page = $result_get['page'];
        $skip = $result_get['skip'];

        if (isset($_GET["sort"])) {
            $query['sort'] = [
                ['name.keyword' => ['order' => 'asc']],
            ];
        } else {
            $query['sort'] = [
                ['datePublished.keyword' => ['order' => 'desc']],
            ];
        }

        $params = [];
        $params["index"] = $index;
        $params["type"] = $type;
        $params["size"] = 50;
        $params["scroll"] = "30s";
        $params["body"] = $query;

        $cursor = $client->search($params);
        foreach ($cursor["hits"]["hits"] as $r) {
            /* Exportador RIS */
            $record_blob[] = Exporters::bibtex($r);
        }

        while (isset($cursor['hits']['hits']) && count($cursor['hits']['hits']) > 0) {
            $scroll_id = $cursor['_scroll_id'];
            $cursor = $client->scroll(
                [
                "scroll_id" => $scroll_id,
                "scroll" => "30s"
                ]
            );

            foreach ($cursor["hits"]["hits"] as $r) {
                /* Exportador RIS */
                $record_blob[] = Exporters::bibtex($r);
            }
        }

        foreach ($record_blob as $record) {
            $record_array = explode('\n', $record);
            echo "\n";
            echo "\n";
            echo implode("\n", $record_array);
        }
    } else {
        echo "Formato não definido";
    }}
?>