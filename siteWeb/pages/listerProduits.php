<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/header.css">
    <link rel="stylesheet" href="../public/css/footer.css">
    <link rel="stylesheet" href="../public/css/listProduit.css">
    <title>Listage des produits</title>
</head>
<?php
include("../include/header.php");
require_once '../include/checkConnexion.php';

// Déclaration de la variable de requête
$req = "";

// Vérification si un ID de catégorie est présent dans l'URL
if (isset($_GET['idCategorie'])) {
    // Récupération de l'ID de catégorie depuis l'URL
    $idCategorie = $_GET['idCategorie'];

    // Jointure de table pour récupérer les produits de la catégorie sélectionnée
    // ainsi que ceux des catégories enfant qui ont pour idcategoriemere l'ID de la catégorie sélectionnée
    $req = "WITH
                ArboCategories (idCategorieMere) AS (
                    SELECT idCategorie
                    FROM Categorie
                    WHERE idCategorie = $idCategorie
                    UNION ALL
                    SELECT idCategorie
                    FROM Categorie C
                    INNER JOIN ArboCategories AC ON AC.idCategorieMere = C.idCategorieMere)
            SELECT * FROM Produit, ArboCategories
            WHERE Produit.idCategorie = ArboCategories.idCategorieMere
            AND Produit.vendreProduit = 1";

} else if (isset($_GET['query'])) {
    $recherche = htmlentities($_GET['query']);
    $req = "SELECT * FROM Produit
            WHERE (UPPER(nomproduit) LIKE :pquery
            OR UPPER(detailsproduit) LIKE :pquery)
            AND Produit.vendreProduit = 1";
} else {
    // Affichage de tous les produits de la table si aucune recherche n'est effectuée
    $req = "SELECT * FROM Produit WHERE Produit.vendreProduit = 1";
}

$lesProduits = oci_parse($connect, $req);

// Vérification si la requête est une recherche
if(isset($_GET['query'])) {
    $recherchebis =  '%'.strtoupper($recherche).'%';
    oci_bind_by_name($lesProduits, ":pquery", $recherchebis);
}

// Exécution de la requête
$result = oci_execute($lesProduits);

if (!$result) {
    $e = oci_error($lesProduits);
    print htmlentities($e['message'].' pour cette requete : '.$e['sqltext']);
}

if(isset($_GET['idCategorie'])) {
    // Affichage du résultat de la requête
    // récupérer la variable de la colonne "nomCategorie" dans la table "categorie" de notre base de données en fonction de "idCategorie" contenu dans l'url de la page
    $reqCategorie = "SELECT nomCategorie FROM categorie WHERE idCategorie = $idCategorie";
    $laCategorie = oci_parse($connect, $reqCategorie);
    $resultCategorie = oci_execute($laCategorie);
    $nomCategorie = oci_fetch_assoc($laCategorie)['NOMCATEGORIE'];
    ?>
    <h1> Les produits de la catégorie <?= $nomCategorie ?> </h1>
<?php }
elseif(!empty($_GET['query'])) { ?>
    <h1> Les produits contenant le mot <?= $recherche ?></h1>
<?php } else { ?>
    <h1> Tous les produits </h1>
<?php } ?>

<section>
<?php
while (($leProduit = oci_fetch_assoc($lesProduits)) != false) {
    $url_produit = 'consultProduit.php?idProduit=' . $leProduit['IDPRODUIT'];
?>
    <div>
        <img src="../public/images/produits/<?= $leProduit['IDPRODUIT'] ?>.<?= $leProduit['EXTENSIONIMGPRODUIT'] ?>" alt="<?= $leProduit['NOMPRODUIT'] ?>">
        <p> <b> <?= $leProduit['NOMPRODUIT'] ?> </b> </p>
        <p > A partir de <?= $leProduit['PRIXPRODUIT'] ?>€ </p>
        <a href="<?= $url_produit ?>"> Voir le produit </a>
    </div>
<?php } ?>

</section>

<?php
// Libération des ressources réservées par le résultat Oracle
oci_free_statement($lesProduits);
include("../include/footer.php");
?>
