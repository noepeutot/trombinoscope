<?php $uristring = uri_string();
$baseUrl = base_url();?>

<header class="d-flex justify-content-between align-items-center mt-5">
    <?php if ($uristring === '' || $uristring === 'search') { ?>
        <a href="<?= $baseUrl ?>" class="link-underline link-underline-opacity-0">
            <h1 class="text-white mx-5">Trombinoscope</h1>
        </a>
    <?php } else { ?>
        <a id="back" type="button" class="btn btn-light d-flex align-items-center mx-5 px-4"
           href="<?= $baseUrl ?>">
            <img class="me-2" src="<?= img_url('back_arrow.svg') ?>" alt="retour">
            <span>Retour</span>
        </a>
    <?php } ?>

    <?= $this->include('frontoffice/navbar') ?>
</header>