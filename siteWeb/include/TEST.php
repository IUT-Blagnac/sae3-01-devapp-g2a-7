<?php
    include("../include/checkConnexion.php");

    function afficher_categories($idCategorieMere=null, $niveau=1) {
        global $connect;
        if ($idCategorieMere == null) {
            $sql = "SELECT idCategorie, nomCategorie FROM Categorie
                WHERE idCategorieMere IS NULL";
            $requete = oci_parse($connect, $sql);
        } else {
            $sql = "SELECT idCategorie, nomCategorie FROM Categorie
                WHERE idCategorieMere = :idCategorieMere";
            $requete = oci_parse($connect, $sql);
            oci_bind_by_name($requete, ":idCategorieMere", $idCategorieMere);
        }
        $result = oci_execute($requete);
        if (!$result) {
            echo "<script>throw 'Une erreur est survenue avec la base de données.'</script>";
            return null;
        }
        $categories = array();
        oci_fetch_all($requete, $categories);
        $idUl = $categories["IDCATEGORIE"][0] ?? "x";
        echo "<ul id='ul".$idUl."'>";
        for ($i=0; $i<count($categories["IDCATEGORIE"]); $i++) {
            echo "<li id='li".$categories["IDCATEGORIE"][$i]."'>".
                $categories["NOMCATEGORIE"][$i];
            afficher_categories($categories["IDCATEGORIE"][$i], $niveau+1);
            echo "</li>";
        }
        echo "</ul>";
        echo "<style>
            #ul".$idUl." {
                display:".($idCategorieMere == null ? "block" : "none").";
                margin-left:".(($niveau-1)*100)."px;
            }

            #li".$idCategorieMere.":hover #ul".$idUl." {
                display:block;
            }
        </style>";
    }
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../public/css/style.css">
        <title>Page de test</title>
    </head>
    <body>
        <?php afficher_categories() ?>
    </body>
</html>

<style media="screen">
    ul {
        border:1px solid blue;
        padding:15px;
    }

    li {
        border:1px solid red;
    }
</style>
