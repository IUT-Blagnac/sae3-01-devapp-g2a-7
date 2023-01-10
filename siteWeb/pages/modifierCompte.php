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
    if (isset($_POST["modifierCompteForm"])) {
        $nom = htmlentities($_POST["nom"]);
        $prenom = htmlentities($_POST["prenom"]);
        $mail = htmlentities($_POST["mail"]);
        $mdp = htmlentities($_POST["mdp"]);
        $ancienMdp = htmlentities($_POST["ancienMdp"]);
        if (isset($_POST["annonces"]) && $_POST["annonces"] == "true") {
            $accepterAnnonce = "1";
        }
        $messageErreur = "";

        // Vérifie la syntaxe de l'adresse mail
        if (!preg_match("/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/", $mail)) {
            $messageErreur = "L'adresse mail donnée est incorrecte.";
        }

        // Vérifie la syntaxe du nouveau mot de passe
        else if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/", $mdp)) {
            $messageErreur = "Le nouveau mot de passe donné doit faire au moins 8 caractères dont 1 majuscule, 1 chiffre et 1 caractère spécial parmi {@, $, !, %, *, #, ?, &}.";
        }

        // Vérifie si l'adresse mail n'existe pas déjà (chez les clients et les admins)
        $sql = "SELECT COUNT(*) FROM (
            SELECT mailClient FROM Client WHERE mailClient = :mail AND mailClient <> :ancienMail
            UNION
            SELECT mailAdmin FROM Administrateur WHERE mailAdmin = :mail AND mailAdmin <> :ancienMail)";
        $requete = oci_parse($connect, $sql);
        // Prépare la requête
        oci_bind_by_name($requete, ":mail", $mail);
        if (isset($_SESSION["CLIENT"])) {
            oci_bind_by_name($requete, ":ancienMail", $_SESSION["CLIENT"]["mailClient"]);
        } else if (isset($_SESSION["ADMIN"])) {
            oci_bind_by_name($requete, ":ancienMail", $_SESSION["ADMIN"]["mailAdmin"]);
        }
        // Exécute la requête
        $result = oci_execute($requete);
        if (!$result) {
            $messageErreur = "Une erreur est survenue avec la base de données.";
        } else if (oci_fetch_array($requete)[0] != "0") {
            $messageErreur = "L'adresse mail donnée est déjà utilisée.";
        }

        // Vérifie si l'ancien mot de passe est correct
        if (isset($_SESSION["CLIENT"])) {
            if (!connecter_client($_SESSION["CLIENT"]["idClient"], $_SESSION["CLIENT"]["mailClient"], $ancienMdp, $_SESSION["CLIENT"]["mdpClient"])) {
                $messageErreur = "L'ancien mot de passe donné est incorrect.";
            }
        } else if (isset($_SESSION["ADMIN"])) {
            if (!connecter_admin($_SESSION["ADMIN"]["idAdmin"], $_SESSION["ADMIN"]["mailAdmin"], $ancienMdp, $_SESSION["ADMIN"]["mdpAdmin"])) {
                $messageErreur = "L'ancien mot de passe donné est incorrect.";
            }
        }

        // Affiche un popup avec un message d'erreur
        if (!empty($messageErreur)) {
            echo '<script type="text/javascript">show_info_popup("'.$messageErreur.'", "red")</script>';
        }

        // Modifie les données du compte dans la BD (cas nominal)
        else {
            // Hache le mot de passe
            $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);
            if (isset($_SESSION["CLIENT"])) {
                $sql = "UPDATE Client SET nomClient = :nom, prenomClient = :prenom, mailClient = :mail, mdpClient = :mdpHash, accepterAnnonceClient = :accepterAnnonce WHERE idClient = :idClient";
                $requete = oci_parse($connect, $sql);
                oci_bind_by_name($requete, ":idClient", $_SESSION["CLIENT"]["idClient"]);
                oci_bind_by_name($requete, ":accepterAnnonce", $accepterAnnonce);
            } else if (isset($_SESSION["ADMIN"])) {
                $sql = "UPDATE Administrateur SET nomAdmin = :nom, prenomAdmin = :prenom, mailAdmin = :mail, mdpAdmin = :mdpHash WHERE idAdmin = :idAdmin";
                $requete = oci_parse($connect, $sql);
                oci_bind_by_name($requete, ":idAdmin", $_SESSION["ADMIN"]["idAdmin"]);
            }
            oci_bind_by_name($requete, ":nom", $nom);
            oci_bind_by_name($requete, ":prenom", $prenom);
            oci_bind_by_name($requete, ":mail", $mail);
            oci_bind_by_name($requete, ":mdpHash", $mdpHash);
            // Exécute la requête
         	$result = oci_execute($requete);
        	if (!$result) {
        		echo '<script type="text/javascript">show_info_popup("Une erreur est survenue lors de la modification des données.", "red")</script>';
        	} else {
                // Redirection vers la page de connexion (avec message de confirmation)
                header("Location: connexion.php?messageInfo=compteModifie");
            }
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
        <title>Modification d'un compte</title>
    </head>
    <body>
        <?php include("../include/header.php") ?>
        <section>
            <div>
                <h1>Modification du compte</h1>
                <form id="form-modifierCompte" method="post">
                    <p>Modifiez les données de votre compte :</p>
                    <input type="text" name="nom" placeholder="Nom" value="<?php echo($nom) ?>" maxlength="100" required>
                    <input type="text" name="prenom" placeholder="Prénom" value="<?php echo($prenom) ?>" maxlength="100" required>
                    <input type="email" name="mail" placeholder="Adresse e-mail" value="<?php echo($mail) ?>" maxlength="100" required>
                    <input type="password" name="ancienMdp" placeholder="Ancien mot de passe" maxlength="30" required>
                    <input type="password" name="mdp" placeholder="Nouveau mot de passe" maxlength="30" required>
                    <?php
                        if ($accepterAnnonce != null) { ?>
                            <div>
                                <input type="checkbox" id="annonces-checkbox" name="annonces" value="true">
                                J'accepte de recevoir des annonces par mail.
                            </div> <?php
                        } ?>
                    <input type="submit" name="modifierCompteForm" value="Valider">
                </form>
            </div>
        </section>
        <?php include("../include/footer.php") ?>
    </body>
</html>

<script type="text/javascript">
    document.getElementById('annonces-checkbox').checked = "<?php echo ($accepterAnnonce == "1" ? true : false) ?>";
</script>
