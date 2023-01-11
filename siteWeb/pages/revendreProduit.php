<?php
    include("../include/infoPopup.php");
    require_once("../include/checkConnexion.php");


        // Affiche un popup avec un message d'erreur
        if (!empty($messageErreur)) {
            echo '<script type="text/javascript">show_info_popup("'.$messageErreur.'", "red")</script>';
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
        <title>Revendre un Produit</title>
    </head>
    <body>
        <?php include("../include/header.php") ?>
        <section>
            <div>
                <h1>Revendre un Produit</h1>
                <form id="form-creationCompte" method="post">
                    <p>Participez à la préservation de la planète en nous revandant vos équipements au lieu de les jeter.</p>
                    <input type="text" name="type" placeholder="Quel est votre équipement ?"  maxlength="100" required>
                    <select name="etat" size="1" > 
                    <option disabled selected value="1"> Quel est son état ? </option>
                    <option value="2"> Neuf </option>
                    <option value="3"> Très bon état </option>
                    <option value="4"> Bon état </option>
                    <option value="5"> Mauvais état </option>
                    <option value="6"> Défectueux </option>      
                    </select>
                    <input type="text" name="prix" placeholder="Estimez son prix ?"  maxlength="100" required>
                    <input type="text" name="infos" placeholder="Informations supplémentaires" maxlength="140" >
                    <p class="info">En validant, vous certifiez que les données entrées sont exactes et complètes.</p>
                    <input type="submit" name="revendreProduitForm" value="Valider">
                </form>
            </div>
        </section>
        <?php
        //si le bouton submit est cliqué, affiche un message : ok
        if (isset($_POST['revendreProduitForm'])) {
            echo '<script type="text/javascript">show_info_popup("Votre produit à bien été ajouté")</script>';
        }
         include("../include/footer.php") ?>
    </body>
</html>