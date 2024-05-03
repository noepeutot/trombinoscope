<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="<?= css_url('home') ?>" rel='stylesheet'>
    <link href="<?= css_url('bootstrap.min.v5.3.3') ?>" rel="stylesheet">
    <script src="<?= js_url('popper.v2.11.8') ?>"></script>
    <script src="<?= js_url('bootstrap.v5.3.3') ?>"></script>
    <script src="<?= js_url('jquery.v3.7.1') ?>"></script>
</head>
<style>
    body {
        background-image: url("<?= img_url('wave_main.svg') ?>");
        background-repeat: no-repeat;
    }
</style>
<body>
<header class="d-flex justify-content-between align-items-center my-5">
    <a href="<?= base_url() ?>" class="link-underline link-underline-opacity-0"><h1 class="text-white mx-5">
            Trombinoscope</h1></a>
    <button type="button" class="btn btn-light d-flex align-items-center mx-5 px-4">
        <img class="me-2" src="<?= img_url('account.svg') ?>" alt="compte" width="30px" height="30px">
        <span>Se connecter</span>
    </button>
</header>
<nav class="navbar navbar-light">
    <div class="container-fluid d-flex justify-content-start ms-5">
        <form method="get" class="row align-items-center w-100">
            <div class="col-3 input-group w-35 mb-4">
                <span class="input-group-text border shadow bg-body">
                    <img class="" src="<?= img_url('search.svg') ?>" alt="statut" width="20px"
                         height="20px">
                </span>
                <input class="form-control border rounded-end shadow bg-body" type="search" placeholder="Recherche"
                       aria-label="Recherche" name="q" list="listePersonnel" data-bs-toggle="tooltip"
                       data-bs-delay="300"
                       data-bs-placement="top" data-bs-title="Entrer pour rechercher" value="<?php if (isset($query))
                    echo $query; ?>">
                <datalist id="listePersonnel">
                    <?php if (isset($allPersonnels)) {
                    foreach ($allPersonnels as $personne) { ?>
                    <option class="autocomplete" value="<?= $personne->prenom . ' ' . $personne->nom ?>">
                        <?php }
                        } ?>
                </datalist>
            </div>
            <div class="col-auto mb-4">
                <div class="dropdown">
                    <button name="statut"
                            class="btn btn-light shadow bg-body dropdown-toggle d-flex align-items-center px-4"
                            type="button" id="statut"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="me-2" src="<?= img_url('statut.svg') ?>" alt="statut" width="20px" height="20px">
                        <span>Statut</span>
                    </button>
                    <ul class="dropdown-menu scrollable-menu" aria-labelledby="statut">
                        <?php if (!empty($statut)) {
                            foreach ($statut as $value) { ?>
                                <li class="dropdown-item">
                                    <input id="<?= $value['statut'] ?>" class="form-check-input" type="checkbox"
                                           value="<?= $value['statut'] ?>" aria-label="Chercheur" name="statut[]"
                                        <?php if (isset($filtreStatut) && in_array($value['statut'], $filtreStatut))
                                            echo 'checked';
                                        ?>>
                                    <label for="<?= $value['statut'] ?>"
                                           class="form-check-label"><?= $value['statut'] ?></label>
                                </li>
                            <?php }
                        } ?>
                    </ul>
                </div>
            </div>
            <div class="col-auto mb-4">
                <div class="dropdown">
                    <button class="btn btn-light shadow bg-body dropdown-toggle d-flex align-items-center px-4"
                            type="button" id="equipe"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="me-2" src="<?= img_url('group.svg') ?>" alt="equipe" width="20px" height="20px">
                        <span>Equipe</span>
                    </button>
                    <ul class="dropdown-menu scrollable-menu" aria-labelledby="equipe">
                        <?php if (!empty($equipe)) {
                            foreach ($equipe as $value) { ?>
                                <li class="dropdown-item">
                                    <input id="<?= $value['nom_court_groupe'] ?>" class="form-check-input"
                                           type="checkbox"
                                           aria-label="Chercheur" name="equipe[]"
                                           value="<?= $value['nom_court_groupe'] ?>"
                                        <?php if (isset($filtreEquipe) && in_array($value['nom_court_groupe'], $filtreEquipe))
                                            echo 'checked';
                                        ?>>
                                    <label for="<?= $value['nom_court_groupe'] ?>"
                                           class="form-check-label"><?= $value['nom_court_groupe'] ?></label>
                                </li>
                            <?php }
                        } ?>
                    </ul>
                </div>
            </div>
            <div class="col-auto mb-4">
                <div class="dropdown">
                    <button class="btn btn-light shadow bg-body dropdown-toggle d-flex align-items-center px-4"
                            type="button" id="tuteur"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="me-2" src="<?= img_url('user.svg') ?>" alt="tuteur" width="20px" height="20px">
                        <span>Tuteur</span>
                    </button>
                    <ul class="dropdown-menu scrollable-menu" aria-labelledby="tuteur">
                        <?php if (!empty($tuteur)) {
                            foreach ($tuteur as $value) { ?>
                                <li class="dropdown-item">
                                    <input id="<?= $value['id_personne'] ?>" class="form-check-input" type="checkbox"
                                           aria-label="Chercheur" name="tuteur[]"
                                           value="<?= $value['prenom'] . ' ' . $value['nom'] ?>"
                                        <?php if (isset($filtreTuteur) && in_array($value['prenom'] . ' ' . $value['nom'], $filtreTuteur))
                                            echo 'checked';
                                        ?>>
                                    <label for="<?= $value['id_personne'] ?>"
                                           class="form-check-label"><?= $value['prenom'] . ' ' . $value['nom'] ?></label>
                                </li>
                            <?php }
                        } ?>
                    </ul>
                </div>
            </div>
            <div class="col-auto mb-4">
                <button id="reset" type="button" class="btn btn-danger btn-sm">Réinitialiser les filtres</button>
            </div>
        </form>
    </div>
</nav>
<main>
    <div class="d-flex justify-content-center flex-wrap">
        <?php if (!empty($personnes)) {
            foreach ($personnes as $value) { ?>
                <article>
                    <a class="link-offset-2 link-underline link-underline-opacity-0 card shadow bg-body m-2 p-4"
                       style="width: 14rem;"
                       href="<?= base_url('profile/') . $value['id_personne'] ?>">
                        <img class="card-img-top" src="<?= img_url('profile/' . $value['id_personne'] . '.jpg') ?>"
                             height="200px"
                             alt="photographie">
                        <div class="pb-0 pt-3 object-fit-contain">
                            <h6 class="card-title text-center mb-0"><?= $value['prenom'] . " " . $value['nom'] ?></h6>
                        </div>
                    </a>
                </article>
            <?php }
        } ?>
    </div>
</main>
</body>
<script src="<?= js_url('home') ?>"></script>
</html>