export function Header() {

  const $section = document.createElement("section");
  
  $section.innerHTML = `
  <header id="header">
    <nav class="navbar">

      <p class="navbar-logo">TB</p>

      
        <div class='navbar-center'>
          <span class="navbar-center-element"><span class="material-icons-round">cottage</span>Home</span>
          <span class="navbar-center-element"><span class="material-icons-round">category</span>Categories</span>
        
          <span class="navbar-center-element"><span class="material-icons-round">add_to_home_screen</span>Contact</span>
      
        </div>

        <div class='navbar-right'>
          <div>
          <span class="material-icons-round">search</span><span class='navbar-span'>Search</span>
          </div>
          <div>
          <span class="material-icons-round">person</span><span class='navbar-span'>Account</span> 
          </div>
        </div>
        
    </nav>
  </header>


          <button class="ham-button hamburger hamburger--collapse" type="button">
            <span class="hamburger-box">
              <span class="hamburger-inner"></span>
            </span>
          </button>

  `;

  return $section;
}
