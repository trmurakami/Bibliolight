<?php

    require 'inc/config.php';
    require 'inc/functions.php';

    use Ramsey\Uuid\Uuid;
    use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;


if (isset($_REQUEST["ID"])) {
    //print_r($_REQUEST);
    $query["doc"]["title"] = $_REQUEST["title"];
    $query["doc"]["contributor"] = $_REQUEST["contributor"];
    $query["doc"]["publisher"] = $_REQUEST["publisher"];
    $query["doc"]["date"] = $_REQUEST["date"];
    if (!empty($_REQUEST["isbn"])) {
        $query["doc"]["identifier"][0]["value"] = $_REQUEST["isbn"];
        $query["doc"]["identifier"][0]["type"] = "ISBN";
    }
    $query["doc_as_upsert"] = true;
    print_r($query);
    $result = Elasticsearch::update($_REQUEST["ID"], $query);
    //print_r($result);
    sleep(2); 
    echo '<script>window.location = \'index.php\'</script>';
} else {
    $uuid4 = Uuid::uuid4();
    $uuid = $uuid4->toString();
}


/* Define variables */
if (isset($_REQUEST["_id"])) {
    $uuid = $_REQUEST["_id"];
    $elasticsearch = new Elasticsearch();
    $cursor = $elasticsearch->get($_REQUEST["_id"], null);
    //print_r($cursor);

    if (isset($cursor["_source"]["title"])) {
    $titleValue = $cursor["_source"]["title"];
    } else {
    $titleValue = "";
    }

    if (isset($cursor["_source"]["contributor"])) {
    $contributorValue = $cursor["_source"]["contributor"][0];
    } else {
    $contributorValue = "";
    }  

    if (isset($cursor["_source"]["publisher"])) {
    $publisherValue = $cursor["_source"]["publisher"];
    } else {
    $publisherValue = "";
    }

    if (isset($cursor["_source"]["date"])) {
    $dateValue = $cursor["_source"]["date"];
    } else {
    $dateValue = "";
    }  

    if (isset($cursor["_source"]["identifier"])) {
    $isbnValue = $cursor["_source"]["identifier"][0]["value"];
    } else {
    $isbnValue = "";
    }  

} else {
    $titleValue = "";
    $contributorValue = "";
    $publisherValue = "";
    $dateValue = "";
    $isbnValue = "";
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
  <?php include 'inc/navbar.php'; ?>
  <div class="container">
    
    <h1>Editor</h1>

    <form action="editor.php" method="post">
    <div class="form-group row">
      <label for="ID" class="col-sm-2 col-form-label">ID</label>
      <div class="col-sm-10">
        <input type="text" readonly class="form-control-plaintext" id="ID" name="ID" value="<?php echo $uuid; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label for="title" class="col-sm-2 col-form-label">Título</label>
      <div class="col-sm-10">
          <input type="text" class="form-control" id="title" name="title" placeholder="Insira o título" value="<?php echo $titleValue; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label for="contributor" class="col-sm-2 col-form-label">Autor</label>
      <div class="col-10">
          <input type="text" class="form-control" id="contributor" name="contributor[]" placeholder="Insira o autor no formato (SOBRENOME, Nome)" value="<?php echo $contributorValue; ?>">
      </div>
    </div>    
    <div class="form-group row">
      <label for="publisher" class="col-sm-2 col-form-label">Imprenta</label>
      <div class="col-7">
          <input type="text" class="form-control" id="publisher" name="publisher" placeholder="Insira a editora" value="<?php echo $publisherValue; ?>">
      </div>
      <div class="col">
          <input type="text" class="form-control" id="date" name="date" placeholder="Insira a data de publicação" pattern="\d\d\d\d" value="<?php echo $dateValue; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label for="date" class="col-sm-2 col-form-label">ISBN</label>
      <div class="col-sm-10">
          <input type="text" class="form-control" id="isbn" name="isbn" placeholder="Insira o ISBN (Somente os números)" pattern="\d*" value="<?php echo $isbnValue; ?>">
      </div>
    </div>              
    <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
  </div>

  </body>
</html>