import { App } from "./app.js";

const d = document;

d.addEventListener("DOMContentLoaded", App);

setTimeout(() => {
  d.addEventListener('click', (e) =>{
    if(e.target.matches('.hamburger') || e.target.matches('.hamburger-box') || e.target.matches('.hamburger-inner')){
      d.getElementById('header').classList.toggle('header-active');
    }
  });


  var hamburger = d.querySelector(".hamburger");
  hamburger.addEventListener("click", function () {
    hamburger.classList.toggle("is-active");
});
}, 50);




/* detect the hash changes in te url 
window.addEventListener('hashchange', () => {
    api.page = 1
    App()
});*/
