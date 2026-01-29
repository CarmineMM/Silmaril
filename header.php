<?php

/**
 * The header for our theme
 *
 * This is the template that displays all the <head> section and everything up until <div id="app">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Silmaril
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="author" href="<?= get_theme_file_uri('humans.txt') ?>">
    <!-- <meta name="theme-color" content="#ffa100"> -->

    <?php wp_head() ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>