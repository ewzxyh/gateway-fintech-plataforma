@props(['class' => '', 'style' => '', 'alt' => 'Logo', 'width' => 'auto', 'height' => 'auto'])

<img 
  src="{{ \App\Helpers\Helper::getLogoByTheme() }}" 
  alt="{{ $alt }}" 
  class="theme-logo {{ $class }}" 
  style="{{ $style }}"
  width="{{ $width }}"
  height="{{ $height }}"
  data-light-src="{{ asset(\App\Models\App::first()->gateway_logo ?? '') }}"
  data-dark-src="{{ asset(\App\Models\App::first()->gateway_logo_dark ?? '') }}"
  onload="updateLogoByTheme(this)"
>

<script>
function updateLogoByTheme(imgElement) {
  // Função para atualizar a logo baseada no tema atual
  function setLogoByTheme() {
    const html = document.querySelector('html');
    const currentTheme = html.getAttribute('data-theme') || html.getAttribute('data-bs-theme') || 'light';
    
    const lightSrc = imgElement.getAttribute('data-light-src');
    const darkSrc = imgElement.getAttribute('data-dark-src');
    
    if (currentTheme === 'dark' && darkSrc) {
      imgElement.src = darkSrc;
    } else if (lightSrc) {
      imgElement.src = lightSrc;
    }
  }
  
  // Define a logo inicial
  setLogoByTheme();
  
  // Observa mudanças no tema
  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.type === 'attributes' && 
        (mutation.attributeName === 'data-theme' || mutation.attributeName === 'data-bs-theme')) {
        setLogoByTheme();
      }
    });
  });
  
  // Observa mudanças no elemento html
  observer.observe(document.querySelector('html'), {
    attributes: true,
    attributeFilter: ['data-theme', 'data-bs-theme']
  });
  
  // Também observa mudanças no localStorage para tema
  window.addEventListener('storage', function(e) {
    if (e.key === 'theme') {
      setLogoByTheme();
    }
  });
}
</script>
