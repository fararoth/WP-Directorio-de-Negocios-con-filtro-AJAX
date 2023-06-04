<?php

/**
 * Load parent theme style
 * Aquí va la función que llama a los estilos del theme padre
 */




// Crear contenido personalizado negocios en este caso

function registrar_tipo_de_contenido_personalizado() {
  $args = array(
      'public' => true,
      'label'  => 'Negocios',
      // Añade más argumentos según tus necesidades
  );
  register_post_type( 'negocios', $args );
}
add_action( 'init', 'registrar_tipo_de_contenido_personalizado' );

// Crear la taxonomía "localidad"
function crear_taxonomia_localidad() {
  $labels = array(
      'name'              => _x('Localidades', 'taxonomy general name'),
      'singular_name'     => _x('Localidad', 'taxonomy singular name'),
      'search_items'      => __('Buscar Localidades'),
      'all_items'         => __('Todas las Localidades'),
      'parent_item'       => __('Localidad Padre'),
      'parent_item_colon' => __('Localidad Padre:'),
      'edit_item'         => __('Editar Localidad'),
      'update_item'       => __('Actualizar Localidad'),
      'add_new_item'      => __('Agregar Nueva Localidad'),
      'new_item_name'     => __('Nombre de la Nueva Localidad'),
      'menu_name'         => __('Localidad'),
  );

  $args = array(
      'hierarchical'      => true,
      'labels'            => $labels,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true,
      'rewrite'           => array('slug' => 'localidad'),
  );

  register_taxonomy('localidad', 'negocios', $args);
}
add_action('init', 'crear_taxonomia_localidad');

// Crear la taxonomía "tipo_de_negocio"
function crear_taxonomia_tipo_de_negocio() {
  $labels = array(
      'name'              => _x('Tipos de Negocio', 'taxonomy general name'),
      'singular_name'     => _x('Tipo de Negocio', 'taxonomy singular name'),
      'search_items'      => __('Buscar Tipos de Negocio'),
      'all_items'         => __('Todos los Tipos de Negocio'),
      'parent_item'       => __('Tipo de Negocio Padre'),
      'parent_item_colon' => __('Tipo de Negocio Padre:'),
      'edit_item'         => __('Editar Tipo de Negocio'),
      'update_item'       => __('Actualizar Tipo de Negocio'),
      'add_new_item'      => __('Agregar Nuevo Tipo de Negocio'),
      'new_item_name'     => __('Nombre del Nuevo Tipo de Negocio'),
      'menu_name'         => __('Tipo de Negocio'),
  );

  $args = array(
      'hierarchical'      => true,
      'labels'            => $labels,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true,
      'rewrite'           => array('slug' => 'tipo-de-negocio'),
  );

  register_taxonomy('tipo_de_negocio', 'negocios', $args);
}
add_action('init', 'crear_taxonomia_tipo_de_negocio');



// Función para manejar la solicitud AJAX de filtrado de contenidos

function filtrar_negocios() {
  $localidades = isset($_POST['filters']['localidad']) ? $_POST['filters']['localidad'] : array();
  $tiposNegocio = isset($_POST['filters']['tipoNegocio']) ? $_POST['filters']['tipoNegocio'] : array();
  $destacados = isset($_POST['destacados']) ? $_POST['destacados'] : false;

  $args = array(
    'post_type' => 'negocios',
    'posts_per_page' => -1,
    'meta_query' => array(),
    'tax_query' => array(),
  );

  if (!empty($localidades)) {
    $args['tax_query'][] = array(
      'taxonomy' => 'localidad',
      'field' => 'slug',
      'terms' => $localidades,
    );
  }

  if (!empty($tiposNegocio)) {
    $args['tax_query'][] = array(
      'taxonomy' => 'tipo_de_negocio',
      'field' => 'slug',
      'terms' => $tiposNegocio,
    );
  }

  $meta_query = array();

  if (!$destacados) {
    $meta_query[] = array(
      'key' => 'destacado',
      'compare' => 'NOT EXISTS',
    );
  }

  $args['meta_query'] = $meta_query;

  $args['orderby'] = array(
    'meta_value' => 'DESC',
    'date' => 'DESC',
  );

  $custom_query = new WP_Query($args);

  if ($custom_query->have_posts()) {
    $data = array();
    $destacados_data = array();

    while ($custom_query->have_posts()) {
      $custom_query->the_post();

      $titulo = get_the_title();
      $contenido = get_the_content();
      $ubicacion = get_field('ubicacion');
      $telefono = get_field('telefono');
      $tipoNegocio_terms = get_the_terms(get_the_ID(), 'tipo_de_negocio');
      $web_site = get_field('web_site');
      $instagram_url = get_field('instagram_url');
      $facebook_url = get_field('facebook_url');
      $twitter_url = get_field('twitter_url');
      $linkedin_url = get_field('linkedin_url');
      $galeria = get_field('galeria');
      $logo = get_field('logo');

      // Verificar si se encontraron términos
      if ($tipoNegocio_terms && !is_wp_error($tipoNegocio_terms)) {
        $tipoNegocio_names = array();

        // Obtener los nombres de los términos
        foreach ($tipoNegocio_terms as $term) {
          $tipoNegocio_names[] = $term->name;
        }
      }

      $negocio = array(
        'titulo' => $titulo,
        'contenido' => $contenido,
        'ubicacion' => $ubicacion,
        'telefono' => $telefono,
        'tipo_negocio' => $tipoNegocio_names,
        'web_site' => $web_site,
        'instagram_url' => $instagram_url,
        'facebook_url' => $facebook_url,
        'twitter_url' => $twitter_url,
        'linkedin_url' => $linkedin_url,
        'galeria' => $galeria,
        'logo' => $logo,
        'destacado' => get_field('destacado') ? true : false
      );

      if ($destacados && get_field('destacado')) {
        $destacados_data[] = $negocio; // Insertar en arreglo de destacados
      } else {
        $data[] = $negocio; // Insertar en arreglo normal
      }
    }

    wp_reset_postdata();

    // Ordenar los datos
    if ($destacados) {
      $data = array_merge($destacados_data, $data); // Mezclar destacados y normales
    }

    $response = array(
      'success' => true,
      'data' => $data
    );

    wp_send_json($response);
  } else {
    $response = array(
      'success' => false,
      'message' => 'No se encontraron negocios.'
    );

    wp_send_json($response);
  }
}

