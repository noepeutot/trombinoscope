<?php if (isset($nom) && isset($prenom) && isset($attribut)): ?>
    <p>
        Bonjour <?= $prenom . ' ' . $nom ?>,
    </p>
    <p>
        Votre demande de modification du trombinoscope sur votre "<?= $attribut ?>" a bien été validée.
        Les informations seront mises à jour prochainement.
    </p>
    <p>
        Bien cordialement.
    </p>
    <p>
        (PS : Ceci est un message automatique. Merci de ne pas y répondre)
    </p>
<?php endif; ?>