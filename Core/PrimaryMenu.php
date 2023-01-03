<?php

namespace Silmaril\Core;

use Silmaril\Core\Support\Collection;
use Silmaril\Core\Support\Configs;

/**
 * Menu principal, que trabaja con Bootstrap.
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
class PrimaryMenu
{
	/**
	 * Nombre del registro
	 */
	const name = 'primary';

	/**
	 * Registrar menu
	 *
	 * @return void
	 */
	public static function register(): void
	{
		register_nav_menus([
			self::name => __('Menú principal', TEXT_DOMAIN),
		]);
	}

	/**
	 * Registrar filtros
	 *
	 * @return void
	 */
	public static function registerFilters(): void
	{
		// Agregar filtros
		Configs::add('filters', [
			[
				/**
				 * Filtra las clases CSS aplicadas al elemento de lista de un elemento de menú.
				 *
				 * @see https://developer.wordpress.org/reference/hooks/nav_menu_css_class/
				 */
				'filter'   => 'nav_menu_css_class',
				'call'     => [self::class, 'liCssClass'],
				'priority' => 10,
				'args'     => 3,
			],
			[
				/**
				 * Filtra los atributos HTML aplicados al elemento de anclaje de un elemento de menú.
				 *
				 * @see https://developer.wordpress.org/reference/hooks/nav_menu_link_attributes/
				 */
				'filter'   => 'nav_menu_link_attributes',
				'call'     => [self::class, 'aCssClass'],
				'priority' => 10,
				'args'     => 3,
			],
			[
				/**
				 * Filtra las clases CSS aplicadas a un elemento de lista de menús.
				 *
				 * @see https://developer.wordpress.org/reference/hooks/nav_menu_submenu_css_class/
				 */
				'filter'   => 'nav_menu_submenu_css_class',
				'call'     => [self::class, 'submenuCssClass'],
				'priority' => 10,
				'args'     => 2,
			],
		]);
	}

	/**
	 * Obtiene el menu principal.
	 * Consulte las configuraciones posibles que se pueden pasar
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_nav_menu/
	 * @param array $attr
	 * @return void
	 */
	public static function get(array $attr = []): void
	{
		if ( !has_nav_menu(self::name) ) {
			return;
		}

		$data = new Collection([
			'container_class' => 'collapse navbar-collapse',
			'container_id'    => 'menu-'.self::name,
			'menu_class'      => 'navbar-nav ms-auto align-items-lg-center',
		]);

		$data->combine($attr)->combine([
			'theme_location' => self::name,
		]);

		wp_nav_menu($data->toArray());
	}

	/**
	 * Usar el menu primario
	 *
	 * @param array $params
	 * @return string
	 */
	public static function use(array $params = []): string
	{
		$data = new Collection([
			'class'     => 'navbar-expand-md navbar-light',
			'logo-attr' => [],
			'template'  => 'primary-menu',
		]);

		$data->combine($params);

		return (new Template())
			->renderElse($data->get('template'), 'Core.templates.primary-menu', compact('data'));
	}

	/**
	 * CSS de los <li> del menu
	 *
	 * @param $items
	 * @param $menu
	 * @param $args
	 *
	 * @return array
	 */
	public static function liCssClass($items, $menu, $args): array
	{
		// Aplicar exclusivamente al menu actual
		if ( $args->theme_location !== self::name ) {
			return $items;
		}

		$items['class'] = 'nav-item';

		// Css para los submenus
		if ( in_array( 'menu-item-has-children', $items ) ) {
			$items['class'] .= ' dropdown';
		}

		return $items;
	}

	/**
	 * CSS de los elementos <a> del menu
	 *
	 * @param $items
	 * @param $menu
	 * @param $args
	 * @return array
	 */
	public static function aCssClass($items, $menu, $args): array
	{
		// Aplicar exclusivamente al menu actual
		if ( $args->theme_location !== self::name ) {
			return $items;
		}

		$items['class'] = $menu->menu_item_parent == 0
			? 'nav-link'       // Menus
			: 'dropdown-item'; // Sub menu

		// Marcar Current Page
		if ( in_array('current_page_item', $menu->classes) ) {
			$items['class'] .= ' active';
			$items['aria-current'] = 'page';
		}

		// Atributos para los submenu
		if ( in_array('menu-item-has-children', $menu->classes) ) {
			$items['data-bs-toggle'] = 'dropdown';
			$items['aria-expanded']  = 'false';
			$items['class'] .= ' dropdown-toggle';
			$items['role']  = 'button';
			$items['id']    = "dropdown-item-{$menu->object_id}";
		}

		return $items;
	}

	/**
	 * CSS para los Submenus
	 *
	 * @param $items
	 * @param $menu
	 * @return array
	 */
	public static function submenuCssClass($items, $menu): array
	{
		if($menu->theme_location !== self::name) {
			return $items;
		}

		$items['class'] = 'dropdown-menu';

		return $items;
	}
}