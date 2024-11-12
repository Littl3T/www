//Commentaire
/*Commentaire multiligne*/

//Utilisation d'un ";" à la fin d'une ligne de commande

/*
Déclaration d'une varibale via le "let"
Attribution avec le "="
*/


let myStr = "HelloWorld";
let myInt = 10;
let myBool = false;
let myArray = ['a',1,true];
let myObject = document.querySelector('h1');


/*
Opérateurs
    +
    Adition 2+2
    Concaténation 'a'+'a'

    -,*,/
    Mathématiques de base

    ===
    Vérification d'une égalité (== en python)

    !==
    Vérifier l'inégalité

    !
    Retourne l'inverse logique ==> !true -> false
*/
let myCalc = myInt*2;
let myStrlonger = myStr + myStr + 'blablabla';


// IF ELSE //
let iceCream = "chocolate";
if (iceCream === "chocolate") {
  alert("Yay, I love chocolate ice cream!");
} else {
  alert("Awwww, but chocolate is my favorite…");
}


// FONCTIONS //
function equal(num1,num2) {
    let result = num1 === num2;
    return result;
}
// appeller la méthode : equal(Myvar1, Myvar2)


// Events
document.querySelector("h1").addEventListener("click",function(){
    let test = document.querySelector("h1");
    test.textContent = test.textContent+'a';
});


// changer une proprété d'un élément html 
const Heading = document.querySelector("h1");
Heading.textContent = myStr;