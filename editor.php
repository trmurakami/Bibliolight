<?php

    require 'inc/config.php';
    require 'inc/functions.php';

    use Ramsey\Uuid\Uuid;
    use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

if (isset($_FILES['cover']['name'])) {
    if (($_FILES['cover']['name']!="")) {
        // Where the file is going to be stored
        $target_dir = "covers/";
        $file = $_FILES['cover']['name'];
        $path = pathinfo($file);
        $filename = $path['filename'];
        $ext = $path['extension'];
        $temp_name = $_FILES['cover']['tmp_name'];
        $path_filename_ext = $target_dir.$filename.".".$ext;
        
        // Check if file already exists
        if (file_exists($path_filename_ext)) {
            $alert = '<div class="alert alert-danger" role="alert">Desculpe, arquivo já existe.</div>';
        } else {
            move_uploaded_file($temp_name, $path_filename_ext);
            $alert = '<div class="alert alert-success" role="alert">Parabéns! Arquivo carregado com sucesso.</div>';
        }
    }
}    

if (isset($_REQUEST["isbn"])) {
    $url_isbn = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$_REQUEST["isbn"].'';
    $json_isbn = file_get_contents($url_isbn);
    $record_isbn = json_decode($json_isbn, true);
    //print_r($record_isbn);
    if ($record_isbn["totalItems"] == 1) {
        $titleValue = $record_isbn["items"][0]["volumeInfo"]["title"];
        $contributorValue = implode(";", $record_isbn["items"][0]["volumeInfo"]["authors"]);
        if (isset($record_isbn["items"][0]["volumeInfo"]["publisher"])) {
            $publisherValue = $record_isbn["items"][0]["volumeInfo"]["publisher"];
        }
        if (isset($record_isbn["items"][0]["volumeInfo"]["publishedDate"])) {
            $dateValue = $record_isbn["items"][0]["volumeInfo"]["publishedDate"];
        }        
    } else {
        $alert = '<div class="alert alert-danger" role="alert">ISBN não encontrado no Google Books</div>';
    }
    $isbnValue = $_REQUEST["isbn"];
    //print_r($record_isbn);
}

