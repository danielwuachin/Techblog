import { MethodGet } from '../../helpers/ajax.js';
import api from '../../helpers/php_api.js';


export function Carousel() {
  let props = {
    url: api.PUBLICATIONS,
    cbSuccess: function (resp){
      console.log(resp);
    }
  }

  MethodGet(props)


  const $section = document.createElement('section');
  $section.innerHTML = '<p>holaa</p>';

  return $section;

}