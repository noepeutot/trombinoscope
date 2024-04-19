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
        /*outline: red dashed 1px;*/
    }

    body {
        background-image: url("<?= img_url('wave_main.svg') ?>");
        background-repeat: no-repeat;
    }

    article:hover {
        transform: scale(0.98);
        transition: transform 200ms ease-in-out;
    }

    #profilePicture {
        object-fit: cover;
        object-position: top;
        height: 200px;
    }

</style>
<body>
<header class="d-flex justify-content-between align-items-center my-5">
    <a type="button" class="btn btn-light d-flex align-items-center mx-5 px-4" href="<?= base_url('/') ?>">
        <img class="me-2" src="<?= img_url('back_arrow.svg') ?>" alt="retour" height="20px">
        <span>Retour</span>
    </a>
    <button type="button" class="btn btn-light d-flex align-items-center mx-5 px-4">
        <img class="me-2" src="<?= img_url('account.svg') ?>" alt="compte" width="30px" height="30px">
        <span>Se connecter</span>
    </button>
</header>
<?php if (isset($personnes) && isset($personnels)) { ?>
    <main class="d-flex flex-column justify-content-center align-items-center">
        <section class="d-flex flex-column justify-content-center align-items-center mb-5">
            <img id="profilePicture" class="border border-5 border-light rounded-3"
                 src="<?= img_url('profile_picture.jpg') ?>"
                 alt="photographie">
            <h2><?= $personnes['nom_usage'] . ' ' . $personnes['prenom'] ?></h2>

            <?php if (isset($personnes['mails_pro'][0]['mail'])) { ?>
                <p class="mb-0"><?= $personnes['mails_pro'][0]['mail'] ?></p>
            <?php } else { ?>
                <p class="fst-italic mb-0"><?= "Aucun email professionnel" ?></p>
            <?php } ?>

            <?php if (isset($localisation['tel_professionnel'])) { ?>
                <p class="mb-0"><?= $localisation['tel_professionnel'] ?></p>
            <?php } else { ?>
                <p class="fst-italic mb-0"><?= "Aucun téléphone professionnel" ?></p>
            <?php } ?>
        </section>

        <section class="row d-flex flex-row justify-content-between w-75">
            <?php if (isset($personnels['statut'])) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Catégorie</p>
                    <p class="fw-bold mb-0"><?= $personnels['statut'] ?></p>
                </div>
            <?php } ?>
            <?php if (isset($personnels['equipes'])) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Equipe de rattachement</p>
                    <p class="fw-bold mb-0"><?= $personnels['equipes'] ?></p>
                </div>
            <?php } ?>

            <?php if (isset($responsabilites)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Résponsabilités</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($responsabilites as $responsabilite) { ?>
                            <span><?= $responsabilite['responsabilite'] ?></span>
                            <?php if (next($sejours['financements'])) {
                                echo ", ";
                            }
                        } ?>
                    </p>
                </div>
            <?php } ?>
            <?php if (isset($personnels['date_debut_sejour'])) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Date d'entrée</p>
                    <p class="fw-bold mb-0"><?= $personnels['date_debut_sejour'] ?></p>
                </div>
            <?php } ?>

            <?php if (isset($localisation['numero_bureau'])) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Bureau</p>
                    <p class="fw-bold mb-0"><?= $localisation['numero_bureau'] ?></p>
                </div>
            <?php } ?>
            <?php if (isset($sejours['financements'])) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Employeur</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($sejours['financements'] as $financement) { ?>
                            <span><?= $financement['org_payeur']['nom_court_op'] ?></span>
                            <?php if (next($sejours['financements'])) {
                                echo ", ";
                            }
                        } ?>
                    </p>
                </div>
            <?php } ?>
            <?php if (isset($localisation['date_fin_sejour'])) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-0">Date de départ</p>
                    <p class="fw-bold mb-0"><?= $personnels['date_fin_sejour'] ?></p>
                </div>
            <?php } ?>

            <?php if (isset($encadrants)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-0">Responsable(s)</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($encadrants as $encadrant) {
                            if (isset($encadrant['id_personne'])) { ?>
                                <a class="link-offset-1"
                                   href="<?= $encadrant['id_personne'] ?>"><?= $encadrant['nom'] . ' ' . $encadrant['prenom'] ?></a>
                            <?php } else { ?>
                                <span><?= $encadrant['nom'] . ' ' . $encadrant['prenom'] ?></span>
                            <?php }
                            if (next($encadrants)) {
                                echo ", ";
                            }
                        } ?>
                    </p>
                </div>
            <?php } ?>
        </section>
    </main>
<?php } ?>
</body>
</html>