<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../public/css/style.css">
        <link rel="stylesheet" href="../public/css/header.css">
        <link rel="stylesheet" href="../public/css/consultStyle.css">
        <script src="../public/js/avisClientTri.js" defer></script>
    </head>

    <body>
        <?php include("../include/header.php"); 
        require_once("../include/checkConnexion.php");
        error_reporting(0);
        $idProduit = $_GET["idProduit"];

        $req = "SELECT prixProduit, TYPECHOIX, libelleChoix, tauxChoix, A.idChoix, nomProduit, extensionImgProduit
                FROM Produit P, Choix C, Affecter A
                WHERE P.idProduit = A.idProduit AND C.idChoix = A.idChoix
                    AND P.idProduit = :idProduit 
                ORDER BY TYPECHOIX";

        $listeChoix = oci_parse($connect, $req) ;

        oci_bind_by_name($listeChoix, ":idProduit", $idProduit);

        $result = oci_execute($listeChoix);
        if (!$result) {
            $e = oci_error($listeChoix);  // on récupère l'exception liée au pb d'execution de la requete
            print htmlentities($e['message'].' pour cette requete : '.$e['sqltext']);	
        }

        $dict = array();
        while (($leChoix = oci_fetch_assoc($listeChoix)) != false) {
            $prixProduit = $leChoix['PRIXPRODUIT']; 
            $nomProduit = $leChoix['NOMPRODUIT'];
            $extProduit = $leChoix['EXTENSIONIMGPRODUIT'];
            if (!key_exists($leChoix['TYPECHOIX'], $dict)) {
                $dict[$leChoix['TYPECHOIX']] = array();
            }
            array_push($dict[$leChoix['TYPECHOIX']], array("libelleChoix" => $leChoix['LIBELLECHOIX'], "tauxChoix" => $leChoix['TAUXCHOIX'], "idChoix" => $leChoix['IDCHOIX']));
        }
        
        $taux = 1;

        foreach ($dict as $key => $value) {
            foreach($value as $infos) {
                $taux *= $infos['tauxChoix'];
            }
        }
        $prixProduit = $taux * $prixProduit;
        ?>
        <div id="consulte">
            <div id="img">
                <img src="../public/images/produits/<?= $_GET['idProduit'] ?>.<?= $extProduit ?>" alt="image du produit">
            </div>
            <div id="donnees">
                <div id="produit">
                    <h1> <?= $nomProduit ?> </h1>
                    <form action="post">
                        <div>
                            <input type="button" name="ajoutPanier" value="Ajouter au panier">
                            <p>Prix: <?= $prixProduit ?>€</p>
                        </div>
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
                                        <input type="radio" id="choix-<?= $infos["idChoix"] ?>" name="choix-<?=$infos["typeChoix"]?>" value="<?= $infos['tauxChoix'] ?>" checked><label for="choix-<?= $infos["idChoix"] ?>"><?= $infos["libelleChoix"] ?></label>
                                    </div>
                                <?php
                                    $first = false;
                                } else {
                                ?>
                                <div>
                                    <input type="radio" id="choix-<?= $infos["idChoix"] ?>" name="choix-<?=$infos["typeChoix"]?>" value="<?= $infos['tauxChoix'] ?>"><label for="choix-<?= $infos["idChoix"] ?>"><?= $infos["libelleChoix"] ?></label>
                                </div>
                                <?php 
                                }
                            } 
                    ?>
                            </div>
                        <?php } ?>
                </div> 
                        <?php
                        oci_free_statement($listeChoix);

                        $req = "SELECT prixProduit, prixBaseProduit, delaiLivraisonProduit, TO_CHAR(dateRetractationProduit, 'dd/mm/YYYY') AS dateRetractationProduit, garantieProduit, verifierProduit
                                FROM Produit
                                WHERE idProduit = :idProduit";

                        $infosProduit = oci_parse($connect, $req) ;
                        oci_bind_by_name($infosProduit, ":idProduit", $idProduit);

                        $result = oci_execute($infosProduit);
                        if (!$result) {
                            $e = oci_error($infosProduit);  // on récupère l'exception liée au pb d'execution de la requete
                            print htmlentities($e['message'].' pour cette requete : '.$e['sqltext']);	
                        }
                        if(($lesInfos = oci_fetch_assoc($infosProduit)) != false) { ?>
                            <div id="infos">
                            <div><h3>Details</h3></div>
                        <?php
                            $tauxReduc = (1 - ($lesInfos['PRIXPRODUIT'] / $lesInfos['PRIXBASEPRODUIT'])) * 100;
                        ?>
                            <div>-<?= round($tauxReduc) ?>% vs prix neuf (<?= $lesInfos['PRIXBASEPRODUIT'] ?>€)</div>
                                <div>Livraison en <?= $lesInfos['DELAILIVRAISONPRODUIT'] ?> jours offerte</div>
                                <div>Changez d'avis jusqu'au <?= $lesInfos['DATERETRACTATIONPRODUIT'] ?> </div>
                                <div>Garantie contractuelle <?= $lesInfos['GARANTIEPRODUIT'] ?> mois</div>
                        <?php
                            if ($lesInfos['VERIFIERPRODUIT']) { ?>
                                <div>Reconditionneur vérifié</div>
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
                        if (!$result) {
                            $e = oci_error($caracProduit);  // on récupère l'exception liée au pb d'execution de la requete
                            print htmlentities($e['message'].' pour cette requete : '.$e['sqltext']);	
                        }
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
                        if (!$result) {
                            $e = oci_error($avisProduitPNote);  // on récupère l'exception liée au pb d'execution de la requete
                            print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);	
                        }

                        $avisProduitTot = oci_parse($connect, $req2);
                        oci_bind_by_name($avisProduitTot, ":idProduit", $idProduit);

                        $result = oci_execute($avisProduitTot);
                        if (!$result) {
                            $e = oci_error($avisProduitTot);  // on récupère l'exception liée au pb d'execution de la requete
                            print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);	
                        }

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
                            if (!$result) {
                                $e = oci_error($listeAvisProduit);  // on récupère l'exception liée au pb d'execution de la requete
                                print htmlentities($e['message'] . ' pour cette requete : ' . $e['sqltext']);	
                            } 
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
                                <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>