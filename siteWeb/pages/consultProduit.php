<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../public/css/style.css">
        <link rel="stylesheet" href="../public/css/header.css">
        <link rel="stylesheet" href="../public/css/consultStyle.css">
    </head>

    <body>
        <?php include("../include/header.php"); ?>
        <div id="consulte">
            <div id="img">
                <img src="" alt="image du produit">
            </div>
            <div id="produit">
                <h1> <?= $_GET['nomProduit'] ?> </h1>
                <form action="post">
                    <input type="button" name="ajoutPanier" value="Ajouter au panier">
                <?php

                    require_once("../include/checkConnexion.php");
                    error_reporting(0);
                    $idProduit = "1";

                    $req = "SELECT TYPECHOIX, libelleChoix, tauxChoix 
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
                        if (!key_exists($leChoix['TYPECHOIX'], $dict)) {
                            $dict[$leChoix['TYPECHOIX']] = array();
                        }
                        array_push($dict[$leChoix['TYPECHOIX']], array("libelleChoix" => $leChoix['LIBELLECHOIX'], "tauxChoix" => $leChoix['TAUXCHOIX']));
                    }
                    foreach ($dict as $key => $value) {
                        echo "<div class=\"choix\">
                                <h3>", $key, "</h3>";
                        foreach($value as $infos) { ?>
                            <input type="radio" name="choix-<? $key ?>" value="<? $infos['tauxChoix'] ?>"><br>";
                        <? }
                        echo "</div>";
                    }
                    echo "</div>";
                    oci_free_statement($listeChoix);

                    $req = "SELECT nomprixProduit, prixBaseProduit, delaiLivraisonProduit, dateRetractationProduit, garantieProduit, verifierProduit
                            FROM Produit
                            WHERE idProduit = :idProduit";

                    $infosProduit = oci_parse($connect, $req) ;
                    oci_bind_by_name($infosProduit, ":idProduit", $idProduit);

                    $result = oci_execute($infosProduit);
                    if (!$result) {
                        $e = oci_error($infosProduit);  // on récupère l'exception liée au pb d'execution de la requete
                        print htmlentities($e['message'].' pour cette requete : '.$e['sqltext']);	
                    }
                    if(($lesInfos = oci_fetch_assoc($infosProduit)) != false) {
                        echo "<div id=\"infos\">";
                        $tauxReduc = (1 - ($lesInfos['PRIXPRODUIT'] /$lesInfos['PRIXBASEPRODUIT']))*100;
                        echo "<div>", round($tauxReduc) , "% vs prix neuf</div>
                              <div>Livraison en ", $lesInfos['DELAILIVRAISONPRODUIT'], " jours offerte</div>
                              <div>Changez d'avis jusqu'au ", $lesInfos['DATERETRACTATIONPRODUIT'], "</div>
                              <div>Garantie contractuelle ", $lesInfos['GARANTIEPRODUIT'], " mois</div>";
                        if ($lesInfos['VERIFIERPRODUIT']) {
                            echo "<div>Reconditionneur vérifié</div>";
                        }
                        echo "</div>";
                    } else {
                        echo "Erreur";
                    }

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
                    echo "<div id=\"caracteristiques\">";
                    while (($laCarac = oci_fetch_assoc($caracProduit)) != false) {
                        echo "<div class=\"caracs\">
                                <div class=\"carac\">", $laCarac['LIBELLECARACTERISTIQUE'], "</div>
                                <div class=\"carac\">", $laCarac['DONNEECARACTERISTIQUE'], "</div>
                            </div>";
                    }
                    echo "</div>";
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
                        print htmlentities($e['message'].' pour cette requete : '.$e['sqltext']);	
                    }

                    $avisProduitTot = oci_parse($connect, $req2);
                    oci_bind_by_name($avisProduitTot, ":idProduit", $idProduit);

                    $result = oci_execute($avisProduitTot);
                    if (!$result) {
                        $e = oci_error($avisProduitTot);  // on récupère l'exception liée au pb d'execution de la requete
                        print htmlentities($e['message'].' pour cette requete : '.$e['sqltext']);	
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
                    $pourcent54 = round(($pourcent54 / $nbAvis)*100);
                    $pourcent43 = round(($pourcent43 / $nbAvis)*100);
                    $pourcent32 = round(($pourcent32 / $nbAvis)*100);
                    $pourcent21 = round(($pourcent21 / $nbAvis)*100);
                ?>
                    <div id="avis">
                        <div id="intitule-avis">
                            <h4>Avis client sur <?= $_GET['nomProduit'] ?></h4>
                            <div id="note-moyenne">
                                <div id="moyenne-note">
                                    <div id="etoiles">
                                        <div id="etoiles-vide"><img src="etoiles.png"></div>
                                        <div id="etoiles-bg" style="width: <?= $pourcentAvis ?>%"></div>
                                    </div>
                                    <div id="note"><?=$moyenneAvis ?>/5</div>
                                </div>
                                <div id="total-notes">(<?= $nbAvis ?> avis pour ce produit)</div>
                            </div>
                        </div>
                
                    <form action="post">
                        <h5>Filtrer par note</h5>
                        <div id="filtres-avis">
                            <div class="selection-avis">
                                <div>
                                    <input type="radio" name="choix-avis" value="Tous"> Tous
                                </div>
                                <div class="bg-barre-avis">
                                    <div class="barre-avis" style="width: 100%"></div>
                                </div>
                            </div>
                            <div class="selection-avis">
                                <div>
                                    <input type="radio" name="choix-avis" value="4-5"> Tous
                                </div>
                                <div class="bg-barre-avis">
                                    <div class="barre-avis" style="width: <? $pourcent54 ?>%"></div>
                                </div>
                            </div>
                            <div class="selection-avis">
                                <div>
                                    <input type="radio" name="choix-avis" value="3-4"> Tous
                                </div>
                                <div class="bg-barre-avis">
                                    <div class="barre-avis" style="width: <? $pourcent43 ?>%"></div>
                                </div>
                            </div>
                            <div class="selection-avis">
                                <div>
                                    <input type="radio" name="choix-avis" value="2-3"> Tous
                                </div>
                                <div class="bg-barre-avis">
                                    <div class="barre-avis" style="width: <? $pourcent32 ?>%"></div>
                                </div>
                            </div>
                            <div class="selection-avis">
                                <div>
                                    <input type="radio" name="choix-avis" value="1-2"> Tous
                                </div>
                                <div class="bg-barre-avis">
                                    <div class="barre-avis" style="width: <? $pourcent21 ?>%"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>