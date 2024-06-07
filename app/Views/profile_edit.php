<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trombinoscope</title>
    <link href="<?= css_url('profile_edit') ?>" rel='stylesheet'>
    <link href="<?= css_url('bootstrap.min.v5.3.3') ?>" rel="stylesheet">
    <script src="<?= js_url('popper.v2.11.8') ?>"></script>
    <script src="<?= js_url('bootstrap.v5.3.3') ?>"></script>
    <script src="<?= js_url('jquery.v3.7.1') ?>"></script>

    <link href="<?= css_url('bootstrap-select.min.v1.13.18') ?>" rel="stylesheet">
    <script src="<?= js_url('bootstrap-select.v1.14.0') ?>"></script>
</head>
<style>
    body {
        background: #88B6F2 url("<?= img_url('wave_edit.svg') ?>") no-repeat fixed center center;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
</style>
<body>
<?= $this->include('header') ?>
<?php $base_url = base_url('profile');
$img_url = img_url('');
if (isset($personne)) { ?>
    <main class="justify-content-center w-100 pt-5 row">
        <?= form_open_multipart($base_url . '/edit', "class='row w-75' id='informations'") ?>
        <section class="col-4 row">
            <div class="col-md-12 d-flex align-items-center flex-column p-5">
                <label class="col-md-12 form-label text-light fs-6 text">Photo
                    <?php if (isset($photoModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                            <img src="<?= $img_url . 'waiting.svg' ?>" alt="en attente" width="10">
                        </span>
                    <?php } ?>
                </label>

                <?php if (isset($photoModif)): ?>
                    <img class="profile rounded rounded-3 mw-"
                         src="<?= $photoModif->apres ?>"
                         alt="photo de profile">
                <?php else: ?>
                    <img class="profile rounded rounded-3"
                         src="<?= $img_url . 'profile/valide/' . $personne->id_personne . '.jpg' ?>"
                         alt="photo de profile">
                <?php endif; ?>
                <div class="col-md-12 d-grid gap-2 mt-2">
                    <?php if (isset($photoModif)): ?>
                        <a class="btn btn-light btn-sm d-flex align-items-center justify-content-center flex-wrap"
                           href="<?= $base_url . '/edit/delete' ?>">
                            <img class="me-2" src="<?= $img_url . 'profile/delete.svg' ?>" width="15px" alt="supprimer">
                            Supprimer
                        </a>
                    <?php endif; ?>
                    <label class="btn btn-primary btn-sm" for="photo_profile">Modifier la photo</label>
                    <input class="btn btn-primary " type="file" id="photo_profile" name="photo_profile"
                           form="informations" accept=".png, .jpg, .jpeg" style="display: none"
                           onchange="form.submit()">
                </div>
            </div>
        </section>
        <section class="col-8 row row-gap-3">
            <div class="col-md-12 row d-flex flex-row">
                <h3 class="col-md-auto text-light">
                    Edition du profile
                </h3>
                <a class="col-md-auto link-offset-2 text-light d-flex justify-content-start align-self-center"
                   href="<?= $base_url . '/' . $personne->id_personne ?>">
                    Prévisualisation
                    <img class="mx-2" src="<?= $img_url . 'arrow_link.svg' ?>" alt="redirection">
                </a>
            </div>
            <div class="col-md-6">
                <label for="inputNom" class="form-label text-light">Nom
                    <?php if (isset($nomModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                            <img src="<?= $img_url . 'waiting.svg' ?>" alt="en attente" width="10">
                        </span>
                    <?php } ?>
                </label>
                <input type="text" class="form-control" id="inputNom" placeholder="Nom" name="nom"
                       value="<?= $nomModif->apres ?? $personne->nom ?>">
            </div>
            <div class="col-md-6">
                <label for="inputPrenom" class="form-label text-light">Prénom
                    <?php if (isset($prenomModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                            <img src="<?= $img_url . 'waiting.svg' ?>" alt="en attente" width="10">
                        </span>
                    <?php } ?>
                </label>
                <input type="text" class="form-control" id="inputPrenom" placeholder="Prénom" name="prenom"
                       value="<?= $prenomModif->apres ?? $personne->prenom ?>">
            </div>
            <div class="col-md-6">
                <label for="inputEmail" class="form-label text-light">Email
                    <?php if (isset($mailModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                            <img src="<?= $img_url . 'waiting.svg' ?>" alt="en attente" width="10">
                        </span>
                    <?php } ?>
                </label>
                <input type="email" class="form-control" id="inputEmail" placeholder="prenom.nom@g2elab.grenoble-inp.fr"
                       name="email"
                       value="<?= $mailModif->apres ?? ($mailPersonne->libelle ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="inputTel" class="form-label text-light">Téléphone
                    <?php if (isset($telephoneModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                            <img src="<?= $img_url . 'waiting.svg' ?>" alt="en attente" width="10">
                        </span>
                    <?php } ?>
                </label>
                <input type="tel" class="form-control" id="inputTel" placeholder="0123456789" name="telephone"
                       pattern="[0-9]{10}" value="<?= $telephoneModif->apres ?? ($personne->telephone ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label for="inputBureau" class="form-label text-light">Bureau
                    <?php if (isset($bureauModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                            <img src="<?= $img_url . 'waiting.svg' ?>" alt="en attente" width="10">
                        </span>
                    <?php } ?>
                </label>
                <select class="selectpicker w-100" id="inputBureau" data-live-search="true"
                        data-style="btn-light" title="Sélectionner…" name="bureau" form="informations">
                    <?php if (!empty($allBureaux)) {
                        foreach ($allBureaux as $bureau) { ?>
                            <option <?php if (isset($bureauModif)) {
                                echo $bureauModif === $bureau->id_bureau ? 'selected' : '';
                            } else if (!empty($bureauPersonne)) {
                                echo $bureau->id_bureau === $bureauPersonne->id_bureau ? 'selected' : '';
                            } ?> value="<?= $bureau->id_bureau ?>">
                                <?= $bureau->numero ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="inputCategorie" class="form-label text-light">Catégorie
                    <?php if (isset($statutModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                            <img src="<?= $img_url . 'waiting.svg' ?>" alt="en attente" width="10">
                        </span>
                    <?php } ?>
                </label>
                <select class="selectpicker w-100" id="inputCategorie" data-live-search="true"
                        data-style="btn-light" title="Sélectionner…" name="statut" form="informations">
                    <?php if (!empty($allStatuts)) {
                        foreach ($allStatuts as $statut) { ?>
                            <option <?php if (isset($statutModif)) {
                                echo $statutModif === $statut->id_statut ? 'selected' : '';
                            } else if (!empty($statutPersonne)) {
                                echo $statut->id_statut === $statutPersonne->id_statut ? 'selected' : '';
                            } ?> value="<?= $statut->id_statut ?>">
                                <?= $statut->nom ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="inputEquipe" class="form-label text-light">Equipe⋅s
                    <?php if (isset($equipesModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                            <img src="<?= $img_url . 'waiting.svg' ?>" alt="en attente" width="10">
                        </span>
                    <?php } ?>
                </label>
                <select class="selectpicker w-100" id="inputEquipe" data-live-search="true"
                        data-style="btn-light" title="Sélectionner…" name="equipe[]" form="informations" multiple>
                    <?php if (!empty($allEquipes)) {
                        foreach ($allEquipes as $equipe) { ?>
                            <option <?php if (isset($equipesModif)) {
                                echo in_array($equipe->id_equipe, $equipesModif) ? 'selected' : '';
                            } else if (isset($equipePersonne)) {
                                echo in_array($equipe->id_equipe,
                                    array_column($equipePersonne, 'id_equipe')) ? 'selected' : '';
                            } ?> value="<?= $equipe->id_equipe ?>">
                                <?= $equipe->nom_court ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="inputEmployeur" class="form-label text-light w-100">Employeur⋅s
                    <?php if (isset($employeursModif)) { ?>
                        <span class="badge rounded-pill text-bg-warning">En attente
                            <img src="<?= $img_url . 'waiting.svg' ?>" alt="en attente" width="10">
                        </span>
                    <?php } ?>
                </label>
                <select class="selectpicker w-100" id="inputEmployeur" data-live-search="true"
                        data-style="btn-light" title="Sélectionner…" name="employeur[]" form="informations" multiple>
                    <?php if (!empty($allEmployeurs)) {
                        foreach ($allEmployeurs as $employeur) { ?>
                            <option <?php if (isset($employeursModif)) {
                                $on_array = in_array($employeur->id_employeur, $employeursModif);
                                echo $on_array ? 'selected' : ''; ?>
                            <?php } else if (isset($employeursPersonne)) {
                                echo in_array($employeur->id_employeur,
                                    array_column($employeursPersonne, 'id_employeur')) ? 'selected' : '';
                            } ?> value="<?= $employeur->id_employeur ?>">
                                <?= $employeur->nom ?>
                            </option>
                        <?php }
                    } ?>
                </select>
            </div>
            <?php if (isset($sejourPersonne) && isset($statutPersonne) && ($statutPersonne->nom === "Stagiaire" || $statutPersonne->nom === "Doctorant")) { ?>
                <div class="col-12">
                    <label for="inputActivite" class="form-label text-light w-100">Activités
                        <?php if (isset($activiteModif)) { ?>
                            <span class="badge rounded-pill text-bg-warning">En attente
                            <img src="<?= $img_url . 'waiting.svg' ?>" alt="en attente" width="10">
                        </span>
                        <?php } ?></label>
                    <textarea class="form-control" id="inputActivite"
                              placeholder="Vos activités, sujet de stage, sujet de thèse…" rows="2"
                              form="informations"
                              name="activite"><?= $activiteModif->apres ?? $sejourPersonne->sujet ?></textarea>
                </div>
            <?php } ?>
            <div class="col-md-12">
                <label for="inputCommentaire" class="form-label text-light w-100">Commentaire</label>
                <input type="text" class="form-control" id="inputCommentaire"
                       placeholder="Commentaire sur le⋅s changement⋅s"
                       name="commentaire">
            </div>
            <div class="col-md-6 d-grid">
                <a href="<?= $base_url . '/edit' ?>"
                   class="btn btn-danger link-underline link-underline-opacity-0" type="button">Annuler</a>
            </div>
            <div class="col-md-6 d-grid">
                <button type="submit" class="btn btn-success link-underline link-underline-opacity-0">Sauvegarder
                </button>
            </div>
        </section>
        </form>
    </main>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <?php if (isset($errors)):
            foreach ($errors as $error): ?>
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header text-bg-danger bg-opacity-75">
                        <img src="<?= $img_url . 'warning.svg' ?>" class="rounded me-2" alt="attention"
                             width="25px">
                        <strong class="me-auto"><?= key($error) ?></strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                                aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        <?php foreach ($error as $text) {
                            echo $text;
                        } ?>
                    </div>
                </div>
            <?php endforeach;
        endif; ?>
    </div>
<?php } ?>
</body>
<script src="<?= js_url('profile_edit') ?>"></script>
</html>