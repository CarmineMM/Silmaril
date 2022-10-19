<?php

namespace Silmaril\Core;

use Silmaril\Core\Security\Checker;
use Silmaril\Core\Support\Configs;

/**
 * Arranque inicial del tema
 *
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
class Foundation
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		if ( !defined('TEXT_DOMAIN') && Checker::checkAuthor() ) {
			define('TEXT_DOMAIN', wp_get_theme()->get('TextDomain'));
		}
	}

	/**
	 * Carga las configuraciones del tema
	 *
	 * @return Foundation
	 */
	public function loadConfigs(): static
	{
		if ( !defined('DIRECTORY_SEPARATOR') ) {
			define('DIRECTORY_SEPARATOR', '/');
		}

		Config::setAllConfigs();

		return $this;
	}

	/**
	 * Cargar las actions presentes
	 *
	 * @return $this
	 */
	public function loadActions(): static
	{
		$actions = Configs::get('actions');

		foreach ($actions as $exec)
		{
			if ( !isset($exec['call']) || !isset($exec['action']) ) {
				continue;
			}

			add_action(
				$exec['action'],
				$exec['call'],
				$exec['priority'] ?? 10,
				$exec['args'] ?? 1
			);
		}

		return $this;
	}

	/**
	 * Carga los filtros
	 *
	 * @return $this
	 */
	public function loadFilters(): static
	{
		foreach (Configs::get('filters') as $filter) {
			if ( !isset($filter['filter']) || !isset($filter['call']) ) {
				continue;
			}

			add_filter(
				$filter['filter'],
				$filter['call'],
				$filter['priority'] ?? 10,
				$filter['args'] ?? 1,
			);
		}

		return $this;
	}

	/**
	 * Hace el cargado de Widgets
	 *
	 * @return $this
	 */
	public function loadWidgets(): static
	{
		if ( count(Configs::get('sidebars')) > 0 ) {
			Configs::add('actions', [
				[
					'action'   => 'widgets_init',
					'call'     => [Foundation::class, 'registerSidebars'],
					'priority' => 10,
					'args'     => 0,
				],
			]);
		}

		return $this;
	}

	/**
	 * Registra los sidebar, mediante el Hook de Wordpress.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_sidebar/
	 * @return void
	 */
	public static function registerSidebars(): void
	{
		foreach (Configs::get('sidebars') as $sidebar) {
			register_sidebar([
				'id'             => $sidebar['id'],
				'name'           => $sidebar['name'],
				'description'    => $sidebar['description'] ?? '',
				'class'          => $sidebar['class'] ?? '',
				'before_widget'  => $sidebar['before_widget'] ?? '<li id="%1$s" class="widget %2$s">',
				'after_widget'   => $sidebar['after_widget'] ?? "</li>",
				'before_title'   => $sidebar['before_title'] ?? '<h2 class="widgettitle">',
				'after_title'    => $sidebar['after_title'] ?? "</h2>",
				'before_sidebar' => $sidebar['before_sidebar'] ?? '',
				'after_sidebar'  => $sidebar['after_sidebar'] ?? '',
				'show_in_rest'   => $sidebar['show_in_rest'] ?? false,
			]);
		}
	}
}