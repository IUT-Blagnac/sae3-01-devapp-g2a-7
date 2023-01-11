<?php
    require_once("../include/checkConnexion.php");
    include("../include/infoPopup.php");

    // Affiche un message de confirmation lorsqu'un compte client a été créé
    if (isset($_GET["messageInfo"])) {
        if ($_GET["messageInfo"] == "compteCree") {
            echo '<script type="text/javascript">show_info_popup("Votre compte a bien été créé, vous pouvez maintenant vous connecter.", "var(--green-blue)")</script>';
        } else if ($_GET["messageInfo"] == "compteModifie") {
            echo '<script type="text/javascript">show_info_popup("Votre compte a bien été modifié, vous pouvez maintenant vous re-connecter.", "var(--green-blue)")</script>';
        }
    }

    // Processus de connexion
    if (isset($_POST["connexionForm"])) {
        $mail = htmlentities($_POST["mail"]);
        $mdp = htmlentities($_POST["mdp"]);
        $messageErreur = "";

        // Vérifie la syntaxe de l'adresse mail
        if (!preg_match("#^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$#", $mail)) {
            $messageErreur = "L'adresse mail donnée est incorrecte.";
        } else {

            $client = get_utilisateur_from_mail("client", $mail);
            $admin = get_utilisateur_from_mail("admin", $mail);

            // Vérifie si l'utilisateur se connecte en tant que client
            if ($client != null) {
                if (!connecter_client($client[0], $mail, $mdp, $client[2])) {
                    $messageErreur = "Les données de connexion sont incorrectes.";
                } else {
                    unset($_SESSION["ADMIN"]);
                    header("Location: index.php");
                }
            }

            // Vérifie si l'utilisateur se connecte en tant qu'administrateur
            else if ($admin != null) {
                if (!connecter_admin($admin[0], $mail, $mdp, $admin[2])) {
                    $messageErreur = "Les données de connexion sont incorrectes.";
                } else {
                    unset($_SESSION["CLIENT"]);
                    header("Location: index.php");
                }
            }

            else {
                $messageErreur = "L'adresse mail donnée ne correspond à aucun compte.";
            }
        }

        // Affiche un popup avec un message d'erreur
        if (!empty($messageErreur)) {
            echo '<script type="text/javascript">show_info_popup("'.$messageErreur.'", "red")</script>';
        }
    }
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../public/css/style.css">
        <link rel="stylesheet" href="../public/css/header.css">
        <link rel="stylesheet" href="../public/css/footer.css">
        <link rel="stylesheet" href="../public/css/connexionStyle.css">
        <title>Connexion</title>
    </head>
    <body>
        <?php include("../include/header.php") ?>
        <section>
            <div>
                <h1>J'ai déjà un compte</h1>
                <form id="form-connexion" method="post">
                    <p>Entrez vos identifiants pour vous connecter :</p>
                    <input type="email" name="mail" placeholder="Adresse e-mail" required>
                    <input type="password" name="mdp" placeholder="Mot de passe" required>
                    <a href="#">Mot de passe oublié ?</a>
                    <input type="submit" name="connexionForm" value="Se connecter">
                </form>
                <a href="creationCompte.php">Pas de compte ? Créez-en un !</a>
            </div>
        </section>
        <?php include("../include/footer.php") ?>
    </body>
</html>
