var prix = document.getElementById("prixProduit");
var prixBase = document.getElementById("prixBaseProduit");
var quantite = document.getElementById("quantiteProduit");
var choix = document.querySelectorAll(".selectionChoix");
var prixProduitInput = document.getElementById("prixProduitInput");

function getPrixProduit() {
    let prixProduit = prix.dataset.prix;
    return prixProduit;
}

function getPrixBaseProduit() {
    let prixBaseProduit = prixBase.dataset.prix;
    return prixBaseProduit;
}

function getQuantiteProduit() {
    let quantiteProduit = quantite.value;
    return quantiteProduit;
}

function getTaux() {
    let taux = 1;
    for (let i = 0; i < choix.length; i++) {
        if (choix[i].checked) {
            taux *= choix[i].dataset.taux;
        }        
    }
    return taux;
}

function prixTotal() {
    let prixTotal = getPrixProduit() * getQuantiteProduit() * getTaux();
    return prixTotal.toFixed(2);
}

function setPrixTotal() {
    prix.innerHTML = prixTotal();
}

function setPrixInput() {
    let prixProduit = prix.dataset.prix
    prixProduitInput.value = (prixProduit * getTaux()).toFixed(2);
}

function setPrixBase() {
    let prixBaseProduit = getPrixBaseProduit() * getTaux();
    prixBase.innerHTML = Math.round(prixBaseProduit);
}

for (let i = 0; i < choix.length; i++) {
    choix[i].addEventListener("change", setPrixTotal);
    choix[i].addEventListener("change", setPrixInput);
    choix[i].addEventListener("change", setPrixBase);
}
quantite.addEventListener("change", setPrixTotal);

setPrixTotal();
setPrixInput();
setPrixBase();