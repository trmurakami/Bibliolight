<?php

require 'inc/config.php';
require 'inc/functions.php';

print_r($_REQUEST);
echo "<br/><br/>";
print_r($_FILES);

if (isset($_FILES['file'])) {

    $fh = fopen($_FILES['file']['tmp_name'], 'r+');
    $row = fgetcsv($fh, 108192, "\t");
    
    foreach ($row as $key => $value) {

        if ($value == "ID") {
            $rowNum["ID"] = $key;
        }
        if ($value == "Título") {
            $rowNum["title"] = $key;
        }
        if ($value == "Autor") {
            $rowNum["contributor"] = $key;
        }
        if ($value == "Editora") {
            $rowNum["publisher"] = $key;
        }        
        if ($value == "Ano de publicação") {
            $rowNum["date"] = $key;
        }
        if ($value == "Edição") {
            $rowNum["editions"] = $key;
        }         
        if ($value == "ISBN") {
            $rowNum["isbn"] = $key;
        }
        if ($value == "Assuntos") {
            $rowNum["subjects"] = $key;
        }
        if ($value == "Localização física") {
            $rowNum["classifications"] = $key;
        }                              
    }


    while (($row = fgetcsv($fh, 108192, "\t")) !== false) {
        $doc = Record::Build($row, $rowNum);
        $id = $row[$rowNum["ID"]];   
        //$sha256 = hash('sha256', ''.$row[$rowNum["ID"]].'');
        print_r($doc);
        if (!is_null($id)) {
            //$resultado_scopus = Elasticsearch::update($id, $doc);
        }        
        //print_r($resultado_scopus);
        //print_r($doc["doc"]["source_id"]);
        echo "<br/><br/><br/>";
        flush();        

    }
    fclose($fh);
}

//sleep(5);
//echo '<script>window.location = \'result_trabalhos.php?filter[]=type:"Work"&filter[]=tag:"'.$_POST["tag"].'"\'</script>';

class Record
{
    public static function build($row, $rowNum)
    {
        $doc["doc"]["title"] = str_replace('"', '', $row[$rowNum["title"]]);
        if (!empty($row[$rowNum["contributor"]])) {
            $doc["doc"]["contributor"] = explode(";", $row[$rowNum["contributor"]]);
        }
        $doc["doc"]["editions"] = $row[$rowNum["editions"]];
        $doc["doc"]["publisher"] = $row[$rowNum["publisher"]];
        if (!empty($row[$rowNum["subjects"]])) {
            $doc["doc"]["subjects"] = explode(";", $row[$rowNum["subjects"]]);
        }
        if (!empty($row[$rowNum["classifications"]])) {
            $doc["doc"]["classifications"] = $row[$rowNum["classifications"]];
        }                   
        $doc["doc"]["date"] = $row[$rowNum["date"]];


        $doc["doc_as_upsert"] = true;
        return $doc;
    }
}

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Tiago Murakami">
        <title>Importar registros</title>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    </head>
    <body>
        <?php include 'inc/navbar.php'; ?>

        <div class="container">

            <h1>Ferramenta de importação de dados</h1>

            <form class="mt-5" action="import.php" method="post" enctype="multipart/form-data">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="customFileLangHTML" name="file">
                    <label class="custom-file-label" for="customFileLangHTML" data-browse="Inserir arquivo">Selecione um arquivo CSV</label>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Enviar</button>        
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


