<?php
  require 'inc/config.php';
  require 'inc/functions.php';

  var_dump($_POST);

  if (isset($_POST)) {
    print_r($_POST);
    //$query["doc"]["name"] = $_POST["name"];
    //$query["doc"]["datePublished"] = $_POST["datePublished"];
    //$query["doc"]["publisher"]["organization"]["name"] = $_POST["publisher_organization_name"];
    //$query["doc_as_upsert"] = true;
    //$resultado = elasticsearch::elastic_update($_POST["_id"], $type, $query);
    //print_r($resultado);
    //sleep(5); 
    //echo '<script>window.location = \'result_trabalhos.php?filter[]=name:"'.$_POST["name"].'"\'</script>';
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Tiago Murakami">
    <title>Editor</title>

    <link rel="canonical" href="https://github.com/trmurakami/bibliolight">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  </head>
  <body>
  <div class="container">
    <h1>Editor</h1>

    <form action="editor.php" method="post">
    <div class="form-group row">
      <label for="title" class="col-sm-2 col-form-label">Título</label>
      <div class="col-sm-10">
          <input type="text" class="form-control" id="title" name="title" placeholder="Insira o título">
      </div>
    </div>
    <div class="form-group row">
      <label for="editor" class="col-sm-2 col-form-label">Editora</label>
      <div class="col-sm-10">
          <input type="text" class="form-control" id="editor" name="editor" placeholder="Insira a editora">
      </div>
    </div>      
    <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
  </div>

  </body>
</html>