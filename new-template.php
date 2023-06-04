<?php
/**
 * Template Name: Negocios
 */
get_header();
the_post();
?>


<div class="main">
  <div class="container">
    <div class="content">
      <div class="vc_content">
        <h1>Directorio de negocios</h1>

        <?php
// Realiza una consulta para obtener las localidades
$localidades = get_terms(array(
  'taxonomy' => 'localidad',
  'hide_empty' => false,
));

$tipos_negocio = get_terms(array(
    'taxonomy' => 'tipo_de_negocio',
    'hide_empty' => false,
  ));

// Verifica si se encontraron localidades
if (!empty($localidades)) {
  // Mostrar el formulario con los checkboxes
  ?>
   <form id="filtro-negocios">
          <label for="localidad">Localidad:</label>
          <select id="localidad" name="localidad">
            <option value="">Todas las localidades</option>
            <?php
            // Obtén la lista de términos de la taxonomía "localidad"
            $terms = get_terms(array('taxonomy' => 'localidad', 'hide_empty' => false));
            foreach ($terms as $term) {
              echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
            }
            ?>
          </select>

          <label for="tipo-negocio">Tipo de Negocio:</label>
<select id="tipo-negocio" name="tipo-negocio">
            <option value="">Todos los tipos de negocio</option>
            <?php
            // Obtén la lista de términos de la taxonomía "tipo_de_negocio"
            $terms = get_terms(array('taxonomy' => 'tipo_de_negocio', 'hide_empty' => false));
            foreach ($terms as $term) {
              echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
            }
            ?>
          </select>

          <button id="filtrar" type="submit">Filtrar</button>
          <button type="button" id="limpiar-filtro">Mostrar todos</button>
        </form>
  <?php
} else {
  // No se encontraron tipos de negocio
  echo 'No se encontraron tipos de negocio.';
}
?>


        <div id="resultado-filtro">
        
                </div>
        </div>

        <?php the_content(); ?>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

// Código para inicializar Isotope y configurar el diseño de masonry
$(document).ready(function() {
  // Inicializar Isotope
  $('.gallery').isotope({
    itemSelector: '.gallery-item',
    layoutMode: 'masonry'
  });
  
  // Filtrar y recargar Isotope cuando se abra la galería
  $(document).on('click', '.openPopup', function(e) {
    e.preventDefault();
    $(this).siblings('.popup-container').fadeIn(); // Mostrar el popup
    
    // Recargar Isotope después de que se muestre el popup
    setTimeout(function() {
      $('.gallery').isotope('layout');
    }, 500);
  });
  
  // Agregar evento de clic al botón de cierre
  $(document).on('click', '.closePopup', function(e) {
    e.preventDefault();
    $(this).closest('.popup-container').fadeOut(); // Ocultar el popup
  });
  
  // Cerrar el popup al hacer clic fuera de él
  $(document).on('click', function(e) {
    if (!$(e.target).closest('.popup-container').length && !$(e.target).hasClass('openPopup')) {
      $('.popup-container').fadeOut(); // Ocultar el popup si se hace clic fuera de él
    }
  });
});

});
</script>

<?php get_footer(); ?>