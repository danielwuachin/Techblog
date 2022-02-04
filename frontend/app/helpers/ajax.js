


export async function MethodGet(props) {

  let {url, cbSuccess} = props;

  await fetch(url, {
      method: 'GET',
      mode: 'cors'
  })
  .then(res => res.ok ? res.json() : Promise.reject(res))
  .then(json => cbSuccess(json))
  .catch(err => {
      let message = err.statusText || 'Ocurrio un error al acceder a la API';

      document.getElementById('main').innerHTML = `<div class="error">
      <p>Error ${err.status}: ${message}</p>
      </div>`;



      console.log(err)
  })
}




