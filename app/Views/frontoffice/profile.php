<?php $cssUrl = css_url('');
$jsUrl = js_url('');
$imgUrl = img_url('');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="<?= $cssUrl . 'profile.css' ?>" rel='stylesheet'>
    <link href="<?= $cssUrl . 'bootstrap.min.v5.3.3.css' ?>" rel="stylesheet">
    <script src="<?= $jsUrl . 'popper.v2.11.8.js' ?>"></script>
    <script src="<?= $jsUrl . 'bootstrap.v5.3.3.js' ?>"></script>
    <script src="<?= $jsUrl . 'jquery.v3.7.1.js' ?>"></script>
</head>
<style>
    body {
        background: url("<?= $imgUrl . 'wave_main.svg' ?>") no-repeat top;
        -webkit-background-size: 100% auto;
        -moz-background-size: 100% auto;
        -o-background-size: 100% auto;
        background-size: 100% auto;
    }
</style>
<body>
<?= $this->include('frontoffice/header') ?>
<?php if (isset($personne)) { ?>
    <main class="d-flex flex-column justify-content-center align-items-center">
        <section class="d-flex flex-column justify-content-center align-items-center mb-5">
            <img id="profilePicture" class="border border-5 border-light rounded-3"
                 src="<?= $imgUrl . 'profile/valide/' . $personne->id_personne . '.jpg' ?>"
                 alt="photographie">
            <h2><?= $personne->nom . ' ' . $personne->prenom ?></h2>

            <?php if (isset($mails)) { ?>
                <a href="mailto:<?= $mails->libelle ?>" class="link-offset-2 link-offset-1-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover mb-0"><?= $mails->libelle ?></a>
            <?php } else { ?>
                <p class="fst-italic mb-0"><?= "Aucun email professionnel" ?></p>
            <?php } ?>

            <?php if (isset($localisations)) {?>
                <div class="">
                <?php foreach ($localisations as $localisation) { ?>
                    <a href="tel:<?= $localisation->telephone ?>" class="link-offset-2 link-offset-1-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover mb-0"><?= $localisation->telephone ?></a>
                    <?= next($bureaux) ? ', ' : ''; ?>
                <?php } ?>
                </div>
            <?php } else { ?>
                <p class="fst-italic mb-0"><?= "Aucun téléphone professionnel" ?></p>
            <?php } ?>
        </section>

        <section class="row d-flex flex-row justify-content-between w-75">
            <?php if (isset($statut)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Catégorie</p>
                    <p class="fw-bold mb-0"><?= $statut->statut ?></p>
                </div>
            <?php } ?>
            <?php if (isset($equipes)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Equipe de rattachement</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($equipes as $equipe) { ?>
                            <span><?= $equipe->nom_court ?></span>
                            <?= next($equipes) ? ", " : ''; ?>
                        <?php } ?>
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
                            <?= next($responsabilites) ? ', ' : ''; ?>
                        <?php } ?>
                    </p>
                </div>
            <?php } ?>
            <?php if (isset($bureaux)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Bureau</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($bureaux as $bureau) { ?>
                            <span><?= $bureau->numero ?></span>
                            <?= next($bureaux) ? ', ' : ''; ?>
                        <?php } ?></p>
                </div>
            <?php } ?>
            <?php if (!empty($employeurs)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-1">Employeur⋅s</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($employeurs as $employeur) { ?>
                            <span><?= $employeur->nom_court ?></span>
                            <?= next($employeurs) ? ", " : ''; ?>
                        <?php } ?>
                    </p>
                </div>
            <?php } ?>
            <?php if (!empty($responsables)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-0">Responsable⋅s</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($responsables as $encadre) {
                            if (isset($encadre->id_personne)) { ?>
                                <a class="link-offset-2 link-offset-1-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover"
                                   href="<?= $encadre->id_personne ?>"><?= $encadre->nom . ' ' . $encadre->prenom ?></a>
                            <?php } else { ?>
                                <span><?= $encadre->nom . ' ' . $encadre->prenom ?></span>
                            <?php }
                            echo next($responsables) ? ', ' : '';
                        } ?>
                    </p>
                </div>
            <?php } ?>
            <?php if (!empty($encadres)) { ?>
                <div class="col-4 my-2">
                    <p class="fw-light mb-0">Encadrement</p>
                    <p class="fw-bold mb-0">
                        <?php foreach ($encadres as $encadre) {
                            if (isset($encadre->id_personne)) { ?>
                                <a class="link-offset-2 link-offset-1-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover"
                                   href="<?= $encadre->id_personne ?>"><?= $encadre->nom . ' ' . $encadre->prenom ?></a>
                            <?php } else { ?>
                                <span><?= $encadre->nom . ' ' . $encadre->prenom ?></span>
                            <?php }
                            echo next($encadres) ? ", " : '';
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
            <?php if (isset($sejour->date_fin) && $sejour->date_fin != "01/01/2100") { ?>
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