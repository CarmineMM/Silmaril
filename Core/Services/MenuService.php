<?php

namespace Silmaril\Core\Services;

use Silmaril\Core\Foundation\Service;

class MenuService extends Service
{
    /**
     * Inicializar servicio
     */
    public function init(): void {}

    /**
     * Obtener todos los menús
     */
    public function getAllMenus(): array
    {
        $menus = [];
        $locations = \get_nav_menu_locations();
        $registered_menus = \get_registered_nav_menus();

        foreach ($registered_menus as $location => $description) {
            $menu_id = $locations[$location] ?? 0;

            $menus[$location] = [
                'location' => $location,
                'description' => $description,
                'menu_id' => $menu_id,
                'items' => $menu_id ? $this->getMenuItems($menu_id) : [],
            ];
        }

        return $menus;
    }

    /**
     * Obtener menú por ubicación
     */
    public function getMenuByLocation(string $location): array
    {
        $locations = \get_nav_menu_locations();
        $menu_id = $locations[$location] ?? 0;

        if (!$menu_id) {
            return [];
        }

        return $this->getMenuItems($menu_id);
    }

    /**
     * Obtener items de un menú
     */
    protected function getMenuItems(int $menu_id): array
    {
        $items = \wp_get_nav_menu_items($menu_id);

        if (!$items) {
            return [];
        }

        return $this->buildMenuTree($items);
    }

    /**
     * Construir árbol de menú
     */
    protected function buildMenuTree($items): array
    {
        $tree = [];
        $items_by_id = [];

        // Indexar items
        foreach ($items as $item) {
            $items_by_id[$item->ID] = $this->formatMenuItem($item);
        }

        // Construir árbol
        foreach ($items as $item) {
            if ($item->menu_item_parent == 0) {
                $tree[] = $items_by_id[$item->ID];
            } else {
                $parent = $items_by_id[$item->menu_item_parent] ?? null;
                if ($parent) {
                    $items_by_id[$item->menu_item_parent]['children'][] = $items_by_id[$item->ID];
                }
            }
        }

        return $tree;
    }

    /**
     * Formatear item de menú
     */
    protected function formatMenuItem($item): array
    {
        $object_id = \get_post_meta($item->ID, '_menu_item_object_id', true);

        return [
            'id' => $item->ID,
            'title' => $item->title,
            'url' => $item->url,
            'slug' => $item->slug ?? sanitize_title($item->title),
            'target' => $item->target ?: '_self',
            'classes' => array_filter($item->classes),
            'xfn' => $item->xfn,
            'description' => $item->description,
            'parent' => (int) $item->menu_item_parent,
            'order' => $item->menu_order,
            'object_id' => (int) $object_id,
            'object_type' => $item->object,
            'children' => [],
            // Campos adicionales útiles
            'is_external' => strpos($item->url, \get_site_url()) === false,
            'has_children' => false, // Se actualizará después
        ];
    }
}
