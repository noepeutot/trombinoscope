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
    <link href="<?= $cssUrl . 'bootstrap.min.v5.3.3.css' ?>" rel="stylesheet">
    <link href="<?= $cssUrl . 'dashboard.css' ?>" rel="stylesheet">
    <script src="<?= $jsUrl . 'popper.v2.11.8.js' ?>"></script>
    <script src="<?= $jsUrl . 'bootstrap.v5.3.3.js' ?>"></script>
    <script src="<?= $jsUrl . 'jquery.v3.7.1.js' ?>"></script>
</head>
<body>
<?= $this->include('backoffice/header') ?>
<main class="m-4 mt-2">
    <h1 class="h1 d-flex align-items-center">
        <img src="<?= $imgUrl . 'backoffice/star.svg' ?>" alt="étoile">
        Tableau de bord
    </h1>
    <p class="p">
        Vous pouvez visualiser les derniers utilisateurs ainsi que les dernières modifications qui ont été faites.
    </p>
    <section class="border rounded p-2 mb-4">
        <h4 class="h4">Utilisateurs ajoutés récemment</h4>
        <?php if (isset($personneRecente)):
            foreach ($personneRecente as $personne):?>
                <article class="row d-flex justify-content-start flex-row border rounded m-2 p-1">
                    <img class="col-sm-1 picture-user rounded-circle"
                         src="<?= $imgUrl . 'profile/valide/' . $personne->id_personne ?>" alt="photo">
                    <span class="col-sm-auto"><?= $personne->nom . ' ' . $personne->prenom ?></span>
                    <span class="col-sm-auto"><?= $personne->libelle ?></span>
                    <span class="col-sm-auto"><?= $personne->date_debut ?></span>
                    <span class="col-sm-auto"><?= $personne->date_fin ?></span>
                </article>
            <?php endforeach;
        else: ?>
            <p class="m-3">Aucune personne n’a été ajouté récemment…</p>
        <?php endif; ?>
    </section>
    <section class="border rounded p-2 mb-4">
        <h4 class="h4">Dernières modifications</h4>
        <div class="row d-flex flex-wrap justify-content-evenly m-2 gap-2">
            <?php if (isset($modificationRecente)):
                foreach ($modificationRecente as $modification):?>
                    <article class="col-sm card px-0">
                        <div class="card-header hstack gap-3">
                        <span class="fs-6">
<!--                            <img class="picture rounded-circle" alt="photo"-->
<!--                                 src="--><?php //= $imgUrl . 'profile/valide/' . $modification->id_personne ?><!--">-->
                            <?= $modification->nom . ' ' . $modification->prenom ?>
                        </span>
                            <span class="badge text-bg-secondary ms-auto">
                                <?= $modification->attribut ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if ($modification->attribut !== "Photo"): ?>
                                <div>
                                    <span class="fw-bold fst-italic me-2">
                                        Ancien
                                    </span>
                                    <span>
                                        <?php if ($modification->attribut === "Bureau" && isset($bureauAvant)) {
                                            echo $bureauAvant->numero;
                                        } elseif ($modification->attribut === "Statut" && isset($statutAvant)) {
                                            echo $statutAvant->nom;
                                        } elseif ($modification->attribut === "Equipe" && isset($equipeAvant)) {
                                            foreach ($equipeAvant as $equipe) {
                                                echo $equipe->nom_court;
                                                echo next($equipeAvant) ? ', ' : '';
                                            }
                                        } elseif ($modification->attribut === "Employeur" && isset($employeurAvant)) {
                                            foreach ($employeurAvant as $employeur) {
                                                echo $employeur->nom;
                                                echo next($employeurAvant) ? ', ' : '';
                                            }
                                        } else {
                                            echo $modification->avant;
                                        } ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-bold text-primary bg-primary-subtle border rounded position-relative px-2">
                                        Nouveau
                                        <img class="position-absolute top-0 start-100 translate-middle"
                                             src="<?= $imgUrl . 'backoffice/star-yellow.svg' ?>"
                                             alt="nouveau">
                                    </span>
                                    <span class="">
                                        <?php if ($modification->attribut === "Bureau" && isset($bureauApres)) {
                                            echo $bureauApres->numero;
                                        } elseif ($modification->attribut === "Statut" && isset($statutApres)) {
                                            echo $statutApres->nom;
                                        } elseif ($modification->attribut === "Equipe" && isset($equipeApres)) {
                                            foreach ($equipeApres as $equipe) {
                                                echo $equipe->nom_court;
                                                echo next($equipeApres) ? ', ' : '';
                                            }
                                        } elseif ($modification->attribut === "Employeur" && isset($employeurApres)) {
                                            foreach ($employeurApres as $employeur) {
                                                echo $employeur->nom;
                                                echo next($employeurApres) ? ', ' : '';
                                            }
                                        } else {
                                            echo $modification->apres;
                                        } ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fw-bold text-primary bg-primary-subtle border rounded position-relative px-2 mb-2">
                                        Nouveau
                                        <img class="position-absolute top-0 start-100 translate-middle"
                                             src="<?= $imgUrl . 'backoffice/star-yellow.svg' ?>"
                                             alt="nouveau">
                                    </span>
                                    <img class="rounded" src="<?= $modification->apres ?>"
                                         alt="Nouvelle photo" width="100px">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer text-body-secondary text-center">
                            <?php if ($modification->statut === "attente"): ?>
                                <span class="fw-medium bg-dark-subtle text-dark rounded px-2 d-inline-flex" disabled>
                                      En attente
                                      <img class="ms-1" src="<?= $imgUrl . 'backoffice/timer.svg' ?>" alt="attente">
                                </span>
                            <?php elseif ($modification->statut === "valide"): ?>
                                <span class="fw-medium bg-success-subtle text-success fst-italic rounded px-2">
                                    Validé
                                </span>
                            <?php else: ?>
                                <span class="fw-medium bg-danger-subtle text-danger fst-italic rounded px-2">
                                    Refusé
                                </span>
                            <?php endif; ?>

                        </div>
                    </article>
                <?php endforeach;
            else: ?>
                <p class="m-3">Aucune personne n’a été ajouté récemment…</p>
            <?php endif; ?>
        </div>
    </section>
</main>
</body>
</html>