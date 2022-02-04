import api from "../../helpers/php_api.js";


export function HeroImage() {
    const $section = document.createElement('section');
    
    
    $section.innerHTML = `
    
            <article class="hero-image" >
                <aside class="hero-image-opacity" >
                    <div class="hero-image-content">
                        <h2 class="hero-image-title" >
                        ${api.NAME.toUpperCase()}
                        </h2>
                        <p id='hero-image-subtitle'>A lot of technology information<br>in one place!</p>
                    </div>
                </aside>
            </article>
        



        `;
    

    return $section;
}