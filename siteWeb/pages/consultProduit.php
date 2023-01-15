<?php
    require_once("../include/checkConnexion.php");

    if(!isset($_GET['idProduit']) ) {
        header("Location: ./index.php");
    } else if (!preg_match("/[0-9]{1,}/", $_GET['idProduit'])){
        header("Location: ./index.php");
    }
    $idProduit = $_GET["idProduit"];

    $req = "SELECT prixProduit, TYPECHOIX, libelleChoix, tauxChoix, A.idChoix, nomProduit, extensionImgProduit, quantiteStockProduit
            FROM Produit P, Choix C, Affecter A
            WHERE P.idProduit = A.idProduit AND C.idChoix = A.idChoix
                AND P.idProduit = :idProduit
            ORDER BY TYPECHOIX";

    $listeChoix = oci_parse($connect, $req) ;

    oci_bind_by_name($listeChoix, ":idProduit", $idProduit);

    $result = oci_execute($listeChoix);

    $produitExiste = oci_parse($connect, "SELECT idProduit, vendreProduit FROM Produit WHERE idProduit=".$_GET['idProduit']);
    $result = oci_execute($produitExiste);

    $produit = array();
    oci_fetch_all($produitExiste, $produit);
    if (empty($produit["IDPRODUIT"]) || $produit["VENDREPRODUIT"][0] == "0") {
        header("Location: index.php");
    }

    $dict = array();
    while (($leChoix = oci_fetch_assoc($listeChoix)) != false) {
        $prixProduit = $leChoix['PRIXPRODUIT'];
        $nomProduit = $leChoix['NOMPRODUIT'];
        $extProduit = $leChoix['EXTENSIONIMGPRODUIT'];
        $stockProduit = $leChoix['QUANTITESTOCKPRODUIT'];
        if (!key_exists($leChoix['TYPECHOIX'], $dict)) {
            $dict[$leChoix['TYPECHOIX']] = array();
        }
        array_push($dict[$leChoix['TYPECHOIX']], array("libelleChoix" => $leChoix['LIBELLECHOIX'], "tauxChoix" => $leChoix['TAUXCHOIX'], "idChoix" => $leChoix['IDCHOIX']));
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../public/css/style.css">
        <link rel="stylesheet" href="../public/css/header.css">
        <link rel="stylesheet" href="../public/css/footer.css">
        <link rel="stylesheet" href="../public/css/consultStyle.css">
        <script src="../public/js/avisClientTri.js" defer></script>
        <script src="../public/js/prixProduit.js" defer></script>
        <title>Consulter produit</title>
    </head>

    <body>
        <?php
            include("../include/header.php");
            include("../include/infoPopup.php");
            if (isset($_SESSION['ajoutPanier']) && $_SESSION['ajoutPanier'] == true) {
                echo '<script type="text/javascript">show_info_popup("Le produit a bien été ajouté à votre panier.", "var(--green-blue)")</script>';
                unset($_SESSION['ajoutPanier']);
            }
        ?>
        <div id="consulte">
            <div id="img">
                <img src="../public/images/produits/<?= $_GET['idProduit'] ?>.<?= $extProduit ?>" alt="image du produit">
            </div>
            <div id="donnees">
                <div id="produit">
                    <h1> <?= $nomProduit ?> </h1>
                    <form action="../include/ajouterProduitPanier.php" method="POST">
                        <div id="affichage-ajout-panier">
                            <div id="ajout-panier">
                                <select name="quantiteProduit" id="quantiteProduit">
                                    <?php
                                    for($i = 1; $i <= $stockProduit; $i++) {
                                        if ($i == 1) { ?>
                                            <option value='<?= $i; ?>' selected><?= $i; ?></option>
                                        <?php } else { ?>
                                            <option value='<?= $i; ?>'><?= $i; ?></option>
                                        <?php }
                                    } ?>
                                </select>
                                <input type="hidden" name="idProduit" value="<?= $idProduit ?>">
                                <input type="hidden" name="extensionImgProduit" value="<?= $extProduit ?>">
                                <input type="hidden" name="quantiteStockProduit" value="<?= $stockProduit ?>">
                                <input type="hidden" name="nomProduit" value="<?= $nomProduit ?>">
                                <input type="submit" name="ajoutPanier" value="Ajouter au panier">
                            </div>
                            <input id="prixProduitInput" name="prixProduit" type="hidden" value="">
                            <p>Prix: <span id="prixProduit" data-prix="<?= $prixProduit ?>"><?= $prixProduit ?></span>€</p>
                        </div>
                        <div id="affichage-choix">
                    <?php
                        foreach ($dict as $key => $value) {
                            $first = true;
                            ?>
                            <div class="choix">
                                    <h3><?= $key ?></h3>
                            <?php
                            foreach($value as $infos) {
                                if ($first) { ?>
                                    <div>
                                        <input type="radio" id="choix-<?= $infos["idChoix"] ?>" class="selectionChoix" name="choix-<?= $key ?>" value="<?= $infos["libelleChoix"] ?>" data-taux="<?= $infos['tauxChoix'] ?>" checked><label for="choix-<?= $infos["idChoix"] ?>"><?= $infos["libelleChoix"] ?></label>
                                    </div>
                                <?php
                                    $first = false;
                                } else {
                                ?>
                                <div>
                                    <input type="radio" id="choix-<?= $infos["idChoix"] ?>" class="selectionChoix" name="choix-<?= $key ?>" value="<?= $infos["libelleChoix"] ?>" data-taux="<?= $infos['tauxChoix'] ?>"><label for="choix-<?= $infos["idChoix"] ?>"><?= $infos["libelleChoix"] ?></label>
                                </div>
                                <?php
                                }
                            }
                    ?>
                            </div>
                        <?php } ?>
                        </div>
                    </form>
                </div>
                        <?php
                        oci_free_statement($listeChoix);

                        $req = "SELECT detailsProduit, prixProduit, prixBaseProduit, delaiLivraisonProduit, TO_CHAR(dateRetractationProduit, 'dd/mm/YYYY') AS dateRetractationProduit, garantieProduit, verifierProduit
                                FROM Produit
                                WHERE idProduit = :idProduit";

                        $infosProduit = oci_parse($connect, $req) ;
                        oci_bind_by_name($infosProduit, ":idProduit", $idProduit);

                        $result = oci_execute($infosProduit);
                        if(($lesInfos = oci_fetch_assoc($infosProduit)) != false) { ?>
                            <div id="infos-details">
                            <div><h3>Details</h3></div>
                        <?php
                            $tauxReduc = (1 - ($lesInfos['PRIXPRODUIT'] / $lesInfos['PRIXBASEPRODUIT'])) * 100;
                        ?>
                            <div><p><?= $lesInfos['DETAILSPRODUIT'] ?></p></div>
                            <div><p>-<?= round($tauxReduc) ?>% vs prix neuf (<span data-prix="<?= $lesInfos['PRIXBASEPRODUIT'] ?>" id="prixBaseProduit"><?= $lesInfos['PRIXBASEPRODUIT'] ?></span>€)</p></div>
                            <div><p>Livraison en <?= $lesInfos['DELAILIVRAISONPRODUIT'] ?> jours offerte</p></div>
                            <div><p>Changez d'avis jusqu'au <?= $lesInfos['DATERETRACTATIONPRODUIT'] ?></p></div>
                            <div><p>Garantie contractuelle <?= $lesInfos['GARANTIEPRODUIT'] ?> mois</p></div>
                        <?php
                            if ($lesInfos['VERIFIERPRODUIT']) { ?>
                                <div><p>Reconditionneur vérifié</p></div>
                            <?php } ?>
                        </div>
                        <?php }

                        oci_free_statement($infosProduit);

                        $req = "SELECT libelleCaracteristique, donneeCaracteristique
                                FROM Produit P, Assigner A, Caracteristique C
                                WHERE P.idProduit = A.idProduit AND C.idCaracteristique = A.idCaracteristique
                                AND P.idProduit = :idProduit";

                        $caracProduit = oci_parse($connect, $req);
                        oci_bind_by_name($caracProduit, ":idProduit", $idProduit);

                        $result = oci_execute($caracProduit);
                        ?>
                    <div id="caracteristiques">
                                <h3>Caractéristiques</h3>
                        <?php
                        while (($laCarac = oci_fetch_assoc($caracProduit)) != false) { ?>
                            <div class="caracs">
                                    <div class="carac"><?= $laCarac['LIBELLECARACTERISTIQUE'] ?></div>
                                    <div class="carac"><?= $laCarac['DONNEECARACTERISTIQUE'] ?> </div>
                                </div>
                        <?php } ?>
                        </div>
                        <?php
                        oci_free_statement($caracProduit);

                        $req = "SELECT noteAvis, COUNT(*) as nombreAvis
                                FROM DonnerAvis
                                WHERE idProduit = :idProduit
                                GROUP BY noteAvis";

                        $req2 = "SELECT COUNT(*) as nombreTotAvis, AVG(noteAvis) as moyAvis
                                FROM DonnerAvis
                                WHERE idProduit = :idProduit";

                        $avisProduitPNote = oci_parse($connect, $req);
                        oci_bind_by_name($avisProduitPNote, ":idProduit", $idProduit);

                        $result = oci_execute($avisProduitPNote);

                        $avisProduitTot = oci_parse($connect, $req2);
                        oci_bind_by_name($avisProduitTot, ":idProduit", $idProduit);

                        $result = oci_execute($avisProduitTot);

                        if (($avisTot = oci_fetch_assoc($avisProduitTot)) != false) {
                            $moyenneAvis = round($avisTot['MOYAVIS'], 1);
                            $nbAvis = $avisTot['NOMBRETOTAVIS'];
                            $pourcentAvis = $moyenneAvis * 20;
                        }
                        $pourcent54 = 0;
                        $pourcent43 = 0;
                        $pourcent32 = 0;
                        $pourcent21 = 0;
                        while (($avisNote = oci_fetch_assoc($avisProduitPNote)) != false) {
                            if ($avisNote['NOTEAVIS'] == 5) {
                                $pourcent54 += $avisNote['NOMBREAVIS'];
                            }
                            if ($avisNote['NOTEAVIS'] == 4) {
                                $pourcent54 += $avisNote['NOMBREAVIS'];
                                $pourcent43 += $avisNote['NOMBREAVIS'];
                            }
                            if ($avisNote['NOTEAVIS'] == 3) {
                                $pourcent43 += $avisNote['NOMBREAVIS'];
                                $pourcent32 += $avisNote['NOMBREAVIS'];
                            }
                            if ($avisNote['NOTEAVIS'] == 2) {
                                $pourcent32 += $avisNote['NOMBREAVIS'];
                                $pourcent21 += $avisNote['NOMBREAVIS'];
                            }
                            if ($avisNote['NOTEAVIS'] == 1) {
                                $pourcent21 += $avisNote['NOMBREAVIS'];
                            }
                        }
                        if ($nbAvis !=0) {
                            $pourcent54 = round(($pourcent54 / $nbAvis) * 100);
                            $pourcent43 = round(($pourcent43 / $nbAvis) * 100);
                            $pourcent32 = round(($pourcent32 / $nbAvis) * 100);
                            $pourcent21 = round(($pourcent21 / $nbAvis) * 100);
                        }
                        oci_free_statement($avisProduitPNote);
                        oci_free_statement($avisProduitTot);
                    ?>
                    <div id="avis">
                        <div id="intitule-avis">
                            <h4>Avis client sur <?= $nomProduit ?></h4>
                            <div id="note-moyenne">
                                <div id="moyenne-note">
                                    <div id="etoiles">
                                        <div id="etoiles-vide"><img src="../public/images/etoiles.png"></div>
                                        <div id="etoiles-bg" style="width: <?= $pourcentAvis ?>%"></div>
                                    </div>
                                    <div id="note"><?=$moyenneAvis ?>/5</div>
                                </div>
                                <div id="total-notes">(<?= $nbAvis ?> avis pour ce produit)</div>
                            </div>
                        </div>
                        <?php if(isset($_SESSION["CLIENT"])) { ?>
                        <div>
                            <a id="ajout-avis" href="formulaireAjoutAvis.php?idProduit=<?= $_GET['idProduit'] ?>">Ajouter un Avis</a>
                        </div>
                        <?php } else { ?>
                        <div>
                            <a id="ajout-avis" href="./connexion.php">Connectez-vous pour donner un avis</a>
                        </div>
                        <?php } ?>
                        <div>
                            <h5 id="filtrerParNote">Filtrer par note</h5>
                            <div id="filtres-avis">
                                <div class="selection-avis">
                                    <div>
                                        <input type="radio" id="tous" class="choix-avis" name="choix-avis" value="tous" checked><label for="tous">Tous</label>
                                    </div>
                                    <div class="bg-barre-avis">
                                        <div class="barre-avis" style="width: 100%"></div>
                                    </div>
                                    <div class="pourcent-avis">100%</div>
                                </div>
                                <div class="selection-avis">
                                    <div>
                                        <input type="radio" id="4-5" class="choix-avis" name="choix-avis" value="4-5"><label for="4-5">4-5</label>
                                    </div>
                                    <div class="bg-barre-avis">
                                        <div class="barre-avis" style="width: <?= $pourcent54 ?>%"></div>
                                    </div>
                                    <div class="pourcent-avis"><?= $pourcent54?>%</div>
                                </div>
                                <div class="selection-avis">
                                    <div>
                                        <input type="radio" id="3-4" class="choix-avis" name="choix-avis" value="3-4"><label for="3-4">3-4</label>
                                    </div>
                                    <div class="bg-barre-avis">
                                        <div class="barre-avis" style="width: <?= $pourcent43 ?>%"></div>
                                    </div>
                                    <div class="pourcent-avis"><?= $pourcent43?>%</div>
                                </div>
                                <div class="selection-avis">
                                    <div>
                                        <input type="radio" id="2-3" class="choix-avis" name="choix-avis" value="2-3"><label for="2-3">2-3</label>
                                    </div>
                                    <div class="bg-barre-avis">
                                        <div class="barre-avis" style="width: <?= $pourcent32 ?>%"></div>
                                    </div>
                                    <div class="pourcent-avis"><?= $pourcent32?>%</div>
                                </div>
                                <div class="selection-avis">
                                    <div>
                                        <input type="radio" id="1-2" class="choix-avis" name="choix-avis" value="1-2"><label for="1-2">1-2</label>
                                    </div>
                                    <div class="bg-barre-avis">
                                        <div class="barre-avis" style="width: <?= $pourcent21 ?>%"></div>
                                    </div>
                                    <div class="pourcent-avis"><?= $pourcent21?>%</div>
                                </div>
                            </div>
                        </div>
                        <?php

                            $req = "SELECT prenomClient, nomClient, descriptionAvis, noteAvis, TO_CHAR(dateAvis, 'dd/mm/YYYY') as dateAvis
                                    FROM Client C, donnerAvis D
                                    WHERE C.idClient = D.idClient
                                    AND idProduit = :idProduit";

                            $listeAvisProduit = oci_parse($connect, $req);
                            oci_bind_by_name($listeAvisProduit, ":idProduit", $idProduit);

                            $result = oci_execute($listeAvisProduit);
                        ?>
                        <div id= "liste-avis">
                            <?php
                                while (($lavis = oci_fetch_assoc($listeAvisProduit)) != false) { ?>
                                    <div class="avis-produit" data-note="<?= $lavis['NOTEAVIS'] ?>">
                                        <div class="haut-avis">
                                            <div class="nom-prenom">
                                                <p><b><?= $lavis['PRENOMCLIENT'] ?> <?= $lavis['NOMCLIENT'] ?></b></p>
                                                <p><?= $lavis['DATEAVIS'] ?></p>
                                            </div>
                                            <div class="note-avis">
                                                <p><?= $lavis['NOTEAVIS'] ?>/5</p>
                                            </div>
                                        </div>
                                        <div class="description-avis">
                                            <p><?= $lavis['DESCRIPTIONAVIS'] ?></p>
                                        </div>
                                    </div>
                                <?php }
                                oci_free_statement($listeAvisProduit); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include("../include/footer.php") ?>
    </body>
</html>
