<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
            integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
            crossorigin="anonymous"></script>
</head>
<style>
    * {
        /*outline: red dashed 1px;*/
    }

    body {
        background-image: url("assets/images/wave_main.svg");
        background-repeat: no-repeat;
    }

    article:hover {
        transform: scale(0.98);
        transition: transform 200ms ease-in-out;
    }

    a img {
        object-fit: cover;
        object-position: top;
    }

</style>
<body>
<header class="d-flex justify-content-between align-items-center my-5">
    <h1 class="text-white mx-5">Trombinoscope</h1>
    <button type="button" class="btn btn-light d-flex align-items-center mx-5 px-4">
        <img class="me-2" src="assets/images/account.svg" alt="compte" width="30px" height="30px">
        <span>Se connecter</span>
    </button>
</header>
<nav class="navbar navbar-light">
    <div class="container-fluid d-flex justify-content-start ms-5">
        <div class="row align-items-center w-100 mb-4">
            <div class="col-3 input-group w-50">
                <span class="input-group-text border shadow bg-body">
                    <img class="" src="assets/images/search.svg" alt="statut" width="20px"
                         height="20px">
                </span>
                <input class="form-control border shadow bg-body" type="search" placeholder="Recherche"
                       aria-label="Recherche">
            </div>
            <div class="col-auto">
                <div class="dropdown">
                    <button class="btn btn-light shadow bg-body dropdown-toggle d-flex align-items-center px-4"
                            type="button" id="statut"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="me-2" src="assets/images/statut.svg" alt="statut" width="20px" height="20px">
                        <span>Statut</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statut">
                        <?php if (!empty($statut)) {
                            foreach ($statut as $value) { ?>
                                <li class="dropdown-item">
                                    <input id="<?php echo $value ?>" class="form-check-input" type="checkbox"
                                           value="<?php echo $value ?>" aria-label="Chercheur">
                                    <label for="<?php echo $value ?>"
                                           class="form-check-label"><?php echo $value; ?></label>
                                </li>
                            <?php }
                        } ?>
                    </ul>
                </div>
            </div>
            <div class="col-auto">
                <div class="dropdown">
                    <button class="btn btn-light shadow bg-body dropdown-toggle d-flex align-items-center px-4"
                            type="button" id="equipe"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="me-2" src="assets/images/group.svg" alt="equipe" width="20px" height="20px">
                        <span>Equipe</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="equipe">
                        <?php if (!empty($equipe)) {
                            foreach ($equipe as $value) { ?>
                                <li class="dropdown-item">
                                    <input id="<?php echo $value ?>" class="form-check-input" type="checkbox"
                                           aria-label="Chercheur">
                                    <label for="<?php echo $value ?>"
                                           class="form-check-label"><?php echo $value; ?></label>
                                </li>
                            <?php }
                        } ?>
                    </ul>
                </div>
            </div>
            <div class="col-auto">
                <div class="dropdown">
                    <button class="btn btn-light shadow bg-body dropdown-toggle d-flex align-items-center px-4"
                            type="button" id="tuteur"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="me-2" src="assets/images/user.svg" alt="tuteur" width="20px" height="20px">
                        <span>Tuteur</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="tuteur">
                        <?php if (!empty($tuteur)) {
                            foreach ($tuteur as $value) { ?>
                                <li class="dropdown-item">
                                    <input id="<?php echo $value ?>" class="form-check-input" type="checkbox"
                                           aria-label="Chercheur">
                                    <label for="<?php echo $value ?>"
                                           class="form-check-label"><?php echo $value; ?></label>
                                </li>
                            <?php }
                        } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<main>
    <div class="d-flex justify-content-center flex-wrap">
        <?php if (!empty($personnels)) {
            foreach ($personnels as $value) { ?>
                <article>
                    <a class="link-offset-2 link-underline link-underline-opacity-0 card shadow bg-body m-2 p-4"
                       style="width: 14rem;"
                       href="<?=base_url('profile/').$value['id_personne']?>">
                        <img class="card-img-top" src="assets/images/pp.jpg" height="200px" alt="photographie">
                        <div class="pb-0 pt-3 object-fit-contain">
                            <h6 class="card-title text-center mb-0"><?php echo $value["prenom"] . " " . $value["nom_usage"] ?></h6>
                        </div>
                    </a>
                </article>
            <?php }
        } ?>
    </div>
</main>
</body>
</html>
