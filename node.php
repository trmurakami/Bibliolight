<?php
/**
 * Item page
 */

require 'inc/config.php';
require 'inc/functions.php';

if (isset($_REQUEST['delete'])) {
    $delete = Elasticsearch::delete($_REQUEST['delete'], null);
    header('Location: index.php'); 
} elseif (isset($_REQUEST['_id'])) {
    $cursor = Elasticsearch::get($_REQUEST['_id'], null);
} else {
    echo "ID não encontrado";
}


/* QUERY */


?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
<head>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Tiago Murakami">
        <title>Detalhe do registro: <?php echo $cursor["_source"]['title'];?></title>
        <link rel="canonical" href="https://github.com/trmurakami/bibliolight">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    </head>


    

</head>
<body>

    <!-- NAV -->
    <?php require 'inc/navbar.php'; ?>
    <!-- /NAV -->
        
    <div class="container">
        <div class="row">
            <div class="col-8">
                <h1><?php echo $cursor["_source"]["title"]; ?></h1>
                <h5>ID: <?php echo $cursor["_id"]; ?></h5>
                <h5>Editora: <?php echo $cursor["_source"]["publisher"]; ?></h5>
                <h5>Data de publicação: <?php echo $cursor["_source"]["date"]; ?></h5>
            </div>
            <div class="col-4">
                <a class="btn btn-warning" href="editor.php?_id=<?php echo $_GET['_id']; ?>" role="button">Editar registro</a>
                <a class="btn btn-danger" href="node.php?delete=<?php echo $_GET['_id']; ?>" role="button">Excluir registro</a>
            </div>
        </div>
        <br/><br/><br/><br/>            
        <?php
            //print_r($cursor);
        ?>
        <br/><br/> 
                

</body>
</html>