add_action('wp_ajax_filtrar_negocios', 'filtrar_negocios');
add_action('wp_ajax_nopriv_filtrar_negocios', 'filtrar_negocios');


function mostrar_negocios_destacados() {
  $args = array(
    'post_type' => 'negocios',
    'posts_per_page' => -1,
    'meta_query' => array(
      array(
        'key' => 'destacado',
        'value' => '1',
        'compare' => '='
      )
    )
  );

  $custom_query = new WP_Query($args);

  if ($custom_query->have_posts()) {
    $data = array();
    while ($custom_query->have_posts()) {
      $custom_query->the_post();
      $titulo = get_the_title();
      $contenido = get_the_content();
      $tipoNegocio_terms = get_the_terms(get_the_ID(), 'tipo_de_negocio');
      $ubicacion = get_field('ubicacion');
      $telefono = get_field('telefono');
      $web_site = get_field('web_site');
      $instagram_url = get_field('instagram_url');
      $facebook_url = get_field('facebook_url');
      $twitter_url = get_field('twitter_url');
      $linkedin_url = get_field('linkedin_url');
      $galeria = get_field('galeria');
      $logo = get_field('logo');

      // Verificar si se encontraron términos
      if ($tipoNegocio_terms && !is_wp_error($tipoNegocio_terms)) {
        $tipoNegocio_names = array();

        // Obtener los nombres de los términos
        foreach ($tipoNegocio_terms as $term) {
          $tipoNegocio_names[] = $term->name;
        }
      }

      $negocio = array(
        'titulo' => $titulo,
        'contenido' => $contenido,
        'ubicacion' => $ubicacion,
        'telefono' => $telefono,
        'tipo_negocio' => $tipoNegocio_names,
        'web_site' => $web_site,
        'instagram_url' => $instagram_url,
        'facebook_url' => $facebook_url,
        'twitter_url' => $twitter_url,
        'linkedin_url' => $linkedin_url,
        'galeria' => $galeria,
        'logo' => $logo
      );

      $data[] = $negocio;
    }

    wp_reset_postdata();

    wp_send_json_success($data);
  } else {
    wp_send_json_error('No se encontraron negocios destacados.');
  }
}

function mostrar_negocios_no_destacados() {
  $args = array(
    'post_type' => 'negocios',
    'posts_per_page' => -1,
    'meta_query' => array(
      'relation' => 'OR',
      array(
        'key' => 'destacado',
        'value' => '1',
        'compare' => '!='
      ),
      array(
        'key' => 'destacado',
        'compare' => 'NOT EXISTS'
      )
    )
  );

  $custom_query = new WP_Query($args);

  if ($custom_query->have_posts()) {
    $data = array();
    while ($custom_query->have_posts()) {
      $custom_query->the_post();
      $titulo = get_the_title();
      $ubicacion = get_field('ubicacion');
      $telefono = get_field('telefono');
      $tipoNegocio_terms = get_the_terms(get_the_ID(), 'tipo_de_negocio');

      // Verificar si se encontraron términos
      if ($tipoNegocio_terms && !is_wp_error($tipoNegocio_terms)) {
        $tipoNegocio_names = array();

        // Obtener los nombres de los términos
        foreach ($tipoNegocio_terms as $term) {
          $tipoNegocio_names[] = $term->name;
        }
      }

      $negocio = array(
        'titulo' => $titulo,
        'tipo_negocio' => $tipoNegocio_names,
        'ubicacion' => $ubicacion,
        'telefono' => $telefono
      );

      $data[] = $negocio;
    }

    wp_reset_postdata();

    wp_send_json_success($data);
  } else {
    wp_send_json_error('No se encontraron negocios no destacados.');
  }
}


add_action('wp_ajax_mostrar_negocios_destacados', 'mostrar_negocios_destacados');
add_action('wp_ajax_nopriv_mostrar_negocios_destacados', 'mostrar_negocios_destacados');
add_action('wp_ajax_mostrar_negocios_no_destacados', 'mostrar_negocios_no_destacados');
add_action('wp_ajax_nopriv_mostrar_negocios_no_destacados', 'mostrar_negocios_no_destacados');


// Habilitar solicitudes AJAX en WordPress
function agregar_scripts_ajax() {
  wp_enqueue_script('ajax-scripts', get_stylesheet_directory_uri() . '/js/ajax-scripts.js', array('jquery'), '1.0', true);
  wp_localize_script('ajax-scripts', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'agregar_scripts_ajax');


function agregar_estilos_personalizados() {
  wp_enqueue_style('estilos-personalizados', get_stylesheet_directory_uri() . '/css/styles-filter.css', array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'agregar_estilos_personalizados');

function enqueue_font_awesome() {
  wp_enqueue_script('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js', array(), '5.15.3', false);
}
add_action('wp_enqueue_scripts', 'enqueue_font_awesome');

function enqueue_bootstrap() {
  wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0', 'all' );
  wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js', array( 'jquery' ), '5.3.0', true );
}

add_action( 'wp_enqueue_scripts', 'enqueue_bootstrap' );
