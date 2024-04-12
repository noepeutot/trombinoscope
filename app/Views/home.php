<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
            integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
            crossorigin="anonymous"></script>
</head>
<style>
    * {
        outline: red dashed 1px;
    }
</style>
<body>
<header>
    <h1>Trombinoscope</h1>
    <button type="button" class="btn btn-light">
        <img src="assets/images/account.svg" alt="compte" width="30px" height="30px">Se connecter
    </button>
</header>
<nav class="navbar navbar-light">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <input class="form-control form-control-sm mb-2" type="search" placeholder="Recherche" aria-label="Recherche">
            </div>
            <div class="col-auto">
                <div class="dropdown mb-2">
                    <button class="btn btn-secondary dropdown-toggle btn-light" type="button" id="statut"
                            data-bs-toggle="dropdown" aria-expanded="false">Statut
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statut">
                        <?php foreach ($statut as $value) { ?>
                            <li>
                                <input class="form-check-input" type="checkbox" aria-label="Chercheur">
                                <label><?php echo $value; ?></label>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-auto">
                <div class="dropdown mb-2">
                    <button class="btn btn-secondary dropdown-toggle btn-light" type="button" id="equipe"
                            data-bs-toggle="dropdown" aria-expanded="false">Equipe
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="equipe">
                        <?php foreach ($equipe as $value) { ?>
                            <li>
                                <input class="form-check-input" type="checkbox" aria-label="Chercheur">
                                <label><?php echo $value; ?></label>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-auto">
                <div class="dropdown mb-2">
                    <button class="btn btn-secondary dropdown-toggle btn-light" type="button" id="tuteur"
                            data-bs-toggle="dropdown" aria-expanded="false">Tuteur
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="tuteur">
                        <?php foreach ($tuteur as $value) { ?>
                            <li>
                                <input class="form-check-input" type="checkbox" aria-label="Chercheur">
                                <label><?php echo $value; ?></label>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<main>

    <div class="card" style="width: 15rem;">
        <img class="card-img-top" src="assets/images/pp.jpg" alt="photographie">
        <div class="card-body">
            <h5 class="card-title"></h5>
        </div>
    </div>
</main>
</body>
</html>
