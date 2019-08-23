<?php
    require 'inc/config.php';
    require 'inc/functions.php';
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Tiago Murakami">
        <title><?php echo $library_name; ?></title>

        <link rel="canonical" href="https://github.com/trmurakami/bibliolight">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <style>
            .bd-placeholder-img {
                font-size: 1.125rem;
                text-anchor: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            @media (min-width: 768px) {
                .bd-placeholder-img-lg {
                font-size: 3.5rem;
                }
            }
        </style>

    </head>
    <body>            

        <?php include 'inc/navbar.php'; ?>

        <div class="px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
            <h1 class="display-4"><?php echo $library_name; ?></h1>
            <p class="lead">Ferramenta de gestão de acervo da <?php echo $library_name; ?>.</p>

            <div class="container">
                <form action="search.php" method="get">
                    <div class="form-group">
                        <label for="searchHelp">Pesquisar</label>
                        <input type="text" class="form-control" id="search" name="search[]" aria-describedby="searchHelp" placeholder="Digite a expressão de busca">
                        <small id="searchHelp" class="form-text text-muted">Você pode pesquisar por título, autor, editora...</small>
                        <small id="searchHelp" class="form-text text-muted">Para pesquisar por parte de uma palavra, digite a parte com asterisco. Ex.: biblio*</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                </form>
            </div>
            </div>
            <div class="container">
                <footer class="pt-4 my-md-5 pt-md-5 border-top">
                <div class="row">
                    <div class="col-12 col-md">
                        <!-- <img class="mb-2" src="/docs/4.3/assets/brand/bootstrap-solid.svg" alt="" width="24" height="24"> -->
                        <small class="d-block mb-3 text-muted">Desenvolvido com <a target="_blank" rel="noopener noreferrer nofollow" href="https://trmurakami.github.io/bibliolight">Bibliolight</a></small>
                        <small class="d-block mb-3 text-muted">Você pode <a target="_blank" rel="noopener noreferrer nofollow" href="https://pag.ae/7VbJhhRHP">contribuir com o Bibliolight</a></small>
                    </div>
                    <div class="col-6 col-md">
                        <h5>Estatísticas</h5>
                        <ul class="list-unstyled text-small">
                            <li><a class="text-muted" href="search.php">Quantidade de registros: <?php echo Homepage::numberOfRecords(); ?></a></li>
                            <li><a class="text-muted" href="search.php?search[]=_exists_:circ.name">Quantidade de empréstimos: <?php echo Homepage::numberCirc(); ?></a></li>
                        </ul>
                    </div>                    
                    <div class="col-6 col-md">
                    <h5>Operações</h5>
                        <ul class="list-unstyled text-small">
                            <li><a class="text-muted" href="tools/export.php?format=table">Exportar todos os registros em CSV</a></li>
                            <li><a class="text-muted" href="editor.php">Criar novo registro</a></li>
                        </ul>
                    </div>
                    <!--
                    <div class="col-6 col-md">
                    <h5>About</h5>
                    <ul class="list-unstyled text-small">
                    <li><a class="text-muted" href="#">Team</a></li>
                    <li><a class="text-muted" href="#">Locations</a></li>
                    <li><a class="text-muted" href="#">Privacy</a></li>
                    <li><a class="text-muted" href="#">Terms</a></li>
                    </ul>
                    </div>
                    -->
                </div>
                </footer>
            </div>

    </body>
</html>
