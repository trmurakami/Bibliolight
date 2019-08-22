<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
    <h5 class="my-0 mr-md-auto font-weight-normal"><a href="index.php">Biblioteca Bibliolight</a></h5>
    <nav class="my-2 my-md-0 mr-md-3">
      <form class="form-inline my-2 my-lg-0" action="search.php" method="get">
        <input class="form-control mr-sm-2" type="search" placeholder="Termo de busca" aria-label="Pesquisar" name="search[]">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Pesquisar</button>
      </form>
    </nav>     
    <nav class="my-2 my-md-0 mr-md-3">
      <form class="form-inline my-2 my-lg-0" action="editor.php" method="get">
        <input class="form-control mr-sm-2" type="search" placeholder="Pesquisar ISBN para adicionar registro" aria-label="ISBN" name="isbn">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Pesquisar ISBN no Google Books</button>
      </form>      
    </nav>
    <nav class="my-2 my-md-0 mr-md-3">
        <a class="btn btn-outline-success" href="editor.php">Criar registro</a>
    </nav>    
    <a class="btn btn-outline-primary" href="login.php">Login</a>
</div>
