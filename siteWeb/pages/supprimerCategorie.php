<?php
    require_once("../include/checkConnexion.php");
    include("../include/infoPopup.php");

    verifier_page();

    if (isset($_POST["supprimerCategorieForm"])) {
        $idCategorie = $_POST["idCategorie"];
        $sql = "DELETE FROM Categorie WHERE idCategorie = :idCategorie";
        $requete = oci_parse($connect, $sql);
        oci_bind_by_name($requete, ":idCategorie", $idCategorie);
        $result = oci_execute($requete);
        if ($result) {
            echo '<script type="text/javascript">show_info_popup("La catégorie a bien été supprimée.", "var(--green-blue)")</script>';
        } else {
            echo '<script type="text/javascript">show_info_popup("Erreur avec la base de données.", "red")</script>';
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
        <title>Supprimer catégorie</title>
    </head>
    <body>
        <?php include("../include/header.php") ?>
        <section>
            <div>
                <h1>Supprimer une catégorie</h1>
                <form id="form-ajouterCategorie" method="post">
                    <p>Choisissez la catégorie à supprimer :</p>
                    <select name="idCategorie" required>
                        <option value="" disabled selected>Catégorie</option>
                        <?php
                            $sql = "SELECT * FROM Categorie C
                                    WHERE NOT EXISTS (
                                        SELECT * FROM Categorie C2
                                        WHERE C2.idCategorieMere = C.idCategorie)";
                            $requete = oci_parse($connect, $sql);
                            $result = oci_execute($requete);
                            if ($result) {
                                $categories = array();
                                oci_fetch_all($requete, $categories);
                                for ($i=0; $i<count($categories["IDCATEGORIE"]); $i++) {
                                    echo "<option value='".$categories["IDCATEGORIE"][$i]."'>".$categories["NOMCATEGORIE"][$i]."</option>";
                                }
                            }
                        ?>
                    </select>
                    <p class="info">Seules les catégories ne possédant pas/plus de catégorie fille peuvent être supprimées.</p>
                    <input type="submit" name="supprimerCategorieForm" value="Valider">
                </form>
            </div>
        </section>
        <?php include("../include/footer.php") ?>
    </body>
</html>
