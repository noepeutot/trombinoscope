<?php
$base_url = base_url();
$img_url = img_url('');
if (isset($activePage)): ?>
    <nav class="navbar navbar-expand-sm bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar"
                    aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbar">
                <div class="navbar-nav mx-auto">
                    <a class="nav-link d-flex mx-3 <?= $activePage === 'dashboard' ? 'active' : '' ?>"
                       href="<?= $base_url . 'backoffice/dashboard' ?>">
                        <img class="my-auto mx-2"
                             src="<?= $activePage === 'dashboard' ?
                                 $img_url . 'backoffice/header/home-selected.svg' :
                                 $img_url . 'backoffice/header/home.svg' ?>"
                             alt="accueil"
                             width="20px">
                        Dashboard
                    </a>
                    <a class="nav-link d-flex mx-3 <?= $activePage === 'users' ? 'active' : '' ?>"
                       href="<?= $base_url . 'backoffice/users' ?>">
                        <img class="my-auto mx-2"
                             src="<?= $activePage === 'users' ?
                                 $img_url . 'backoffice/header/users-selected.svg' :
                                 $img_url . 'backoffice/header/users.svg' ?>"
                             alt="utilisateurs"
                             width="20px">
                        Utilisateurs
                    </a>
                    <a class="nav-link d-flex mx-3 <?= $activePage === 'moderation' ? 'active' : '' ?>"
                       href="<?= $base_url . 'backoffice/moderation' ?>">
                        <img class="my-auto mx-2"
                             src="<?= $activePage === 'moderation' ?
                                 $img_url . 'backoffice/header/moderation-selected.svg' :
                                 $img_url . 'backoffice/header/moderation.svg' ?>"
                             alt="accueil"
                             width="20px">
                        Modération
                    </a>
                </div>
                <a class="btn btn-light" href="<?= $base_url ?>">
                    <img src="<?= $img_url . 'backoffice/header/out.svg' ?>" alt="sortir" width="20px">
                    Sortir
                </a>
            </div>
        </div>
    </nav>
<?php endif; ?>