# WP-Directorio-de-Negocios-con-filtro-AJAX
Esta es una implementación sencilla, para listar y filtrar una lista de negocios en una página WordPress, los cuales tendrán la característica de que algunos por ser destacados tendrán prioridad al aparecer y más campos a desplegar. Con un filtro que utiliza AJAX.

#### Recordar que, los archivos en la parte principal (functions.php, new-template.php, etc) debería estar dentro de wp-content, en la carpeta themes, en la carpeta de su tema hijo.

## Para hacer que funcione

-En el header.php hay que llamar a los scripts que creamos:

    <script type='text/javascript' src="<?php echo get_template_directory_uri(); ?>/assets/js/ajax-scripts.js"></script>
    
-Para los campos personalizados, yo usé Advanced Custom Fields. Todos los campos que están actualmente tendrán que ser reemplazados por los campos que usted quiera utilizar.

-Implementar en el functions del child theme de su theme WordPress.


Más que todo, lo que puedo ofrecer aquí, es el hecho de que los listing de negocios pueden ser destacados o no (los que pagan se destacan) y cuando es así (el campo destacado fue creado con Advanced Custom field) entonces siempre aparecen de primero, tanto cuando filtras como cuando los muestras todos. Es algo que aunque simple, no lo he visto en otro lado, entonces con esta implementación puedes crear tu tipo de contenido personalizado, con sus taxonomías para filtrar, y cuando listes puedes priorizar los que paguen de los que no. Además de mostrar un contenido distinto los que pagan de los que no.
