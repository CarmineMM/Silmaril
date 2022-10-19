<?php

use Silmaril\Core\PrimaryMenu;
?>

<nav class="navbar <?= $data->get('class') ?>">
    <div class="container">
        <!-- Logo -->
        <?= \Silmaril\Core\Support\Frontend::logo( $data->get('logo-attr') ) ?>

        <!-- Responsive Button -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menu-<?= PrimaryMenu::name ?>"
            aria-controls="menu-<?= PrimaryMenu::name ?>"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <?php PrimaryMenu::get(); ?>
    </div>
</nav>
