<?php if (isset($nom) && isset($prenom) && isset($attribut)): ?>
    <p>
        Bonjour <?= $prenom . ' ' . $nom ?>,
    </p>
    <p>
        Votre demande de modification du trombinoscope sur votre "<?= $attribut ?>" a été refusé.
        Veuillez vérifier à nouveau les informations et contacter les personnes compétentes.
    </p>
    <p>
        Bien cordialement.
    </p>
    <p>
        (PS : Ceci est un message automatique. Merci de ne pas y répondre)
    </p>
<?php endif; ?>