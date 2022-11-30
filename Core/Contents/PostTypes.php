<?php

namespace Silmaril\Core\Contents;

use Silmaril\Core\Support\Collection;
use Silmaril\Core\Support\Configs;
use Silmaril\Core\Support\Str;

class PostTypes extends Contents
{
    /**
     * Generar los fields para los post types
     *
     * @param array $names
     * @param array $labels
     * @param array $arg
     * @param string $genderName
     * @return Collection
     * @throws \Exception
     */
    public function fields(array $names, array $labels = [], array $arg = [], string $genderName = 'o'): Collection
    {
        // Nombres pasados en Singular y (opcional) plural
        $names = $this->expectedNames($names);

        $labelsCollection = new Collection([
            // Nombre general para el tipo de publicación, generalmente plural. Lo mismo y anulado por.
            // El valor predeterminado es 'Publicaciones' / 'Páginas'
            'name' => __($names->plural, TEXT_DOMAIN),

            // Nombre de un objeto de este tipo de entrada. El valor predeterminado es 'Post' / 'Page'.
            'singular_name' => __($names->singular, TEXT_DOMAIN),

            // El valor predeterminado es 'Agregar nuevo' para los tipos jerárquicos y no jerárquicos.
            // Al internacionalizar esta cadena, utilice uncontexto gettextque coincida con su tipo de publicación.
            'add_new' =>  __("Agregar nuev{$genderName}", TEXT_DOMAIN),

            // Etiqueta para añadir un nuevo elemento singular.
            // El valor predeterminado es 'Agregar nueva publicación' / 'Agregar nueva página'.
            'add_new_item' =>  __("Agregar nuev{$genderName} {$names->singular}", TEXT_DOMAIN),

            // Etiqueta para editar un elemento singular. El valor predeterminado es 'Editar publicación' / 'Editar página'.
            'edit_item' => __("Editar {$names->singular}", TEXT_DOMAIN),

            // Etiqueta para el nuevo título de la página del artículo. El valor predeterminado es 'Nueva publicación' / 'Nueva página'.
            'new_item' => __("Nuev{$genderName} {$names->singular}", TEXT_DOMAIN),

            // Etiqueta para ver un elemento singular. El valor predeterminado es 'Ver publicación' / 'Ver página'.
            'view_item' => __("Ver {$names->singular}", TEXT_DOMAIN),

            // Etiqueta para ver archivos tipo post. El valor predeterminado es 'Ver publicaciones' / 'Ver páginas'.
            'view_items' => __("Ver {$names->plural}", TEXT_DOMAIN),

            // Etiqueta para buscar elementos en plural. El valor predeterminado es 'Buscar publicaciones' / 'Buscar páginas'.
            'search_items' => __("Buscar {$names->plural}", TEXT_DOMAIN),

            // Etiqueta utilizada cuando no se encuentran artículos. El valor predeterminado es 'No se encontraron publicaciones' / 'No se encontraron páginas'.
            'not_found' => __("No se encontraron {$names->plural}", TEXT_DOMAIN),

            // Etiqueta utilizada cuando no hay artículos en la Papelera.
            // El valor predeterminado es 'No se han encontrado publicaciones en la papelera' / 'No se han encontrado páginas en la papelera'.
            'not_found_in_trash' => __("No se han encontrado {$names->plural} en la papelera", TEXT_DOMAIN),

            // Etiqueta para indicar todos los elementos en un enlace de submenú.
            // El valor predeterminado es 'Todas las publicaciones' / 'Todas las páginas'.
            'all_items' => __("Tod{$genderName}s l{$genderName}s {$names->plural}", TEXT_DOMAIN),

            // Etiqueta para archivos en menús de navegación. El valor predeterminado es 'Post Archives' / 'Page Archives'.
            'archives' => __("Archivos de {$names->singular}", TEXT_DOMAIN),

            // Etiqueta para el metabox de atributos. El valor predeterminado es 'Atributos de publicación' / 'Atributos de página'.
            'attributes' => __("Atributos de {$names->singular}", TEXT_DOMAIN),

            // Etiqueta para el botón del marco multimedia. El valor predeterminado es 'Insertar en la publicación' / 'Insertar en la página'.
            'insert_into_item' => __("Insertar en l{$genderName} {$names->singular}", TEXT_DOMAIN),

            // Etiqueta para el filtro de trama de medios. El valor predeterminado es 'Subido a esta publicación' / 'Subido a esta página'.
            'uploaded_to_this_item' => __("Subido a esta {$names->singular}", TEXT_DOMAIN),

            // Etiqueta para el título del metabox de la imagen destacada. El valor predeterminado es 'Imagen destacada'.
            'featured_image' => __('Imagen destacada', TEXT_DOMAIN),

            // Etiqueta para configurar la imagen destacada. El valor predeterminado es 'Establecer imagen destacada'.
            'set_featured_image' => __('Establecer imagen destacada', TEXT_DOMAIN),

            // Etiqueta para eliminar la imagen destacada. El valor predeterminado es 'Eliminar imagen destacada'.
            'remove_featured_image' => __('Eliminar imagen destacada', TEXT_DOMAIN),

            // Etiqueta en el marco multimedia para usar una imagen destacada. El valor predeterminado es 'Usar como imagen destacada'.
            'use_featured_image' => __('Usar como imagen destacada', TEXT_DOMAIN),

            // Etiqueta para el nombre del menú. El valor predeterminado es el mismo que.
            'menu_name' => __($names->plural, TEXT_DOMAIN),

            // Etiqueta para el encabezado oculto de las vistas de tabla.
            // El valor predeterminado es 'Filtrar lista de publicaciones' / 'Filtrar lista de páginas'.
            'filter_items_list' => __("Filtrar lista de {$names->plural}", TEXT_DOMAIN),

            // Etiqueta para el filtro de fecha en tablas de lista. El valor predeterminado es 'Filtrar por fecha'.
            'filter_by_date' => __('Filtrar por fecha', TEXT_DOMAIN),

            // Etiqueta para el encabezado oculto de paginación de la tabla.
            // El valor predeterminado es 'Navegación de la lista de publicaciones' / 'Navegación de la lista de páginas'.
            'items_list_navigation' => __("Navegación en la lista de {$names->plural}", TEXT_DOMAIN),

            // Etiqueta para el encabezado oculto de la tabla. El valor predeterminado es 'Lista de publicaciones' / 'Lista de páginas'.
            'items_list' => __("Lista de {$names->plural}", TEXT_DOMAIN),

            // Etiqueta utilizada cuando se publica un artículo. El valor predeterminado es 'Post published.' / 'Página publicada'.
            'item_published' => __("{$names->singular} publicada", TEXT_DOMAIN),

            // Etiqueta utilizada cuando se publica un artículo con visibilidad privada.
            // El valor predeterminado es 'Publicar de forma privada' / 'Página publicada de forma privada'.
            'item_published_privately' => __('Publicar de forma privada', TEXT_DOMAIN),

            // Etiqueta utilizada cuando un artículo se cambia a un borrador.
            // El valor predeterminado es 'Publicación revertida a borrador.' / 'Página revertida a borrador'.
            'item_reverted_to_draft' => __("{$names->plural} revertida a borrador", TEXT_DOMAIN),

            // Etiqueta utilizada cuando un artículo está programado para su publicación.
            // El valor predeterminado es 'Post scheduled.' / 'Página programada'.
            'item_scheduled' => __("{$names->plural} en tareas programadas", TEXT_DOMAIN),

            // Etiqueta utilizada cuando se actualiza un artículo.
            // El valor predeterminado es 'Publicación actualizada' / 'Página actualizada'.
            'item_updated' => __("{$names->singular} actualizada", TEXT_DOMAIN),

            // Título para una variación de bloque de enlace de navegación.
            // El valor predeterminado es 'Publicar enlace' / 'Enlace de página'.
            'item_link' => __("Publicar enlace", TEXT_DOMAIN),

            // Descripción de una variación de bloque de enlace de navegación.
            // El valor predeterminado es 'Un enlace a una publicación' / 'Un enlace a una página'.
            'item_link_description' => __("Un enlace a {$names->singular}", TEXT_DOMAIN),
        ], false);


        $fields = new Collection([
            // Un breve resumen descriptivo de cuál es el tipo de publicación.
            'description' => "{$names->plural} - Descripción",

            // Si un tipo de publicación está destinado a ser utilizado públicamente, ya sea a través de la interfaz de administración o por usuarios front-end.
            // Aunque la configuración predeterminada de $exclude_from_search, $publicly_queryable, $show_ui y $show_in_nav_menus se heredan de $public,
            // cada uno no se basa en esta relación y controla una intención muy específica.
            // Valor predeterminado 'false'.
            'public' => true,

            // Si el tipo de publicación es jerárquico (por ejemplo, página). Valor predeterminado 'false'.
            // (false: Entrada, true: Pagina)
            'hierarchical' => true,

            // Si se deben excluir publicaciones con este tipo de publicación de los resultados de búsqueda front-end.
            // El valor predeterminado es el valor opuesto de $public.
            'exclude_from_search' => false,

            // Si se pueden realizar consultas en el front-end para el tipo de publicación como parte de parse_request().
            // Los extremos incluirían: * ?post_type={post_type_key} * ? {post_type_key}={single_post_slug} * ? {post_type_query_var}={single_post_slug}
            // Si no se establece, el valor predeterminado se hereda de $public.
            'publicly_queryable' => true,

            // Si desea generar y permitir una interfaz de usuario para administrar este tipo de publicación en el administrador.
            // El valor predeterminado es el valor de $public.
            'show_ui' => true,

            // Dónde mostrar el tipo de publicación en el menú de administración.
            // Para funcionar, $show_ui debe ser true. Si es true, el tipo de publicación se muestra en su propio menú de nivel superior.
            // Si es false, no se muestra ningún menú. Si es una cadena de un menú de nivel superior existente (o, por ejemplo), el tipo de publicación se colocará como un submenú de eso.
            // El valor predeterminado es el valor de $show_ui.'tools.php''edit.php?post_type=page'
            'show_in_menu' => true,

            // Hace que este tipo de entrada esté disponible para su selección en los menús de navegación.
            // El valor predeterminado es el valor de $public.
            'show_in_nav_menus' => true,

            // Hace que este tipo de publicación esté disponible a través de la barra de administración.
            // El valor predeterminado es $show_in_menu.
            'show_in_admin_bar' => true,

            // Si se debe incluir el tipo de publicación en la API de REST.
            // Establezca esto en true para que el tipo de publicación esté disponible en el editor de bloques.
            'show_in_rest' => true,

            // Para cambiar la dirección URL base de la ruta de la API de REST. El valor predeterminado es $post_type.
            //'rest_base' => $names->plural,

            // Para cambiar la dirección URL del espacio de nombres de la ruta de la API de REST. El valor predeterminado es wp/v2.
            'rest_namespace' => ' wp/v2',

            // Nombre de clase del controlador de API de REST. El valor predeterminado es 'WP_REST_Posts_Controller'.
            // @see https://developer.wordpress.org/reference/classes/wp_rest_posts_controller/
            // 'rest_controller_class' => '',

            // La posición en el orden del menú debe aparecer el tipo de publicación.
            // Para funcionar, $show_in_menu debe ser true. Valor predeterminado null (en la parte inferior).
            'menu_position' => 10,

            // La dirección URL del icono que se va a utilizar para este menú. Pase un SVG codificado en base64 usando un URI de datos,
            // que se coloreará para que coincida con el esquema de color, esto debería comenzar con.
            'menu_icon' => 'dashicons-dashboard',

            // La cadena que se va a usar para crear las capacidades de lectura, edición y eliminación.
            // Puede pasarse como una matriz para permitir plurales alternativos cuando se usa este argumento como base para construir las capacidades, por ejemplo,
            // Predeterminado.'story''stories''post'
            //'capability_type' => ['story', 'stories', 'post'],

            // Conjunto de capacidades para este tipo de publicación. $capability_type se utiliza como base para construir capacidades de forma predeterminada.
            // @see https://developer.wordpress.org/reference/functions/get_post_type_capabilities/
            'capabilities' => [],

            // Si se va a utilizar el control de capabilities predeterminado interno.
            // Valor predeterminado 'false'.
            //'map_meta_cap' => false,

            // Características principales que admite el tipo de publicación. Sirve como alias para llamar aadd_post_type_support()directamente.
            // @see https://developer.wordpress.org/reference/functions/add_post_type_support/
            'supports' => [
                'title',
                'editor',
                'comments',
                //'revisions',
                'trackbacks',
                'author',
                'excerpt',
                'page-attributes',
                'thumbnail',
                'custom-fields',
                'post-formats'
            ],

            // Proporcione una función de devolución de llamada que configure los metabox para el formulario de edición.
            // Haga remove_meta_box() y add_meta_box()llamadas en la devolución de llamada. Valor predeterminado nulo.
            // @see https://developer.wordpress.org/reference/functions/remove_meta_box/
            // @see https://developer.wordpress.org/reference/functions/add_meta_box/
            // 'register_meta_box_cb' => '',

            // Una matriz de identificadores de taxonomía que se registrarán para el tipo de publicación.
            // Las taxonomías se pueden registrar más tarde con register_taxonomy() o register_taxonomy_for_object_type().
            'taxonomies' => [],

            // Si debe haber archivos de tipo post, o si es una cadena, el archivo slug para usar.
            // Generará las reglas de reescritura adecuadas si $rewrite está habilitado. Valor predeterminado 'false'.
            'has_archive' => false,

            // El valor predeterminado es true, utilizando $post_type como slug.
            // Para especificar reglas de reescritura, se puede pasar una matriz con cualquiera de estas claves:
            'rewrite' => [
                'slug' => (new Str($names->plural))->toSlug(),
            ],

            // Establece la tecla query_var para este tipo de publicación. El valor predeterminado es $post_type key.
            // Si es false, no se puede cargar un tipo de publicación en ? {query_var}={post_slug}.
            // Si se especifica como una cadena, la consulta ? {query_var_string}={post_slug} será válido.
            // 'query_var' =>

            // Si se deben eliminar publicaciones de este tipo al eliminar un usuario.
            //'delete_with_user' => null,
        ], false);
        $fields->combine($arg);

        // Post de tipo 'Pagina'
        if ( $fields->get('hierarchical') ) {
            $labelsCollection->combine([
                // Etiqueta utilizada para prefijar los padres de los elementos jerárquicos.
                // No se utiliza en tipos de puestos no jerárquicos. El valor predeterminado es 'Página principal:'.
                'parent_item_colon' => __("{$names->singular} principal:", TEXT_DOMAIN),
            ]);
        }
        // Post de tipo 'Entrada'
        else {
            $labelsCollection->combine([]);
        }

        // Agregar los labels al field
        $fields->combine([
            'labels' => $labelsCollection->combine($labels)->toArray(),
        ]);

        return $fields;
    }

	/**
	 * Registrar los Post Types
	 *
	 * @return void
	 * @throws \Exception
	 */
	public static function register(): void
	{
		$self = new self;

		foreach (Configs::get('post_types') as $post_type) {
			$fields = $self->fields(
				$post_type['names'],
				$post_type['labels'] ?? [],
				$post_type['args'] ?? [],
				$post_type['gender_name'] ?? '0'
			);

			register_post_type($post_type['post_type'], $fields->toArray());
		}
	}
}