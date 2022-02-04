/* import { methodGet } from "./helpers/ajax.js"; */
import { HeroImage } from "./components/main/heroImage.js";
import { Header } from "./components/main/header.js"
import { Carousel } from "./components/main/carousel.js";

export function App () {
    const $root = document.getElementById('root');

    $root.innerHTML = "";

    $root.appendChild(HeroImage());
    $root.appendChild(Header());
    $root.appendChild(Carousel());
    

}