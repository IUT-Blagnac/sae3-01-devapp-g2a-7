<?php
    include("../include/infoPopup.php");
    require_once("../include/checkConnexion.php");

    $nom = "";
    $prenom = "";
    $mail = "";
    $mdp = "";
    $accepterAnnonce = "0";

    // Vérifie les champs lorsque le formulaire est envoyé
    if (isset($_POST["creationCompteForm"])) {
        $nom = htmlentities($_POST["nom"]);
        $prenom = htmlentities($_POST["prenom"]);
        $mail = htmlentities($_POST["mail"]);
        $mdp = htmlentities($_POST["mdp"]);
        if (isset($_POST["annonces"]) && $_POST["annonces"] == "true") {
            $accepterAnnonce = "1";
        }
        $messageErreur = "";

        // Vérifie la syntaxe de l'adresse mail
        if (!preg_match("/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/", $mail)) {
            $messageErreur = "L'adresse mail donnée est incorrecte.";
        }

        // Vérifie la syntaxe du mot de passe
        else if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/", $mdp)) {
            $messageErreur = "Le mot de passe donné doit faire au moins 8 caractères dont 1 majuscule, 1 chiffre et 1 caractère spécial parmi {@, $, !, %, *, #, ?, &}.";
        }

        // Vérifie si l'adresse mail n'existe pas déjà (chez les clients et les admins)
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
            $sql = "BEGIN Gestion_REVIVE.AjouterClient(:nom, :prenom, :mail, :mdpHash, :accepterAnnonce); END;";
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
                header("Location: connexion.php?messageInfo=compteCree");
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
        <title>Création d'un compte</title>
    </head>
    <body>
        <?php include("../include/header.php") ?>
        <section>
            <div>
                <h1>Nouveau client ?</h1>
                <form id="form-creationCompte" method="post">
                    <p>Créez votre compte :</p>
                    <input type="text" name="nom" placeholder="Nom" value="<?php echo($nom) ?>" maxlength="100" required>
                    <input type="text" name="prenom" placeholder="Prénom" value="<?php echo($prenom) ?>" maxlength="100" required>
                    <input type="email" name="mail" placeholder="Adresse e-mail" value="<?php echo($mail) ?>" maxlength="100" required>
                    <input type="password" name="mdp" placeholder="Mot de passe" maxlength="30" required>
                    <div>
                        <input type="checkbox" id="annonces-checkbox" name="annonces" value="true">
                        J'accepte de recevoir des annonces par mail.
                    </div>
                    <p class="info">En créant votre compte vous acceptez les conditions générales d'utilisation et de vente, et la politique de confidentialité de REVIVE.</p>
                    <input type="submit" name="creationCompteForm" value="Valider">
                </form>
            </div>
        </section>
        <?php include("../include/footer.php") ?>
    </body>
</html>

<script type="text/javascript">
    document.getElementById('annonces-checkbox').checked = "<?php echo ($accepterAnnonce == "1" ? true : false) ?>";
</script>
