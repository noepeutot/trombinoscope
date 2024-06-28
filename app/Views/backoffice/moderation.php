<?php $cssUrl = css_url('');
$jsUrl = js_url('');
$imgUrl = img_url('');
$baseUrl = base_url('backoffice/moderation');
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
        Modération
    </h1>
    <p class="p">
        Vous pouvez voir les dernières modifications faites sur les profils et l’historique de modération.
    </p>
    <section class="border rounded p-2 mb-4 position-relative">
        <h4 class="h4">En Attente
            (<?= $nombreEnAttente ?? 0 ?>)
        </h4>
        <a class="d-inline-flex flex-row flex-nowrap align-middle btn btn-light border position-absolute top-0 end-0 translate-middle"
           href="<?= $baseUrl . '/en-attente' ?>">
            Voir plus
            <img class="ms-2" alt="voir plus" src="<?= $imgUrl . 'backoffice/go-arrow.svg' ?>">
        </a>
        <?php if (!empty($modificationEnAttente)): ?>
            <div class="row d-flex flex-wrap justify-content-evenly m-2 gap-2 ">
                <?php foreach ($modificationEnAttente as $modification): ?>
                    <article class="col-md card shadow px-0">
                        <div class="card-header hstack gap-3">
                        <span class="fs-6">
                            <?= $modification->nom . ' ' . $modification->prenom ?>
                        </span>
                            <span class="badge text-bg-secondary ms-auto">
                                <?= $modification->attribut ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if ($modification->attribut !== "Photo"): ?>
                                <div class="d-flex flex-column align-items-center mb-3">
                                    <span class="fw-bold fst-italic me-2">
                                        Ancien
                                    </span>
                                    <span class="text-center text-break text-truncate-span">
                                        <?php if ($modification->attribut === "Bureau") {
                                            $bureauxAvant =  $modification->bureauxAvant;
                                            foreach ($bureauxAvant as $bureauAvant) {
                                                echo $bureauAvant->numero;
                                                echo next($bureauxAvant) ? ', ' : '';
                                            }
                                        } elseif ($modification->attribut === "Statut") {
                                            echo $modification->statutAvant->statut;
                                        } elseif ($modification->attribut === "Equipe") {
                                            $equipeAvant = $modification->equipeAvant;
                                            foreach ($equipeAvant as $equipe) {
                                                echo $equipe->nom_court;
                                                echo next($equipeAvant) ? ', ' : '';
                                            }
                                        } elseif ($modification->attribut === "Employeur") {
                                            $employeurAvant = $modification->employeurAvant;
                                            foreach ($employeurAvant as $employeur) {
                                                echo $employeur->nom;
                                                echo next($employeurAvant) ? ', ' : '';
                                            }
                                        } else {
                                            echo $modification->avant;
                                        } ?>
                                    </span>
                                </div>
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fw-bold text-primary bg-primary-subtle border rounded position-relative px-2">
                                        Nouveau
                                        <img class="position-absolute top-0 start-100 translate-middle"
                                             src="<?= $imgUrl . 'backoffice/star-yellow.svg' ?>"
                                             alt="nouveau">
                                    </span>
                                    <span class="text-center text-break text-truncate-span">
                                        <?php if ($modification->attribut === "Bureau") {
                                            $bureauxApres =  $modification->bureauxApres;
                                            foreach ($bureauxApres as $bureauApres) {
                                                echo $bureauApres->numero;
                                                echo next($bureauxApres) ? ', ' : '';
                                            }
                                        } elseif ($modification->attribut === "Statut") {
                                            echo $modification->statutApres->statut;
                                        } elseif ($modification->attribut === "Equipe") {
                                            $equipeApres = $modification->equipeApres;
                                            foreach ($equipeApres as $equipe) {
                                                echo $equipe->nom_court;
                                                echo next($equipeApres) ? ', ' : '';
                                            }
                                        } elseif ($modification->attribut === "Employeur") {
                                            $employeurApres = $modification->employeurApres;
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
                            <span class="fw-medium bg-dark-subtle text-dark rounded px-2 d-inline-flex" disabled>
                                  En attente
                                  <img class="ms-1" src="<?= $imgUrl . 'backoffice/timer.svg' ?>" alt="attente">
                            </span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="col m-3">Aucune modification n’est en attente de validation…</p>
        <?php endif; ?>
    </section>

    <section class="border rounded p-2 mb-4  position-relative">
        <h4 class="h4">Historique
            (<?= $nombreHistorique ?? 0 ?>)
        </h4>
        <a class="d-inline-flex flex-row flex-nowrap align-middle btn btn-light border position-absolute top-0 end-0 translate-middle"
           href="<?= $baseUrl . '/historique' ?>">
            Voir plus
            <img class="ms-2" alt="voir plus" src="<?= $imgUrl . 'backoffice/go-arrow.svg' ?>">
        </a>
        <?php if (!empty($modificationHistorique)): ?>
            <div class="row d-flex flex-wrap justify-content-evenly m-2 gap-2">
                <?php foreach ($modificationHistorique as $modification): ?>
                    <article class="col-sm card shadow px-0">
                        <div class="card-header hstack gap-3">
                        <span class="fs-6">
                            <?= $modification->nom . ' ' . $modification->prenom ?>
                        </span>
                            <span class="badge text-bg-secondary ms-auto">
                                <?= $modification->attribut ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if ($modification->attribut !== "Photo"): ?>
                                <div class="d-flex flex-column align-items-center mb-3">
                                    <span class="fw-bold fst-italic me-2">
                                        Ancien
                                    </span>
                                    <span class="text-center text-break text-truncate-span">
                                        <?php if ($modification->attribut === "Bureau") {
                                            $bureauxAvant =  $modification->bureauxAvant;
                                            foreach ($bureauxAvant as $bureauAvant) {
                                                echo $bureauAvant->numero;
                                                echo next($bureauxAvant) ? ', ' : '';
                                            }
                                        } elseif ($modification->attribut === "Statut") {
                                            echo $modification->statutAvant->statut;
                                        } elseif ($modification->attribut === "Equipe") {
                                            $equipeAvant = $modification->equipeAvant;
                                            foreach ($equipeAvant as $equipe) {
                                                echo $equipe->nom_court;
                                                echo next($equipeAvant) ? ', ' : '';
                                            }
                                        } elseif ($modification->attribut === "Employeur") {
                                            $employeurAvant = $modification->employeurAvant;
                                            foreach ($employeurAvant as $employeur) {
                                                echo $employeur->nom;
                                                echo next($employeurAvant) ? ', ' : '';
                                            }
                                        } else {
                                            echo $modification->avant;
                                        } ?>
                                    </span>
                                </div>
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fw-bold text-primary bg-primary-subtle border rounded position-relative px-2">
                                        Nouveau
                                        <img class="position-absolute top-0 start-100 translate-middle"
                                             src="<?= $imgUrl . 'backoffice/star-yellow.svg' ?>"
                                             alt="nouveau">
                                    </span>
                                    <span class="text-center text-break text-truncate-span">
                                        <?php if ($modification->attribut === "Bureau") {
                                            $bureauxApres =  $modification->bureauxApres;
                                            foreach ($bureauxApres as $bureauApres) {
                                                echo $bureauApres->numero;
                                                echo next($bureauxApres) ? ', ' : '';
                                            }
                                        } elseif ($modification->attribut === "Statut") {
                                            echo $modification->statutApres->statut;
                                        } elseif ($modification->attribut === "Equipe") {
                                            $equipeApres = $modification->equipeApres;
                                            foreach ($equipeApres as $equipe) {
                                                echo $equipe->nom_court;
                                                echo next($equipeApres) ? ', ' : '';
                                            }
                                        } elseif ($modification->attribut === "Employeur") {
                                            $employeurApres = $modification->employeurApres;
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
                            <?php if ($modification->statut === "valide"): ?>
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
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="col m-3">Aucune modification n’est enregistrée à l’heure actuelle…</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>