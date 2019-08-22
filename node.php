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
                <h5>Autor: <?php echo $cursor["_source"]["contributor"][0]; ?></h5>
                <?php if (isset($cursor["_source"]["editions"])) : ?>
                    <h5>Edição: <?php echo $cursor["_source"]["editions"]; ?></h5>
                <?php endif; ?>                
                <h5>Editora: <?php echo $cursor["_source"]["publisher"]; ?></h5>
                <h5>Data de publicação: <?php echo $cursor["_source"]["date"]; ?></h5>
                <?php if (isset($cursor["_source"]["physicalDescriptions"])) : ?>
                    <h5>Descrição física: <?php echo $cursor["_source"]["physicalDescriptions"]; ?></h5>
                <?php endif; ?> 
                <?php if (isset($cursor["_source"]["languages"][0]["name"])) : ?>
                    <h5>Idioma: <?php echo $cursor["_source"]["languages"][0]["name"]; ?></h5>
                <?php endif; ?>                               
                <?php if (isset($cursor["_source"]["identifier"][0]["value"])) : ?>
                    <h5>ISBN: <?php echo $cursor["_source"]["identifier"][0]["value"]; ?></h5>
                <?php endif; ?>
                <?php if (isset($cursor["_source"]["classifications"])) : ?>
                    <h5>Localização física: <?php echo $cursor["_source"]["classifications"]; ?></h5>
                <?php endif; ?>
            </div>
            <div class="col-4">
                <p>
                <?php
                if (!empty($cursor["_source"]["identifier"])) {
                    $path_filename_ext = 'covers/'.$cursor["_source"]["identifier"][0]["value"].'.jpg';
                    if (file_exists($path_filename_ext)) {
                        echo  '<img src="'.$path_filename_ext.'" class="card-img" alt="Capa">';
                    } else {
                        $cover_link = 'https://covers.openlibrary.org/b/isbn/'.$cursor["_source"]["identifier"][0]["value"].'-M.jpg';
                        echo  '<img src="'.$cover_link.'" class="card-img" alt="Capa">';
                    }
                }
                ?>
                </p>
                <p>
                <a class="btn btn-warning" href="editor.php?_id=<?php echo $_GET['_id']; ?>" role="button">Editar registro</a>
                <!-- Button trigger modal delete -->
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">Excluir registro</button>                
                </p>
            </div>
        </div>
        <br/><br/><br/><br/>            
        <?php
            //print_r($cursor);
        ?>
        <br/><br/> 

    <!-- Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Confirmação</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            Tem certeza que deseja excluir o registro?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <a class="btn btn-danger" href="node.php?delete=<?php echo $_GET['_id']; ?>" role="button">Excluir registro</a>
        </div>
        </div>
    </div>
    </div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>           

</body>
</html>