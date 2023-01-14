<!DOCTYPE html>
<html lang="fr" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Ajouter un produit</title>
    </head>
    <body>
        <?php
            include("checkConnexion.php");
            verifier_page();
            if (isset($_SESSION["CLIENT"])) {
                echo "CLIENT";
            } else if (isset($_SESSION["ADMIN"])) {
                echo "ADMIN";
            }

            // Tests
            echo "ééé";
            echo htmlentities("ééé", ENT_COMPAT, 'UTF-8');
        ?>
        TODO
    </body>
</html>
