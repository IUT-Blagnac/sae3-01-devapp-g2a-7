<?php
    require_once("checkConnexion.php");
    error_reporting(0);

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
        echo "</ul><style>
            #ul".$idUl." {
                display:none;
            }
            #li".$idCategorieMere.":hover #ul".$idUl." {
                display:block;
            } </style>";
    }
?>

<header>
    <div>
        <a href="../pages/index.php" class="logo">
            <img src="../public/images/logo.png" alt="Revive">
            <span>REVIVE</span>
        </a>
    </div>
    <nav>
        <a href="">
            <img src="../public/images/information.png" alt="Information">
            <span>INFORMATION</span>
        </a>
        <?php if (isset($_SESSION['CLIENT']) || isset($_SESSION['ADMIN'])) { ?>
            <a href="./consultCompte.php">
                <img src="../public/images/userIcon.png" alt="Icon utilisateur">
                <span>MON PROFIL</span>
            </a>
        <?php } else { ?>
            <a href="./connexion.php">
                <img src="../public/images/userIcon.png" alt="Icon utilisateur">
                <span>CONNEXION</span>
            </a>
        <?php } ?>
        <a href="">
            <img src="../public/images/pannier.png" alt="Panier">
            <span>PANIER</span>
        </a>
    </nav>
    <div class="searchBox">
        <form action="" method="GET">
            <input type="text" placeholder="Rechercher un produit..." name="query">
            <button><img src="../public/images/loupe.png" alt="Rechercher"></button>
        </form>
    </div>
</header>
<div class="subHeader">
    <div class="navbar">
        <div id="div-submenu">
            <a href="#" id="titleSubmenu">
                <img src="../public/images/menuIcon.png" alt="Menu Icon"> NOS PRODUITS
            </a>
            <?php afficher_categories() ?>
        </div>
        <a href="#">REVENDRE</a>
    </div>
</div>
