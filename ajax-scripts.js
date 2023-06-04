$(document).ready(function () {

  $('#filtro-negocios').on('submit', function (event) {
      event.preventDefault(); // Prevenir comportamiento predeterminado del botón de envío
      filtrarNegocios();
  });

  function filtrarNegocios() {
      var filters = {
          localidad: $('[name="localidad"]').val(),
          tipoNegocio: $('[name="tipo-negocio"]').val()
      };

      var destacados = $('[name="destacados"]').is(':checked'); // Obtener el valor del checkbox de destacados

      var data = {
          action: 'filtrar_negocios',
          filters: filters,
          destacados: destacados // Agregar el valor de destacados en la solicitud
      };

      $.ajax({
          url: ajax_object.ajaxurl,
          type: 'POST',
          data: data,
          beforeSend: function () {
              $('#resultado-filtro').html('Cargando...');
          },
          success: function (response) {
              if (response.success) {
                  var negociosDestacados = [];
                  var negociosNoDestacados = [];

                  $.each(response.data, function (index, item) {
                      if (item.destacado) {
                          negociosDestacados.push(item);
                      } else {
                          negociosNoDestacados.push(item);
                      }
                  });

                  console.log("Destacados: ", negociosDestacados);
                  console.log("No Destacados: ", negociosNoDestacados);

                  mostrarNegociosFiltrados(negociosDestacados, negociosNoDestacados);
              } else {
                  $('#resultado-filtro').html('No se encontraron negocios.');
              }
          },
          error: function () {
              $('#resultado-filtro').html('Error en la solicitud AJAX');
          }
      });
  }

  function mostrarNegociosFiltrados(negociosDestacados, negociosNoDestacados) {
      var html = '';

      // Mostrar negocios destacados con la clase "destacado"
      $.each(negociosDestacados, function (index, item) {
          html += '<div class="negocio destacado">';

          if (item.logo) {
              html += '<div class="logo-div bytedesign-col"><img class="logo" src="' + item.logo + '"></div>';
          }

          if (item.titulo || item.contenido || item.tipo_negocio || item.ubicacion) {

              html += '<div class="contenido bytedesign-col"><h2>' + item.titulo + '</h2>' +
                  '<p>' + item.contenido + '</p>';

              if (item.tipo_negocio) {
                  html += '<p><i class="fas fa-industry"></i> Sector: ' + item.tipo_negocio.join(', ') + '</p>';
              }

              if (item.ubicacion) {
                  html += '<p><i class="fas fa-map-marker-alt"></i> Ubicación: ' + item.ubicacion + '</p>';
              }

              if (item.telefono) {
                  html += '<p><i class="fas fa-phone"></i> Teléfono: ' + item.telefono + '</p>';
              }

              if (item.web_site) {
                  html += '<div class="website"><p><i class="fas fa-globe"></i> Sitio web: </p><a href="' + item.web_site + '">' + item.web_site + '</a></div>';
              }

              html += '</div>'; // Cierre de columna contenido
          }


          if (item.instagram_url || item.facebook_url || item.twitter_url || item.linkedin_url || item.galeria) {

              html += '<div class="social-gallery bytedesign-col">';

              // Verificar si existe al menos un enlace de red social
              if (item.instagram_url || item.facebook_url || item.twitter_url || item.linkedin_url) {
                  html += '<div class="redes_sociales">';

                  if (item.instagram_url) {
                      html += '<a href="' + item.instagram_url + '"><i class="fab fa-instagram"></i></a>';
                  }

                  if (item.facebook_url) {
                      html += '<a href="' + item.facebook_url + '"><i class="fa fa-facebook"></i></a>';
                  }

                  if (item.twitter_url) {
                      html += '<a href="' + item.twitter_url + '"><i class="fab fa-twitter"></i></a>';
                  }

                  if (item.linkedin_url) {
                      html += '<a href="' + item.linkedin_url + '"><i class="fab fa-linkedin"></i></a>';
                  }

                  html += '</div>';
              }
              if (item.galeria) {
                  console.log("Galería", item.galeria);

                  // Generar el contenido HTML de las imágenes
                  var galeria = item.galeria;
                  html += '<a class="openPopup" href="#">Abrir Galería</a>';
                  html += '<div class="popup-container popup-container-scroll">';
                  html += '<div class="popup-content">';
                  html += '<div class="gallery row row-cols-1 row-cols-sm-2 row-cols-md-3">';

                  for (var i = 0; i < galeria.length; i++) {
                      var urlImagen = galeria[i].url;

                      var imagenHTML = '<div class="col gallery-item">';
                      imagenHTML += '<img src="' + urlImagen + '" alt="Imagen" class="img-fluid img-popup-galeria">';
                      imagenHTML += '</div>';

                      html += imagenHTML;
                  }

                  html += '</div>'; // Cierra el div.gallery
                  html += '</div>'; // Cierra el div.popup-content
                  html += '<button class="closePopup">Cerrar</button>'; // Agregar botón de cierre al popup
                  html += '</div>'; // Cierra el div.popup-container



                  // Agregar el contenido HTML al contenedor adecuado en tu página
                  /*$('#container').remove(); // Eliminar el contenedor existente si existe
                  $('body').append(html); // Agregar el nuevo contenedor al final del body*/

                  // Agregar evento de clic al botón "Abrir Galería"
                  $(document).on('click', '.openPopup', function (e) {
                      e.preventDefault();
                      $(this).siblings('.popup-container').fadeIn(); // Mostrar el popup utilizando fadeIn()
                  });

                  // Agregar evento de clic al botón de cierre
                  $(document).on('click', '.closePopup', function (e) {
                      e.preventDefault();
                      $(this).closest('.popup-container').fadeOut(); // Ocultar el popup utilizando fadeOut()
                  });

                  // Cerrar el popup al hacer clic fuera de él
                  $(document).on('click', function (e) {
                      if (!$(e.target).closest('.popup-container').length && !$(e.target).hasClass('openPopup')) {
                          $('.popup-container').fadeOut(); // Ocultar el popup si se hace clic fuera de él
                      }
                  });
              }

              html += '</div>' // cierre de columna redes sociales botón galería
          }


          html += '</div>';
      });

      // Mostrar otros negocios con la clase "negocio"
      $.each(negociosNoDestacados, function (index, item) {
          html += '<div class="negocio">' +
              '<h2>' + item.titulo + '</h2>';

          if (item.tipo_negocio) {
              html += '<p>Sector: ' + item.tipo_negocio.join(', ') + '</p>';
          }

          if (item.ubicacion) {
              html += '<p>Ubicación: ' + item.ubicacion + '</p>';
          }

          if (item.telefono) {
              html += '<p>Teléfono: ' + item.telefono + '</p>';
          }

          html += '</div>';
      });

      $('#resultado-filtro').html(html);
  }


  $('#limpiar-filtro').on('click', function (e) {
      e.preventDefault();

      // Ejecuta la función para mostrar todos los negocios
      mostrarTodosNegocios();
  });


  $('#filtrar').on('click', function (e) {
      console.log("filtro");
      e.preventDefault();

      // Ejecuta la función para filtrar los negocios
      filtrarNegocios();
  });

  // Ejecuta la función para mostrar todos los negocios al cargar la página
  mostrarTodosNegocios();

  function mostrarTodosNegocios() {
      var dataDestacados = {
          action: 'mostrar_negocios_destacados'
      };

      var dataNoDestacados = {
          action: 'mostrar_negocios_no_destacados'
      };

      $.when(
          $.ajax({
              url: ajax_object.ajaxurl,
              type: 'POST',
              data: dataDestacados
          }),
          $.ajax({
              url: ajax_object.ajaxurl,
              type: 'POST',
              data: dataNoDestacados
          })
      ).done(function (responseDestacados, responseNoDestacados) {
          if (responseDestacados[0].success && responseNoDestacados[0].success) {
              var negociosDestacados = responseDestacados[0].data;
              var negociosNoDestacados = responseNoDestacados[0].data;

              console.log("Destacados: ", negociosDestacados);
              console.log("No Destacados: ", negociosNoDestacados);



              var html = '';

              // Mostrar negocios destacados con la clase "destacado"
              $.each(negociosDestacados, function (index, item) {
                  html += '<div class="negocio destacado">';

                  if (item.logo) {
                      html += '<div class="logo-div bytedesign-col"><img class="logo" src="' + item.logo + '"></div>';
                  }

                  if (item.titulo || item.contenido || item.tipo_negocio || item.ubicacion) {

                      html += '<div class="contenido bytedesign-col"><h2>' + item.titulo + '</h2>' +
                          '<p>' + item.contenido + '</p>';

                      if (item.tipo_negocio) {
                          html += '<p><i class="fas fa-industry"></i> Sector: ' + item.tipo_negocio.join(', ') + '</p>';
                      }

                      if (item.ubicacion) {
                          html += '<p><i class="fas fa-map-marker-alt"></i> Ubicación: ' + item.ubicacion + '</p>';
                      }

                      if (item.telefono) {
                          html += '<p><i class="fas fa-phone"></i> Teléfono: ' + item.telefono + '</p>';
                      }

                      if (item.web_site) {
                          html += '<div class="website"><p><i class="fas fa-globe"></i> Sitio web: </p><a href="' + item.web_site + '">' + item.web_site + '</a></div>';
                      }

                      html += '</div>'; // Cierre de columna contenido
                  }


                  if (item.instagram_url || item.facebook_url || item.twitter_url || item.linkedin_url || item.galeria) {

                      html += '<div class="social-gallery bytedesign-col">';

                      // Verificar si existe al menos un enlace de red social
                      if (item.instagram_url || item.facebook_url || item.twitter_url || item.linkedin_url) {
                          html += '<div class="redes_sociales">';

                          if (item.instagram_url) {
                              html += '<a href="' + item.instagram_url + '"><i class="fab fa-instagram"></i></a>';
                          }

                          if (item.facebook_url) {
                              html += '<a href="' + item.facebook_url + '"><i class="fa fa-facebook"></i></a>';
                          }

                          if (item.twitter_url) {
                              html += '<a href="' + item.twitter_url + '"><i class="fab fa-twitter"></i></a>';
                          }

                          if (item.linkedin_url) {
                              html += '<a href="' + item.linkedin_url + '"><i class="fab fa-linkedin"></i></a>';
                          }

                          html += '</div>';
                      }
                      if (item.galeria) {
                          console.log("Galería", item.galeria);

                          // Generar el contenido HTML de las imágenes
                          var galeria = item.galeria;
                          html += '<a class="openPopup" href="#">Abrir Galería</a>';
                          html += '<div class="popup-container popup-container-scroll">';
                          html += '<div class="popup-content">';
                          html += '<div class="gallery row row-cols-1 row-cols-sm-2 row-cols-md-3">';

                          for (var i = 0; i < galeria.length; i++) {
                              var urlImagen = galeria[i].url;

                              var imagenHTML = '<div class="col gallery-item">';
                              imagenHTML += '<img src="' + urlImagen + '" alt="Imagen" class="img-fluid img-popup-galeria">';
                              imagenHTML += '</div>';

                              html += imagenHTML;
                          }

                          html += '</div>'; // Cierra el div.gallery
                          html += '</div>'; // Cierra el div.popup-content
                          html += '<button class="closePopup">Cerrar</button>'; // Agregar botón de cierre al popup
                          html += '</div>'; // Cierra el div.popup-container



                          // Agregar el contenido HTML al contenedor adecuado en tu página
                          /*$('#container').remove(); // Eliminar el contenedor existente si existe
                          $('body').append(html); // Agregar el nuevo contenedor al final del body*/

                          // Agregar evento de clic al botón "Abrir Galería"
                          $(document).on('click', '.openPopup', function (e) {
                              e.preventDefault();
                              $(this).siblings('.popup-container').fadeIn(); // Mostrar el popup utilizando fadeIn()
                          });

                          // Agregar evento de clic al botón de cierre
                          $(document).on('click', '.closePopup', function (e) {
                              e.preventDefault();
                              $(this).closest('.popup-container').fadeOut(); // Ocultar el popup utilizando fadeOut()
                          });

                          // Cerrar el popup al hacer clic fuera de él
                          $(document).on('click', function (e) {
                              if (!$(e.target).closest('.popup-container').length && !$(e.target).hasClass('openPopup')) {
                                  $('.popup-container').fadeOut(); // Ocultar el popup si se hace clic fuera de él
                              }
                          });
                      }

                      html += '</div>' // cierre de columna redes sociales botón galería
                  }


                  html += '</div>';
              });

              // Mostrar otros negocios con la clase "negocio"
              $.each(negociosNoDestacados, function (index, item) {
                  html += '<div class="negocio">';
                  html += '<div class="contenido bytedesign-col"><h2>' + item.titulo + '</h2>';

                  if (item.tipo_negocio) {
                      html += '<p><i class="fas fa-industry"></i> Sector: ' + item.tipo_negocio.join(', ') + '</p>';
                  }

                  if (item.ubicacion) {
                      html += '<p><i class="fas fa-map-marker-alt"></i> Ubicación: ' + item.ubicacion + '</p>';
                  }

                  if (item.telefono) {
                      html += '<p><i class="fas fa-phone"></i> Teléfono: ' + item.telefono + '</p>';
                  }


                  html += '</div>';
                  html += '</div>';
              });

              $('#resultado-filtro').html(html);
          } else {
              $('#resultado-filtro').html('No se encontraron negocios.');
          }
      }).fail(function () {
          $('#resultado-filtro').html('Error en la solicitud AJAX');
      });
  }

  // Ejecuta la función para mostrar todos los negocios al cargar la página
  mostrarTodosNegocios();

}); 