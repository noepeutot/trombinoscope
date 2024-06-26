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
    <link href="<?= $cssUrl . 'attente.css' ?>" rel="stylesheet">
    <link href="<?= $cssUrl . 'bootstrap.min.v5.3.3.css' ?>" rel="stylesheet">
    <link href="<?= $cssUrl . 'dashboard.css' ?>" rel="stylesheet">
    <script src="<?= $jsUrl . 'popper.v2.11.8.js' ?>"></script>
    <script src="<?= $jsUrl . 'bootstrap.v5.3.3.js' ?>"></script>
    <script src="<?= $jsUrl . 'jquery.v3.7.1.js' ?>"></script>
</head>
<body>
<?= $this->include('backoffice/header') ?>
<main class="m-4 mt-2">
    <a id="back" type="button" class="btn btn-light border px-4 my-2"
       href="<?= $baseUrl?>">
        <img class="me-2" src="<?= img_url('back_arrow.svg') ?>" alt="retour" width="20px">
        <span>Retour</span>
    </a>
    <h1 class="h1 d-flex align-items-center">
        <img src="<?= $imgUrl . 'backoffice/star.svg' ?>" alt="étoile">
        Modération - Historique
    </h1>
    <p class="p">
        Vous pouvez voir l’historique des modifications validées sur des profils d’utilisateurs.
    </p>
    <section class="border rounded p-2 mb-4">
        <h4 class="h4">En Attente
            (<?= $nombreHistorique ?? 0 ?>)
        </h4>
        <?php if (!empty($modificationHistorique)): ?>
            <div class="d-flex flex-wrap justify-content-evenly m-2 gap-2">
                <?php foreach ($modificationHistorique as $modification): ?>
                    <article class="card shadow px-0">
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
                                            echo $modification->bureauAvant->numero;
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
                                            echo $modification->bureauApres->numero;
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
            <p class="col m-3">Aucune modification n’est en attente de validation…</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>