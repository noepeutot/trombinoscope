<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="<?= css_url('profile') ?>" rel='stylesheet'>
    <link href="<?= css_url('bootstrap.min.v5.3.3') ?>" rel="stylesheet">
    <script src="<?= js_url('popper.v2.11.8') ?>"></script>
    <script src="<?= js_url('bootstrap.v5.3.3') ?>"></script>
    <script src="<?= js_url('jquery.v3.7.1') ?>"></script>
</head>
<style>
    body {
        background: url("<?= img_url('wave_main.svg') ?>") no-repeat top;
        -webkit-background-size: 100% auto;
        -moz-background-size: 100% auto;
        -o-background-size: 100% auto;
        background-size: 100% auto;
    }
</style>
<body>
<?= $this->include('header') ?>
<?php if (isset($personne)) { ?>
    <main class="d-flex flex-column justify-content-center align-items-center">
        <section class="d-flex flex-column justify-content-center align-items-center mb-5">
            <img id="profilePicture" class="border border-5 border-light rounded-3"
                 src="<?= img_url('profile/' . $personne->id_personne . '.jpg') ?>"
                 alt="photographie">
            <h2><?= $personne->nom . ' ' . $personne->prenom ?></h2>

            <?php if (isset($mails)) { ?>
                <p class="mb-0"><?= $mails->libelle ?></p>
            <?php } else { ?>
                <p class="fst-italic mb-0"><?= "Aucun email professionnel" ?></p>
            <?php } ?>

            <?php if (isset($personne->telephone)) { ?>
                <p class="mb-0"><?= $personne->telephone ?></p>
            <?php } else { ?>
                <p class="fst-italic mb-0"><?= "Aucun téléphone professionnel" ?></p>
            <?php } ?>
        </section>

        <section class="row d-flex flex-row justify-content-between w-75">
            <?php if (isset($statut)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Catégorie</p>
                    <p class="fw-bold mb-0"><?= $statut->nom ?></p>
                </div>
            <?php } ?>
            <?php if (isset($equipes)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Equipe de rattachement</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($equipes as $equipe) { ?>
                            <span><?= $equipe->nom_court ?></span>
                            <?php if (next($equipes)) {
                                echo ", ";
                            }
                        } ?>
                    </p>
                </div>
            <?php } ?>
            <?php if (isset($sejour->sujet)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-0">Activités</p>
                    <p class="fw-bold mb-0"><?= $sejour->sujet ?></p>
                </div>
            <?php } ?>

            <?php if (!empty($responsabilites)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Résponsabilités</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($responsabilites as $responsabilite) { ?>
                            <span><?= $responsabilite->libelle ?></span>
                            <?php if (next($responsabilites)) {
                                echo ", ";
                            }
                        } ?>
                    </p>
                </div>
            <?php } ?>
            <?php if (isset($bureau)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Bureau</p>
                    <p class="fw-bold mb-0"><?= $bureau->numero ?></p>
                </div>
            <?php } ?>
            <?php if (!empty($employeurs)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Employeur⋅s</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($employeurs as $employeur) { ?>
                            <span><?= $employeur->nom_court ?></span>
                            <?php if (next($employeurs)) {
                                echo ", ";
                            }
                        } ?>
                    </p>
                </div>
            <?php } ?>
            <?php if (!empty($responsables)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-0">Responsable⋅s</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($responsables as $encadre) {
                            if (isset($encadre->id_personne)) { ?>
                                <a class="link-offset-1"
                                   href="<?= $encadre->id_personne ?>"><?= $encadre->nom . ' ' . $encadre->prenom ?></a>
                            <?php } else { ?>
                                <span><?= $encadre->nom . ' ' . $encadre->prenom ?></span>
                            <?php }
                            if (next($responsables)) {
                                echo ", ";
                            }
                        } ?>
                    </p>
                </div>
            <?php } ?>
            <?php if (!empty($encadres)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-0">Co-Encadrement de recherche</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($encadres as $encadre) {
                            if (isset($encadre->id_personne)) { ?>
                                <a class="link-offset-1"
                                   href="<?= $encadre->id_personne ?>"><?= $encadre->nom . ' ' . $encadre->prenom ?></a>
                            <?php } else { ?>
                                <span><?= $encadre->nom . ' ' . $encadre->prenom ?></span>
                            <?php }
                            if (next($encadres)) {
                                echo ", ";
                            }
                        } ?>
                    </p>
                </div>
            <?php } ?>
            <?php if (isset($sejour->date_debut)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Date d’entrée</p>
                    <p class="fw-bold mb-0"><?= $sejour->date_debut ?></p>
                </div>
            <?php } ?>
            <?php if (isset($sejour->date_fin)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Date de départ</p>
                    <p class="fw-bold mb-0"><?= $sejour->date_fin ?></p>
                </div>
            <?php } ?>
        </section>
    </main>
<?php } ?>
</body>
</html>