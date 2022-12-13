<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/header.css">
    <link rel="stylesheet" href="../public/css/panier.css">
    <title>Revive | Panier</title>
</head>

<body>

<?php
    include("../include/header.php");
    require_once("../include/connect.inc.php");
    $idClient = 1;

    $sqlContenir = "SELECT * FROM CONTENIR
    WHERE CONTENIR.IDPANIER IN (
        SELECT IDPANIER FROM PANIER
        WHERE PANIER.IDCLIENT = :idClient
    )";

    $contenir = oci_parse($connect, $sqlContenir);
    oci_bind_by_name($contenir, ":idClient", $idClient);
    $resultContenir = oci_execute($contenir);

    $sqlProduits = "SELECT * FROM PRODUIT
    WHERE PRODUIT.IDPRODUIT IN (
        SELECT IDPRODUIT FROM CONTENIR
        WHERE CONTENIR.IDPANIER IN (
            SELECT IDPANIER FROM PANIER
            WHERE PANIER.IDCLIENT = :idClient
        )
    )";

    $produits = oci_parse($connect, $sqlProduits);
    oci_bind_by_name($produits, ":idClient", $idClient);
    $resultProduits = oci_execute($produits);

    $sqlCategorie = "SELECT * FROM CATEGORIE
    WHERE CATEGORIE.IDCATEGORIE IN (
        SELECT IDCATEGORIE FROM PRODUIT
        WHERE PRODUIT.IDPRODUIT IN (
            SELECT IDPRODUIT FROM CONTENIR
            WHERE CONTENIR.IDPANIER IN (
                SELECT IDPANIER FROM PANIER
                WHERE PANIER.IDCLIENT = :idClient
            )
        )
    )";

    $categorie = oci_parse($connect, $sqlCategorie);
    oci_bind_by_name($categorie, ":idClient", $idClient);
    $resultCategorie = oci_execute($categorie);

    

?>

<section>
    <h1>Récapitulatif de mon panier</h1>
    <div class="commande">
        <div class="produits">
            <div class="produit">
                <div class="image">
                    <img src="../public/images/produit1.png" alt="NOM DU PRODUIT">
                </div>
                <div class="objet">
                    <h2>CATEGORIE - NOM DU PRODUIT</h2>
                    <p class="detail">Details objet/produit</p>
                    <p>15,33€</p>
                </div>
                <div class="prix">
                    <form action="" method="post">
                        <select name="" id="" onchange="this.form.submit()">
                            <?php for($i = 1; $i <= 100; $i++) { ?>
                                <option value="<?= $i; ?>"><?= $i; ?></option>
                            <?php } ?>
                        </select>
                    </form>
                    <p>15,33€</p>
                </div>
                <div class="supprimer">
                    <form action="" method="get">
                        <button><img src="../public/images/poubelle.png" alt="Supprimer"></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="total">
            <div class="sticky">
                <div class="prix">
                    <p>Produits</p>
                    <p>30,66</p>
                </div>
                <div class="livraison">
                    <p>Frais de livraison</p>
                    <p>GRATUIT</p>
                </div>
                <div class="prixTotal">
                    <p>Total</p>
                    <p>30,66</p>
                </div>
                <div class="valider">
                    <form action="" method="post">
                        <button>Valider ma commande</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>