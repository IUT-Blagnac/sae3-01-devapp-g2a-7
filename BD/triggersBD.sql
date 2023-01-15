-- Modification des stocks des produits d'une commande validée
CREATE OR REPLACE TRIGGER ModifierStock AFTER INSERT OR UPDATE
    ON Renseigner FOR EACH ROW
DECLARE
    stockProduit Produit.quantiteStockProduit%TYPE;
BEGIN
    SELECT quantiteStockProduit INTO stockProduit FROM Produit
    WHERE idProduit = :NEW.idProduit;

    UPDATE Produit P
    SET P.quantiteStockProduit = P.quantiteStockProduit - :NEW.quantiteProduit
    WHERE P.idProduit = :NEW.idProduit;

    IF stockProduit - :NEW.quantiteProduit = 0 THEN
        UPDATE Produit P
        SET P.vendreProduit = 0
        WHERE P.idProduit = :NEW.idProduit;
    ELSE
        UPDATE Produit P
        SET P.vendreProduit = 1
        WHERE P.idProduit = :NEW.idProduit;
    END IF;
END;
/


-- Vérification du stock d'un produit avant son ajout dans un panier
CREATE OR REPLACE TRIGGER Verif_Stock_Produit
    BEFORE INSERT ON Contenir FOR EACH ROW
DECLARE
    -- variable
    quantite_stock_produit PRODUIT.QUANTITESTOCKPRODUIT%TYPE;
BEGIN
    -- selectionner la quantité de stock de l'article
    SELECT QUANTITESTOCKPRODUIT INTO quantite_stock_produit
    FROM PRODUIT
    WHERE IDPRODUIT = :NEW.IDPRODUIT;

    -- si la quantité de stock de l'article est inférieure à la quantité de l'article dans la table contenir
    IF quantite_stock_produit < :NEW.QUANTITEPRODUIT THEN
        -- afficher un message d'erreur
        RAISE_APPLICATION_ERROR(-20001, 'Il n''y a pas assez de stock pour cet article');
    END IF;
END;
/
