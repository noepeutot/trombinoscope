<?php $cssUrl = css_url('');
$jsUrl = js_url('');
$imgUrl = img_url('');
?>
<!DOCTYPE html>
<html lang="fr" class="h-100 w-100 m-0">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="<?= $cssUrl . 'login.css' ?>" rel='stylesheet'>
    <link href="<?= $cssUrl . 'bootstrap.min.v5.3.3.css' ?>" rel="stylesheet">
    <script src="<?= $jsUrl . 'popper.v2.11.8.js' ?>"></script>
    <script src="<?= $jsUrl . 'bootstrap.v5.3.3.js' ?>"></script>
    <script src="<?= $jsUrl . 'jquery.v3.7.1.js' ?>"></script>
</head>
<style>
    body {
        background: #88B6F2 url("<?= $imgUrl . 'wave_login.svg' ?>") no-repeat fixed center center;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
</style>
<body class="mh-100 h-100 m-0 p-5">
<header class="d-flex justify-content-between align-items-center">
    <a type="button" class="btn btn-light d-flex align-items-center px-4" href="javascript:history.back()">
        <img class="me-2" src="<?= $imgUrl . 'back_arrow.svg' ?>" alt="retour" width="20px">
        <span>Retour</span>
    </a>
</header>
<main class="h-100 d-flex justify-content-center align-items-center">
    <form method="post" class="container w-35">
        <h2 class="text-light">
            Connexion
        </h2>
        <div class="my-4">
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div><?= $error ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>
        </div>
        <div class="my-4">
            <label id="login" class="form-label text-light">
                Login
            </label>
            <input type="text" class="form-control <?php echo isset($error) ? 'is-invalid' : '' ?>"
                   aria-describedby="loginFeedback" aria-label="login" name="login"
                   placeholder="login" required>
        </div>
        <div class="my-4">
            <label id="password" class="form-label text-light">
                Mot de passe
            </label>
            <div class="input-group">
                <input id="input-pw" type="password"
                       class="form-control <?php echo isset($error) ? 'is-invalid' : '' ?>" aria-label="password"
                       name="password"
                       placeholder="**********" aria-describedby="display-pw" required>
                <div id="display-pw" class="input-group-text">
                    <img id="show" src="<?= $imgUrl . 'login/show.svg' ?>" alt="voir">
                    <img id="hide" src="<?= $imgUrl . 'login/hide.svg' ?>" alt="cacher">
                </div>
            </div>
        </div>
        <div class="my-4">
            <button type="submit" class="btn btn-light w-100">
                Se connecter
            </button>
        </div>
    </form>
</main>
<script src="<?= $jsUrl . 'login.js' ?>"></script>
</body>
</html>