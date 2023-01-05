var prix = document.getElementById("prixProduit");
var quantite = document.getElementById("quantiteProduit");
var choix = document.querySelectorAll(".selectionChoix");
var prixProduitInput = document.getElementById("prixProduitInput");

function getPrixProduit() {
    let prixProduit = prix.dataset.prix;
    return prixProduit;
}

function getQuantiteProduit() {
    let quantiteProduit = quantite.value;
    return quantiteProduit;
}

function getTaux() {
    let taux = 1;
    for (let i = 0; i < choix.length; i++) {
        if (choix[i].checked) {
            taux *= choix[i].value;
        }        
    }
    return taux;
}

function prixTotal() {
    let prixTotal = getPrixProduit() * getQuantiteProduit() * getTaux();
    return prixTotal;
}

function setPrixTotal() {
    prix.innerHTML = prixTotal();
}

function setPrixInput() {
    prixProduitInput.value = prix * getTaux();
}

for (let i = 0; i < choix.length; i++) {
    choix[i].addEventListener("change", setPrixTotal);
    choix[i].addEventListener("change", setPrixInput);
}
quantite.addEventListener("change", setPrixTotal);

setPrixTotal();
setPrixInput();