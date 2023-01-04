// sélectionne tout les élément qui ont la classe selection-avis
let selectionAvis = document.querySelectorAll('.selection-avis');
let listeAvis = document.querySelectorAll('.avis-produit');

// boucle sur tout les éléments qui ont la classe selection-avis
for (let i = 0; i < selectionAvis.length; i++) {
    // récupère l'input dans selectionAvis
    let choixInput = selectionAvis[i].querySelector('input');
    // On écoute le changement de la valeur de l'input
    choixInput.addEventListener('change', choix);
}

// fonction choix
function choix() {
    // récupère la valeur de l'input
    let choixValue = this.value;
    let note = choixValue.split('-');

    // on boucle dans les avis pour les cacher
    for (let i = 0; i < listeAvis.length; i++) {
        listeAvis[i].style.display = "none";
    }

    // on boucle dans les avis pour trouver les avis qui ont la même note que l'input et on les affiches
    for (let i = 0; i < listeAvis.length; i++) {
        let noteAvis = listeAvis[i].dataset.note;
        for (let j = 0; j < note.length; j++) {
            if (note[j] == noteAvis) {
                listeAvis[i].style.display = "block";
            } else if (choixValue == "tous") {
                listeAvis[i].style.display = "block";
            }
        }
    }
}