<?php
    include("../include/infoPopup.php");
    require_once("../include/checkConnexion.php");

    // Vérifie que la page est sécurisée
    verifier_page();

    // Récupérè des données lorsque l'utilisateur est un client
    if (isset($_SESSION["CLIENT"])) {
        $sql = "SELECT nomClient, prenomClient, mailClient, TO_CHAR(dateCompteClient, 'dd/mm/YYYY HH24:MI:ss'), accepterAnnonceClient
            FROM Client WHERE idClient = :idClient";
        $requete = oci_parse($connect, $sql);
        // Prépare la requête
        oci_bind_by_name($requete, ":idClient", $_SESSION["CLIENT"]["idClient"]);

    // Récupérè des données lorsque l'utilisateur est un administrateur
    } else if (isset($_SESSION["ADMIN"])) {
        $sql = "SELECT nomAdmin, prenomAdmin, mailAdmin, TO_CHAR(dateCompteAdmin, 'dd/mm/YYYY HH24:MI:ss')
            FROM Administrateur WHERE idAdmin = :idAdmin";
        $requete = oci_parse($connect, $sql);
        // Prépare la requête
        oci_bind_by_name($requete, ":idAdmin", $_SESSION["ADMIN"]["idAdmin"]);
    }

    // Exécute la requête et complète les champs
    $messageErreur = "";
    $result = oci_execute($requete);
    if (!$result) {
        $messageErreur = "Une erreur est survenue avec la base de données.";
    } else {
        $utilisateur = oci_fetch_array($requete);
        $nom = html_entity_decode($utilisateur[0]);
        $prenom = html_entity_decode($utilisateur[1]);
        $mail = html_entity_decode($utilisateur[2]);
        $date = html_entity_decode($utilisateur[3]);
        $accepterAnnonce = $utilisateur[4] ?? null;
    }

    // Vérifie les champs lorsque le formulaire est envoyé
    if (isset($_POST["creationCompteForm"])) {

        // Vérifie la syntaxe de l'adresse mail
        if (!preg_match("#^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$#", $mail)) {
            $messageErreur = "L'adresse mail donnée est incorrecte.";
        }

        // Vérifie si l'adresse mail n'existante pas déjà (chez les clients et les admins)
        $sql = "SELECT COUNT(*) FROM (
            SELECT mailClient FROM Client WHERE mailClient = :mail
            UNION
            SELECT mailAdmin FROM Administrateur WHERE mailAdmin = :mail)";
        $requete = oci_parse($connect, $sql);
        // Prépare la requête
        oci_bind_by_name($requete, ":mail", $mail);
        // Exécute la requête
        $result = oci_execute($requete);
        if (!$result) {
            $messageErreur = "Une erreur est survenue avec la base de données.";
        } else if (oci_fetch_array($requete)[0] != "0") {
            $messageErreur = "L'adresse mail donnée est déjà utilisée.";
        }

        // Affiche un popup avec un message d'erreur
        if (!empty($messageErreur)) {
            echo '<script type="text/javascript">show_info_popup("'.$messageErreur.'", "red")</script>';
        }

        // Créé un compte et ajoute les données dans la BD (cas nominal)
        else {
            // Hache le mot de passe
            $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);
            $sql = "BEGIN AjouterClient(:nom, :prenom, :mail, :mdpHash, :accepterAnnonce); END;";
            // Prépare la requête
            $requete = oci_parse($connect, $sql);
            oci_bind_by_name($requete, ":nom", $nom);
            oci_bind_by_name($requete, ":prenom", $prenom);
            oci_bind_by_name($requete, ":mail", $mail);
            oci_bind_by_name($requete, ":mdpHash", $mdpHash);
            oci_bind_by_name($requete, ":accepterAnnonce", $accepterAnnonce);
            // Exécute la requête
         	$result = oci_execute($requete);
        	if (!$result) {
        		//$e = oci_error($requete);
        		echo '<script type="text/javascript">show_info_popup("Une erreur est survenue lors de l\'ajout des données.", "red")</script>';
        	} else {
                // Redirection vers la page de connexion (avec message de confirmation)
                header("Location: Connexion.php?nouveauCompte=true");
            }
        }
    }

    // Déconnecte l'utilisateur
    if (isset($_POST["deconnexionForm"])) {
        unset($_SESSION["CLIENT"]);
        unset($_SESSION["ADMIN"]);
        header("Location: index.php");
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
        <link rel="stylesheet" href="../public/css/connexionStyle.css">
        <title>Voir le compte</title>
    </head>
    <body>
        <?php include("../include/header.php") ?>
        <section>
            <div>
                <h1>Données du compte</h1>
                <div>
                    <label>Utilisateur : <b><?php echo $prenom." ".$nom ?></b></label>
                    <label>Adresse mail : <b><?php echo $mail ?></b></label>
                    <label>Date de création du compte : <b><?php echo $date ?></b></label>
                    <label id="info-label" class="info"></label>
                </div>
                <a href="modifierCompte.php">Modifier les données</a>
                <form id="form-deconnexion" method="post">
                    <input id="deconnexion-input" type="submit" name="deconnexionForm" value="Se déconnecter">
                </form>
            </div>
        </section>
        <?php include("../include/footer.php") ?>
    </body>
</html>

<script type="text/javascript">
    if ("<?php echo $accepterAnnonce ?>" == "1") {
        document.getElementById('info-label').textContent = "Vous avez accepté de recevoir des annonces par mail.";
    } else if ("<?php echo $accepterAnnonce ?>" == "0") {
        document.getElementById('info-label').textContent = "Vous n'avez pas accepté de recevoir des annonces par mail.";
    } else {
        document.getElementById('info-label').textContent = "COMPTE ADMINISTRATEUR";
    }
</script>
