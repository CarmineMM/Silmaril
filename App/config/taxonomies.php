<?php

return [
    'categories' => [
        'post_types' => ['post'],
        'args' => [
            'hierarchical' => true,
            'labels' => [
                'name' => __('Categories', 'taxonomy general name'),
                'singular_name' => __('Category', 'taxonomy singular name'),
                'search_items' => __('Search Categories', 'silmaril'),
                'all_items' => __('All Categories', 'silmaril'),
                'parent_item' => __('Parent Category', 'silmaril'),
                'parent_item_colon' => __('Parent Category:', 'silmaril'),
                'edit_item' => __('Edit Category', 'silmaril'),
                'update_item' => __('Update Category', 'silmaril'),
                'add_new_item' => __('Add New Category', 'silmaril'),
                'new_item_name' => __('New Category Name', 'silmaril'),
                'menu_name' => __('Categories', 'silmaril'),
            ],
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'category'],
        ],
    ],
];
