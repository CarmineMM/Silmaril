<?php

namespace Silmaril\Core\Contents;

use Silmaril\Core\Support\Collection;
use Silmaril\Core\Support\Configs;
use Silmaril\Core\Support\Str;

class Taxonomies extends Contents
{
    /**
     * Campos para las taxonomies
     *
     * @param array $names
     * @param array $labels
     * @param array $arg
     * @param string $genderName
     * @return Collection
     * @throws \Exception
     * @see https://developer.wordpress.org/reference/functions/register_taxonomy/#user-contributed-notes
     */
    public function fields(array $names, array $labels = [], array $arg = [], string $genderName = 'o'): Collection
    {
        $names = $this->expectedNames($names);

        // Fields
        $labelsCollection = new Collection([
            // Nombre de la taxonomía en plural. Para el ejemplo, «Carrocerías» / «Bodyworks»
            'name' => __($names->plural, TEXT_DOMAIN),

            // Igual que el anterior, pero en singular («Carrocería» / «Bodywork»)
            'singular_name' => __($names->singular, TEXT_DOMAIN),

            // Será el nombre que mostrará en el menú lateral de WordPress, dentro del backoffice («Carrocerías» / «Bodyworks»)
            'menu_name' => __($names->singular, TEXT_DOMAIN),

            // Este texto aparecerá en cualquier lugar del backoffice en el que se haga referencia a todos los elementos de la taxonomía («Todas las carrocerías» / «All Bodyworks»).
            'all_items' => __("Tod{$genderName}s l{$genderName}s {$names->plural}", TEXT_DOMAIN),

            // Igual que el anterior, pero para editar un elemento («Editar carrocería» / «Edit Bodywork»).
            'edit_item' => __("Editar {$names->singular}", TEXT_DOMAIN),

            // Texto que se mostrará en los enlaces para ver la taxonomía («Ver carrocería» / «View Bodywork»).
            'view_item' => __("Ver {$names->singular}", TEXT_DOMAIN),

            //  Si «all» era para todos, «edit» para editar y «view» para ver… ¿Para qué puede ser «update»? Exacto, para la etiqueta de actualizar el elemento («Actualizar carrocería» / «Update bodywork»).
            'update_item' => __("Actualizar {$names->singular}", TEXT_DOMAIN),

            // Será el texto que aparecerá para indicar que puedes crear un nuevo elemento («Nueva carrocería» / «New Bodywork»).
            'add_new_item' => __("Añadir nuev{$genderName} {$names->singular}", TEXT_DOMAIN),

            // Otra etiqueta, que por su nombre, es muy fácil de identificar.
            // Se mostrará junto al campo en el que el usuario tenga que introducir el nombre del elemento («Nombre de la nueva carrocería» / «New Bodywork Name»).
            'new_item_name' => __("Nuev{$genderName} {$names->singular}", TEXT_DOMAIN),

            //  Es el texto que aparece junto al campo para buscar elementos de esa taxonomía («Buscar carrorecías» / «Search Bodyworks»).
            'search_items' => __("Buscar {$names->plural}", TEXT_DOMAIN),

            // Es el texto que indica al usuario, dentro de la página para crear un contenido del tipo al que pertenece esta taxonomía (recuerda que en el ejemplo era «coches»),
            // que si quiere crear varias, debe separarlas por comas («Separa las carrocerías por comas» / «Separate Bodyworks with
            'separate_items_with_commas' => __("Separar l{$genderName}s {$names->plural} por comas", TEXT_DOMAIN),

            //  Se trata del texto que aparecerá cuando se intente buscar una taxonomía de este tipo por su nombre y no haya ninguna coincidencia
            // («No se ha encontrado ninguna carrocería» / «Bodywork not found»).
            'not_found' => __("No se ha encontrado ningun{$genderName} {$names->singular}", TEXT_DOMAIN),

            // El texto mostrado en el enlace que aparece después de actualizar un elemento («Volver a carrocerías» / «Back to Bodyworks»).
            'back_to_items' => __("Volver a l{$genderName}s {$names->plural}", TEXT_DOMAIN),
        ], false);



        // Argumentos
        $argsCollection = new Collection([
            // Con este argumento puedes definir si la taxonomía será visible públicamente,
            // ya sea a través del backoffice de la web o desde el front.
            'public' => true,

            // Este argumento define si la taxonomía puede ser consultada de forma pública.
            'publicly_queryable' => true,

            // Gracias a este argumento, puedes indicar si quieres generar una interfaz de usuario por defecto para la taxonomía o no.
            'show_ui' => true,

            // Con este argumento puedes definir si la taxonomía se mostrará en el menú de tu backoffice,
            // dentro del menú del tipo de contenido al que ha sido asignada, o no.
            'show_in_menu' => true,

            // Este argumento define si la taxonomía puede ser mostrada o no para ser seleccionada en los menús de navegación.
            'show_in_nav_menus' => true,

            // Gracias a este argumento, podrás indicar si quieres que se incluya la taxonomía en la REST API.
            'show_in_rest' => true,

            // Este argumento te permitirá definir la URL base de la REST API para la taxonomía.
            //'rest_base' => (new Str($names->plural))->toSlug(),

            // Con este argumento puedes definir el nombre de la clase para el controlador de la API.
            'rest_controller_class' => '',

            // Este argumento te ayudará a definir si permites que el widget Tag Cloud utilice esta taxonomía o no.
            'show_tagcloud' => false,

            // Gracias a este argumento podrás indicar si quieres que la taxonomía se muestre,
            // en el panel de edición rápida del tipo de contenido al que pertenece.
            'show_in_quick_edit' => true,

            // Con este argumento podrás asociar una función para mostrarla en el metabox de la taxonomía.
            'meta_box_cb' => '',

            //  Este argumento te permitirá definir si quieres que se creen columnas, de forma automática,
            // en la tabla de los tipos de contenidos a los que está asociada la taxonomía.
            'show_admin_column' => true,

            // Con este argumento podrás incluir una descripción de la taxonomía.
            'description' => "$names->singular - descripción",

            // Gracias a este argumento, puedes indicar si quieres que la taxonomía sea del tipo categoría o etiqueta,
            // con solo indicar el valor de verdadero (true) o falso (false), respectivamente. (true: Categoría. false: etiqueta)
            'hierarchical' => true,

            // En este argumento podrás indicar una función que sea llamada cada vez que se actualice el contador de objetos de los tipos de contenidos asociados.
            'update_count_callback' => false,

            // Este argumento permite definir si quieres que se utilice o no una variable con el nombre de la taxonomía en la URL para realizar búsquedas de la misma.
            'query_var' => true,

            // Con este argumento podrás definir el formato de la URL de los tipos de contenidos a los que se asigne un elemento de la taxonomía.
            'rewrite' => [
                'slug' => (new Str($names->get('singular')))->toSlug(),
            ],

            // Gracias a este argumento podrás definir una serie de permisos para la taxonomía.
            'capabilities' => [],
        ], false);

        $argsCollection->combine($arg);

        // Agregar exclusivos para categorías
        if ( $argsCollection->get('hierarchical') ) {
            $labelsCollection->combine([
                // Igual que el anterior, pero el texto aparecerá junto al campo desplegable, con dos puntos («Carrocería padre:» / «Parent Bodywork:»).
                // Al igual que su hermano de arriba, solo para las taxonomías que sean del tipo «categoría».
                'parent_item_colon' => __("{$names->singular} Padre:", TEXT_DOMAIN),

                // Sirve para el texto que aparecerá dentro del campo desplegable con el nombre de otros elementos del mismo tipo,
                // para definir si el que estás creando ahora es hijo del otro. («Carrocería padre» / «Parent Bodywork»). Solo para las taxonomías que sean del tipo «categoría».
                'parent_item' => __("{$names->singular} Padre", TEXT_DOMAIN),
            ]);
        }
        // Agregar exclusivos de etiquetas
        else {
            $labelsCollection->combine([
                // Se trata del texto que aparecerá para definir a los elementos más populares («Carrocerías populares» / «Popular Bodyworks»).
                // Solo para las taxonomías del tipo «etiqueta».
                'popular_items' => __("{$names->plural} populares", TEXT_DOMAIN),

                // Es el texto que indicará al usuario que se pueden añadir o eliminar elementos de la taxonomía («Añadir o eliminar carrocerías» / «Add or Remove Bodyworks»).
                // Este texto también corresponde solamente a las taxonomías del tipo «etiqueta».
                'add_or_remove_items' => __("Agregar o remover {$names->plural}", TEXT_DOMAIN),

                // Este texto aparecerá en el enlace que hay debajo del campo para crear una nueva taxonomía del tipo etiqueta,
                // dentro de la página para crear el contenido («Elige entre las carrocerías más utilizadas» / «Choose from Bodyworks most used»).
                'choose_from_most_used' => __("Elige entre l{$genderName}s {$names->plural} más utilizadas", TEXT_DOMAIN),
            ]);
        }


        // Agregar labels a la collection
        $argsCollection->combine([
            'labels' => $labelsCollection->combine($labels)->toArray(),
        ]);

        return $argsCollection;
    }

    /**
     * Registrar taxonomy
     *
     * @return void
     * @throws \Exception
     */
    public static function register(): void
    {
        $self = new self;

        foreach (Configs::get('taxonomies') as $taxonomy) {
            $fields = $self->fields(
				$taxonomy['names'],
				$taxonomy['labels'] ?? [],
				$taxonomy['args'] ?? [],
				$taxonomy['gender_name'] ?? 'o'
            );

            register_taxonomy($taxonomy['taxonomy'], $taxonomy['object_type'] ?? [], $fields->toArray());
        }
    }
}