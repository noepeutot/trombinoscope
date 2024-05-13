<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="<?= css_url('login') ?>" rel='stylesheet'>
    <link href="<?= css_url('bootstrap.min.v5.3.3') ?>" rel="stylesheet">
    <script src="<?= js_url('popper.v2.11.8') ?>"></script>
    <script src="<?= js_url('bootstrap.v5.3.3') ?>"></script>
    <script src="<?= js_url('jquery.v3.7.1') ?>"></script>
</head>
<style>
    body {
        background: #88B6F2 url("<?= img_url('wave_login.svg') ?>") no-repeat fixed center center;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
</style>
<body>
<header class="d-flex justify-content-between align-items-center my-5">
    <a type="button" class="btn btn-light d-flex align-items-center mx-5 px-4" href="javascript:history.back()">
        <img class="me-2" src="<?= img_url('back_arrow.svg') ?>" alt="retour" height="20px">
        <span>Retour</span>
    </a>
</header>
<main>
    <form method="post">
        <div>
            <label id="login">Login</label>
            <input type="email" aria-label="login">
        </div>
        <div>
            <label id="password">Mot de passe</label>
            <input type="password" aria-label="password">
        </div>
        <button>
            Se connecter
        </button>
    </form>
</main>
</body>
</html>