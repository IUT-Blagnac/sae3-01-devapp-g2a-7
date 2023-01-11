<?php
    require_once("connect.inc.php");
    session_start();


    function get_utilisateur_from_mail($type, $mail) {
        global $connect;
        $utilisateur = null;
        if ($type == "client") {
            $sql = "SELECT idClient, mailClient, mdpClient FROM Client WHERE mailClient = :mail";
        } else if ($type == "admin") {
            $sql = "SELECT idAdmin, mailAdmin, mdpAdmin FROM Administrateur WHERE mailAdmin = :mail";
        }
        $requete = oci_parse($connect, $sql);
        // Prépare la requête
        oci_bind_by_name($requete, ":mail", $mail);
        // Exécute la requête
        $result = oci_execute($requete);
        if (!$result) {
            echo "<script>throw 'Une erreur est survenue avec la base de données.'</script>";
        } else {
            $utilisateur = oci_fetch_array($requete);
            if (!$utilisateur) {
                $utilisateur = null;
            }
        }
        return $utilisateur;
    }


    function connecter_client($idClient, $mail, $mdp, $mdpHash) {
        if (password_verify($mdp, $mdpHash)) {
            $_SESSION["CLIENT"] = array("idClient" => $idClient,
                                        "mailClient" => $mail,
                                        "mdpClient" => $mdpHash);
            return true;
        }
        return false;
    }


    function connecter_admin($idAdmin, $mail, $mdp, $mdpHash) {
        if (password_verify($mdp, $mdpHash)) {
        $_SESSION["ADMIN"] = array("idAdmin" => $idAdmin,
                                   "mailAdmin" => $mail,
                                   "mdpAdmin" => $mdpHash);
           return true;
       }
       return false;
    }


    function verifier_page() {
        $connecte = false;

        // Session client
        if (isset($_SESSION["CLIENT"])) {
            $client = get_utilisateur_from_mail("client", $_SESSION["CLIENT"]["mailClient"]);
            if ($client != null && $_SESSION["CLIENT"]["mdpClient"] == $client[2]) {
                $connecte = true;
            }

        // Session administrateur
        } else if (isset($_SESSION["ADMIN"])) {
            $admin = get_utilisateur_from_mail("admin", $_SESSION["ADMIN"]["mailAdmin"]);
            if ($admin != null && $_SESSION["ADMIN"]["mdpAdmin"] == $admin[2]) {
                $connecte = true;
            }
        }

        // Redirige l'utilisateur s'il n'est pas connecté (ou qu'il y a eu une erreur)
        if (!$connecte) {
            header("Location: connexion.php");
        }
    }
?>
