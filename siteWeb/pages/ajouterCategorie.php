<?php
    require_once("../include/checkConnexion.php");
    include("../include/infoPopup.php");

    verifier_page();

    if (isset($_POST["ajouterCategorieForm"])) {
        $nomCategorie = htmlentities($_POST["nomCategorie"]);
        $idCategorieMere = htmlentities($_POST["idCategorieMere"]);
        $sql = "BEGIN Gestion_REVIVE.AjouterCategorie(:nomCategorie, :idCategorieMere); END;";
        $requete = oci_parse($connect, $sql);
        oci_bind_by_name($requete, ":nomCategorie", $nomCategorie);
        oci_bind_by_name($requete, ":idCategorieMere", $idCategorieMere);
        $result = oci_execute($requete);
        if ($result) {
            echo '<script type="text/javascript">show_info_popup("La catégorie a bien été ajoutée.", "var(--green-blue)")</script>';
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
        <title>Ajouter catégorie</title>
    </head>
    <body>
        <?php include("../include/header.php") ?>
        <section>
            <div>
                <h1>Ajouter une catégorie</h1>
                <form id="form-ajouterCategorie" method="post">
                    <p>Entrez les données de la nouvelle catégorie :</p>
                    <input type="text" name="nomCategorie" placeholder="Nom de la catégorie" required>
                    <select name="idCategorieMere" required>
                        <option disabled selected>Catégorie mère</option>
                        <option value="null">Pas de catégorie mère</option>
                        <?php
                            $sql = "SELECT idCategorie, nomCategorie FROM Categorie";
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
                    <input type="submit" name="ajouterCategorieForm" value="Valider">
                </form>
            </div>
        </section>
        <?php include("../include/footer.php") ?>
    </body>
</html>