if (isset($_REQUEST["ID"])) {
    //print_r($_REQUEST);
    $query["doc"]["title"] = $_REQUEST["title"];
    if (!empty($_REQUEST["contributor"])) {
        $query["doc"]["contributor"] = explode(";", $_REQUEST["contributor"]);
    }
    $query["doc"]["editions"] = $_REQUEST["editions"];    
    $query["doc"]["publisher"] = $_REQUEST["publisher"];
    $query["doc"]["date"] = $_REQUEST["date"];
    $query["doc"]["languages"] = $_REQUEST["languages"];
    
    if (!empty($_REQUEST["physicalDescriptions"])) {
        $query["doc"]["physicalDescriptions"] = $_REQUEST["physicalDescriptions"];
    }      
    if (!empty($_REQUEST["isbn"])) {
        $query["doc"]["identifier"][0]["value"] = $_REQUEST["isbn"];
        $query["doc"]["identifier"][0]["type"] = "ISBN";
    }
    if (!empty($_REQUEST["subjects"])) {
        $subjectsArray = explode(";", $_REQUEST["subjects"]);
        foreach ($subjectsArray as $subjectsUnits) {
            $query["doc"]["subjects"][] = trim($subjectsUnits);
        }
    }
    if (!empty($_REQUEST["classifications"])) {
        $query["doc"]["classifications"] = $_REQUEST["classifications"];
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
        $contributorValue = implode(";", $cursor["_source"]["contributor"]);
    } else {
        $contributorValue = "";
    } 
    
    if (isset($cursor["_source"]["editions"])) {
        $editionsValue = $cursor["_source"]["editions"];
    } else {
        $editionsValue = "";
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
    
    if (isset($cursor["_source"]["physicalDescriptions"])) {
        $physicalDescriptionsValue = $cursor["_source"]["physicalDescriptions"];
    } else {
        $physicalDescriptionsValue = "";
    }
    
    if (isset($cursor["_source"]["languages"])) {
        $languagesValue = $cursor["_source"]["languages"];
    } else {
        $languagesValue = "";
    }    

    if (isset($cursor["_source"]["identifier"])) {
        $isbnValue = $cursor["_source"]["identifier"][0]["value"];
    } else {
        $isbnValue = "";
    }
    
    if (isset($cursor["_source"]["subjects"])) {
        $subjectsValue = implode(";", $cursor["_source"]["subjects"]);
    } else {
        $subjectsValue = "";
    }
    
    if (isset($cursor["_source"]["classifications"])) {
        $classificationsValue = $cursor["_source"]["classifications"];
    } else {
        $classificationsValue = "";
    }    

}

if (!isset($titleValue)) {
    $titleValue = "";
}
if (!isset($contributorValue)) {
    $contributorValue = "";
}
if (!isset($editionsValue)) {
    $editionsValue = "";
}
if (!isset($publisherValue)) {
    $publisherValue = "";
}
if (!isset($dateValue)) {
    $dateValue = "";
}
if (!isset($physicalDescriptionsValue)) {
    $physicalDescriptionsValue = "";
}
if (!isset($languagesValue)) {
    $languagesValue = "";
}
if (!isset($isbnValue)) {
    $isbnValue = "";
}
if (!isset($subjectsValue)) {
    $subjectsValue = "";
}
if (!isset($classificationsValue)) {
    $classificationsValue = "";
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

    <?php (isset($alert)? print_r($alert) : print_r("")); ?>

    <form action="editor.php" method="post" enctype="multipart/form-data">
    <div class="form-group row">
      <label for="ID" class="col-sm-2 col-form-label">ID</label>
      <div class="col-sm-10">
        <input type="text" readonly class="form-control-plaintext" id="ID" name="ID" value="<?php echo $uuid; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label for="title" class="col-sm-2 col-form-label">Título</label>
      <div class="col-sm-10">
          <input type="text" class="form-control" id="title" name="title" placeholder="Insira o título" value="<?php echo $titleValue; ?>" required>
      </div>
    </div>
    <div class="form-group row">
      <label for="contributor" class="col-sm-2 col-form-label">Autor</label>
      <div class="col-10">
          <input type="text" class="form-control" id="contributor" name="contributor" placeholder="Insira o autor no formato (SOBRENOME, Nome), caso tenha mais de um autor, separe por ponto e vírgula" value="<?php echo $contributorValue; ?>" required>
      </div>
    </div> 
    <div class="form-group row">
      <label for="editions" class="col-sm-2 col-form-label">Edição</label>
      <div class="col-10">
          <input type="text" class="form-control" id="editions" name="editions" placeholder="Insira o declaração de edição" value="<?php echo $editionsValue; ?>">
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
      <label for="physicalDescriptions" class="col-sm-2 col-form-label">Descrição física</label>
      <div class="col-10">
          <input type="text" class="form-control" id="physicalDescriptions" name="physicalDescriptions" placeholder="Insira a descrição física" value="<?php echo $physicalDescriptionsValue; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label for="languages" class="col-sm-2 col-form-label">Idioma</label>
      <div class="col-10">
          <select class="form-control" id="languages" name="languages">
            <option value="Português|por" selected>Português</option>
            <option value="Inglês|eng">Inglês</option>
            <option value="Espanhol|spa">Espanhol</option>
            <option value="Francês|fre">Francês</option>
            <option value="Indeterminado|und">Indeterminado</option>
          </select>
      </div>
    </div>              
    <div class="form-group row">
      <label for="date" class="col-sm-2 col-form-label">ISBN</label>
      <div class="col-sm-10">
          <input type="text" class="form-control" id="isbn" name="isbn" placeholder="Insira o ISBN (Somente os números)" pattern="\d*" value="<?php echo $isbnValue; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label for="date" class="col-sm-2 col-form-label">Assuntos</label>
      <div class="col-sm-10">
          <input type="text" class="form-control" id="subjects" name="subjects" placeholder="Insira os assuntos (separados por ponto e vírgula ;)"  value="<?php echo $subjectsValue; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label for="date" class="col-sm-2 col-form-label">Localização física</label>
      <div class="col-sm-10">
          <input type="text" class="form-control" id="classifications" name="classifications" placeholder="Insira a localização física" value="<?php echo $classificationsValue; ?>">
      </div>
    </div>    
    <div class="custom-file">
        <input type="file" class="custom-file-input" id="customFile" name="cover">
        <label class="custom-file-label" for="customFile">Selecionar arquivo de capa. Somente com nome usando o mesmo ISBN e jpg. Ex: 9788585637323.jpg)</label>
    </div> 
    <br/><br/>              
    <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
  </div>

  <script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
  </script>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
  <script src="https://getbootstrap.com/docs/4.3/assets/js/docs.min.js"></script>

  </body>
</html>