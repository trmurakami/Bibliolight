<?php
    require 'inc/config.php';
    require 'inc/functions.php';

if (isset($fields)) {
    $_GET["fields"] = $fields;
}
    $result_get = Requests::getParser($_GET);
    $limit = $result_get['limit'];
    $page = $result_get['page'];
    $params = [];
    $params["index"] = $index;
    $params["body"] = $result_get['query'];
    $cursorTotal = $client->count($params);
    $total = $cursorTotal["count"];

if (isset($_GET["sort"])) {
    $result_get['query']["sort"][$_GET["sort"]]["unmapped_type"] = "long";
    $result_get['query']["sort"][$_GET["sort"]]["missing"] = "_last";
    $result_get['query']["sort"][$_GET["sort"]]["order"] = "desc";
    $result_get['query']["sort"][$_GET["sort"]]["mode"] = "max";
} else {
    //$result_get['query']['sort']['datePublished.keyword']['order'] = "desc";
    //$result_get['query']["sort"]["_uid"]["unmapped_type"] = "long";
    //$result_get['query']["sort"]["_uid"]["missing"] = "_last";
    //$result_get['query']["sort"]["_uid"]["order"] = "desc";
    //$result_get['query']["sort"]["_uid"]["mode"] = "max";
}
    $params["body"] = $result_get['query'];
    $params["size"] = $limit;
    $params["from"] = $result_get['skip'];
    $cursor = $client->search($params);


?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Tiago Murakami">
        <title>Resultado da busca</title>
        <link rel="canonical" href="https://github.com/trmurakami/bibliolight">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    </head>
    <body>
        <?php require 'inc/navbar.php'; ?>               
        <div class="container">
            <!-- PAGINATION -->
            <?php UI::pagination($page, $total, $limit); ?>
            <!-- /PAGINATION --> 

            <div class="row mb-3">

                <div class="col-12 col-md-8 themed-grid-col">
                <?php
                foreach ($cursor["hits"]["hits"] as $r) {
                    echo '
                    <div class="card mb-3">
                        <div class="row no-gutters">
                            <div class="col-md-2">';

                            if (!empty($r["_source"]["isbn"])) {
                                $cover_link = 'https://covers.openlibrary.org/b/isbn/'.$r["_source"]["isbn"].'-M.jpg';
                                echo  '<img src="'.$cover_link.'" class="card-img" alt="Capa">';
                            } else {
                                echo '<svg class="bd-placeholder-img" width="100%" height="170" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: Image"><title>Placeholder</title><rect width="100%" height="100%" fill="#868e96"></rect><text x="50%" y="50%" fill="#dee2e6" dy=".3em">Image</text></svg>
                                <!-- <img src="..." class="card-img" alt="Capa"> -->';
                            }

                    echo '</div>
                            <div class="col-md-10">
                            <div class="card-body">
                                <h5 class="card-title"><a href="node.php?_id='.$r["_id"].'">'.$r["_source"]["title"].'</a></h5>
                                <p class="card-text">
                                    <small class="text-muted">Autor: '.$r["_source"]["contributor"][0].'</small><br/>
                                    <small class="text-muted">Editora: '.$r["_source"]["publisher"].'</small><br/>
                                    <small class="text-muted">Data de publicação: '.$r["_source"]["date"].'</small><br/>
                                    <small class="text-muted">ISBN: '.$r["_source"]["identifier"][0]["value"].'</small>
                                </p>
                                
                            </div>
                            </div>
                        </div>
                    </div>
                    ';
                }
                ?>                
                </div>
                <div class="col-6 col-md-4 themed-grid-col">

                    <div class="list-group">

                        <?php
                            $facets = new Facets();
                            $facets->query = $result_get['query'];
                        if (!isset($_GET["search"])) {
                            $_GET["search"] = null;
                        }
                            $facets->facet("contributor", 10, "Autores", null, "_term", $_GET["search"], true);
                            $facets->facet("publisher", 10, "Editora", null, "_term", $_GET["search"], true);
                            $facets->facet("date", 10, "Data de publicação", null, "_term", $_GET["search"], true);
                        ?>
                        
                    </div>
                </div>
                <!-- PAGINATION -->
                <?php UI::pagination($page, $total, $limit); ?>
                <!-- /PAGINATION --> 
            </div>
        </div>
    </body>
</html>