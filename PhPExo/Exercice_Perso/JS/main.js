const myImage = document.querySelector("img");
let mybutton = document.querySelector("button");
let myHeading = document.querySelector("h1");

myImage.onclick = () => {
    const mySrc = myImage.getAttribute("src");
    
    if(mySrc === "img/1.jpg") {
        myImage.setAttribute("src","img/2.jpg");
    } else {
        myImage.setAttribute("src","img/1.jpg");
    }
};

function setUserName() {
    const myName = prompt("please enter your name");
    localStorage.setItem("name",myName);
    myHeading.textContent = 'Hello '+myName;
};

if(!localStorage.getItem("name")) {
    setUserName();
} else {
    const  storedName = localStorage.getItem("name");
    myHeading.textContent = 'hello '+storedName;
};

mybutton.onclick = () =>{
    setUserName();
};