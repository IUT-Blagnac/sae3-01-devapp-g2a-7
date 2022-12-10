<!DOCTYPE html>
<html lang="fr" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../public/css/style.css">
        <link rel="stylesheet" href="../public/css/header.css">
        <link rel="stylesheet" href="../public/css/connexionStyle.css">
        <title>Connexion</title>
    </head>
    <body>
        <?php include("../include/header.php"); ?>
        <section>
            <div>
                <h1>J'ai déjà un compte</h1>
                <form id="form-connexion" action="index.html" method="post">
                    <p>Entrez vos identifiants pour vous connecter :</p>
                    <input type="email" name="mail" placeholder="Adresse e-mail">
                    <input type="password" name="mdp" placeholder="Mot de passe">
                    <a href="#">Mot de passe oublié ?</a>
                    <input type="submit" name="connexionForm" value="Se connecter">
                </form>
                <a href="CreationCompte.php">Pas de compte ? Créez-en un !</a>
            </div>
        </section>
    </body>
</html>
