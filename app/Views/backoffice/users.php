<?php $cssUrl = css_url('');
$jsUrl = js_url('');
$imgUrl = img_url('');
$baseUrl = base_url();
$uriString = uri_string();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="<?= $cssUrl . 'bootstrap.min.v5.3.3.css' ?>" rel="stylesheet">
    <link href="<?= $cssUrl . 'users.css' ?>" rel="stylesheet">
    <script src="<?= $jsUrl . 'popper.v2.11.8.js' ?>"></script>
    <script src="<?= $jsUrl . 'bootstrap.v5.3.3.js' ?>"></script>
    <script src="<?= $jsUrl . 'jquery.v3.7.1.js' ?>"></script>
    <link href="<?= $cssUrl . 'bootstrap-select.min.v1.13.18.css' ?>" rel="stylesheet">
    <script src="<?= $jsUrl . 'bootstrap-select.v1.14.0.js' ?>"></script>
</head>
<body>
<?= $this->include('backoffice/header') ?>
<main class="m-4 mt-2">
    <h1 class="h1 d-flex align-items-center">
        <img src="<?= $imgUrl . 'backoffice/star.svg' ?>" alt="étoile">
        Utilisateurs
    </h1>
    <p class="p">
        Vous pouvez gérer les utilisateurs et leurs permissions ici.
    </p>
    <section class="p-2 mb-4">
        <nav class="mb-4">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link <?= $uriString === 'backoffice/users' ? 'active' : '' ?>" aria-current="page"
                       href="<?= $baseUrl . 'backoffice/users' ?>">Tout voir</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $uriString === 'backoffice/users/admin' ? 'active' : '' ?>"
                       href="<?= $baseUrl . 'backoffice/users/admin' ?>">Admin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $uriString === 'backoffice/users/modo' ? 'active' : '' ?>"
                       href="<?= $baseUrl . 'backoffice/users/modo' ?>">Modérateur</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $uriString === 'backoffice/users/normal' ? 'active' : '' ?>"
                       href="<?= $baseUrl . 'backoffice/users/normal' ?>">Normal</a>
                </li>
                <li id="recherche" class="ms-auto">
                    <form id="search" action="<?= $baseUrl . $uriString ?>" method="get">
                        <div class="input-group">
                            <span class="input-group-text border">
                                <img class="" src="<?= $imgUrl . 'search.svg' ?>" alt="statut" width="20px"
                                     height="20px">
                            </span>
                            <input id="search" class="form-control border rounded-end" type="search"
                                   placeholder="Recherche" aria-label="Recherche" name="q" value="<?= $query ?? '' ?>">
                        </div>
                    </form>
                </li>
                <li id="pagination" class="nav-item ms-auto">
                    <?= isset($pager) ? $pager->links() : '' ?>
                </li>
            </ul>
        </nav>
        <?php if (isset($users)): ?>
            <div class="table-responsive card">
                <table class="table table-striped table-hover m-0">
                    <thead class="table-secondary">
                    <tr>
                        <th scope="col">Nom</th>
                        <th scope="col">Statut</th>
                        <th scope="col">Rôle</th>
                        <th scope="col">Date de création</th>
                        <th scope="col">Date de départ</th>
                    </tr>
                    </thead>
                    <tbody class="table-group-divider">
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="align-middle lh-1">
                                <span class="fw-medium">
                                    <?= $user->nom . ' ' . $user->prenom ?>
                                </span>
                                <br>
                                <span class="fs-6 text fw-lighter">
                                    <small>
                                        <?= $user->libelle ?>
                                    </small>
                                </span>
                            </td>
                            <td class="align-middle"><?= $user->statut ?></td>
                            <td class="align-middle">
                                <form id="role-<?= $user->id_personne ?>" action="<?= $baseUrl . 'backoffice/users/changer-role' ?>"
                                      method="get" class="role">
                                    <label>
                                        <select class="select-role form-select form-select-sm" form="role-<?= $user->id_personne ?>"
                                                name="role">
                                            <option <?= $user->role === 'admin' ? 'selected' : '' ?>
                                                    value="admin <?= $user->id_personne ?>">Admin
                                            </option>
                                            <option <?= $user->role === 'modo' ? 'selected' : '' ?>
                                                    value="modo <?= $user->id_personne ?>">Modo
                                            </option>
                                            <option <?= $user->role === 'normal' ? 'selected' : '' ?>
                                                    value="normal <?= $user->id_personne ?>">Normal
                                            </option>
                                        </select>
                                    </label>
                                </form>
                            </td>
                            <td class="align-middle"><?= $user->date_debut ?></td>
                            <td class="align-middle"><?= $user->date_fin ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
<script src="<?= $jsUrl . 'users.js' ?>"></script>
</html>