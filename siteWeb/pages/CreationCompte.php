<!DOCTYPE html>
<html lang="fr" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../public/css/style.css">
        <link rel="stylesheet" href="../public/css/header.css">
        <link rel="stylesheet" href="../public/css/connexionStyle.css">
        <title>Création d'un compte</title>
    </head>
    <body>
        <?php include("../include/header.php"); ?>
        <section>
            <div>
                <h1>Nouveau client ?</h1>
                <form id="form-creationCompte" action="index.html" method="post">
                    <p>Créez votre compte :</p>
                    <input type="text" name="nom" placeholder="Nom">
                    <input type="text" name="prenom" placeholder="Prénom">
                    <input type="email" name="mail" placeholder="Adresse e-mail">
                    <input type="password" name="mdp" placeholder="Mot de passe">
                    <div>
                        <input type="checkbox" name="annonces">
                        J'accepte de recevoir des annonces par mail.
                    </div>
                    <p id="info">En créant votre compte vous acceptez les conditions générales d'utilisation et de vente, et la politique de confidentialité de REVIVE.</p>
                    <input type="submit" name="creationCompteForm" value="Valider">
                </form>
            </div>
        </section>
    </body>
</html>
