<?php if (isset($personneConnectee)) { ?>
    <div class="dropdown-center">
        <a role="button" class="btn btn-light d-flex align-items-center mx-5 px-4 dropdown-toggle"
           data-bs-toggle="dropdown" aria-expanded="false">
            <img class="me-2" id="accountImage"
                 src="<?= img_url('profile/' . $personneConnectee->id_personne . '.jpg') ?>"
                 alt="photographie">
            <span><?= $personneConnectee->nom . ' ' . $personneConnectee->prenom ?></span>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item d-flex align-items-center justify-content-start"
                   href="<?= base_url() ?>">
                    <img class="mx-1 img-nav" src="<?= img_url('navbar/home.svg') ?>" alt="accueil">
                    <span class="fw-semibold">Accueil</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center justify-content-start"
                   href="<?= base_url('profile/edit') ?>">
                    <img class="mx-1 img-nav" src="<?= img_url('navbar/profile.svg') ?>" alt="mon profile">
                    <span class="fw-semibold"> Mon Profile</span>
                </a>
            </li>
            <?php if ($personneConnectee->role === 'admin') { ?>
                <li>
                    <a class="dropdown-item d-flex align-items-center justify-content-start"
                       href="#">
                        <img class="mx-1 img-nav" src="<?= img_url('navbar/back-office.svg') ?>" alt="back-office">
                        <span class="fw-semibold">Back-Office</span>
                    </a>
                </li>
            <?php } ?>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <a class="dropdown-item btn btn-dark d-flex align-items-center justify-content-start"
                   href="<?= base_url('logout') ?>">
                    <img class="mx-1 img-nav" src="<?= img_url('navbar/logout_red.svg') ?>" alt="deconnexion">
                    <span class="text-danger fw-semibold">Deconnexion</span>
                </a>
            </li>
        </ul>
    </div>
<?php } else { ?>
    <a type="button" class="btn btn-light d-flex align-items-center mx-5 px-4"
       href="<?= base_url('login') ?>">
        <img id="accountImage" class="me-2" src="<?= img_url('account.svg') ?>" alt="compte" width="30px"
             height="30px">
        <span>Se connecter</span>
    </a>
<?php } ?>