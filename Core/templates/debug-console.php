<?php

use Silmaril\Core\Support\Configs;

$debugConsoleLog = \Silmaril\Core\Debug::all();
$allQueues = Configs::get('all-queues');

?>

<div id="debug-console">
    <ol class="items-debug-console">
        <li>
            <h1>Debug Console</h1>
        </li>
        <li>
            <button class="debug-button" data-debug="log">
                Console
                <?php if ( count($debugConsoleLog) > 0 ): ?>
                    <span class="debug-badge">
                        <?= count($debugConsoleLog) ?>
                    </span>
                <?php endif; ?>
            </button>
        </li>
        <li>
            <button class="debug-button" data-debug="actions">Actions</button>
        </li>
        <li>
            <button class="debug-button" data-debug="enqueue">Enqueue</button>
        </li>
        <li>
            <button class="debug-button" data-debug="filters">Filters</button>
        </li>
        <li>
            <button class="debug-button" data-debug="support">Support</button>
        </li>
        <li>
            <button class="debug-button" data-debug="sidebars">Sidebars</button>
        </li>
        <li>
            <button class="debug-button" data-debug="taxonomies">Taxonomies</button>
        </li>
        <li>
            <button class="debug-button" data-debug="post_types">Post Types</button>
        </li>
        <li>
            <button class="toggle-debug-console">
                <span class="dashicons dashicons-arrow-up-alt2"></span>
                <span class="dashicons dashicons-arrow-down-alt2"></span>
            </button>
        </li>
    </ol>

    <section class="content-debug-console">
        <!-- Welcome -->
        <article class="debug-item open" data-item="welcome">
            <h2>Consola por: <b>Carmine Maggio</b></h2>
            <p style="margin-bottom: 1rem"><a href="mailto:carminemaggiom@gmail.com">carminemaggiom@gmail.com</a></p>
            <p style="margin-bottom: 1rem">Los datos y estadísticas presentadas a continuación son solo las acciones y elementos presentes en el tema, mas no de todo Wordpress.</p>
            <table>
                <tr>
                    <th><div>Console</div></th>
                    <td><div>Mensajes internos del tema, o del desarrollo.</div></td>
                </tr>
                <tr>
                    <th><div>Actions</div></th>
                    <td><div>Hooks cargados en la ejecución de wordpress.</div></td>
                </tr>
                <tr>
                    <th><div>Enqueue</div></th>
                    <td><div>Hojas de estilos de scripts que fueron cargados exitosamente en wordpress.</div></td>
                </tr>
                <tr>
                    <th><div>Filters</div></th>
                    <td><div>Filtros activos que se aplican desde el tema.</div></td>
                </tr>
                <tr>
                    <th><div>Supports</div></th>
                    <td><div>Soporte incluido para el tema.</div></td>
                </tr>
                <tr>
                    <th><div>Sidebars</div></th>
                    <td><div>Contenedor de Widgets o laterales al blog.</div></td>
                </tr>
                <tr>
                    <th><div>Taxonomies</div></th>
                    <td><div>Categorización o etiquetado de contenido en los post types.</div></td>
                </tr>
                <tr>
                    <th><div>Post Types</div></th>
                    <td><div>Contenido adicional, estructurado del contenido.</div></td>
                </tr>
            </table>
        </article>

        <!-- Welcome -->
        <article class="debug-item" data-item="log">
            <ul>
                <?php
                    foreach ($debugConsoleLog as $log):
                        $icon = match ($log['type']) {
                            'info'    => 'info',
                            'error'   => 'dismiss',
                            'warning' => 'warning',
                            default   => 'megaphone',
                        };
                ?>
                <li class="log-<?= $log['type'] ?>">
                    <span class="dashicons dashicons-<?= $icon ?>"></span>
                    <p style="margin: 0"><?= $log['msg'] ?></p>
                </li>
                <?php endforeach; ?>
            </ul>
        </article>

        <!-- Actions -->
        <article class="debug-item" data-item="actions">
            <table>
                <thead>
                    <tr>
                        <th>
                            <div>Hook</div>
                        </th>
                        <th>
                            <div>Call / Action</div>
                        </th>
                        <th>
                            <div style="text-align: center">Priority</div>
                        </th>
                        <th>
                            <div style="text-align: center">Params</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (Configs::get('actions') as $action): ?>
                    <tr>
                        <td><div><?= $action['action'] ?></div></td>
                        <td>
                            <div>
                                <?php
                                    echo is_array($action['call'])
                                        ? "{$action['call'][0]}::{$action['call'][1]}"
                                        : $action['call'];
                                ?>
                            </div>
                        </td>
                        <td>
                            <div style="text-align: center"><?= $action['priority'] ?? 10 ?></div>
                        </td>
                        <td>
                            <div style="text-align: center"><?= $action['params'] ?? 1 ?></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </article>

        <!-- Enqueue -->
        <article class="debug-item" data-item="enqueue">
            <table>
                <thead>
                    <tr>
                        <th><?php _e('Nombre', TEXT_DOMAIN); ?></th>
                        <th><?php _e('URL', TEXT_DOMAIN); ?></th>
                        <th><?php _e('Dependencias', TEXT_DOMAIN); ?></th>
                        <th><?php _e('Versión', TEXT_DOMAIN); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach (Configs::get('enqueue') as $type => $enqueue): ?>
                    <!-- Separador -->
                    <tr>
                        <td colspan="4">
                            <div>
                                <h3 style="text-align: center; margin: 0">
                                    <?php
                                        echo match ($type) {
                                             'css' => __('Estilos', TEXT_DOMAIN),
                                             'js'  => __('Scripts', TEXT_DOMAIN),
                                             'admin-css' => __('Estilos (Escritorio de Wordpress)', TEXT_DOMAIN),
                                             'admin-js'  => __('Scripts (Escritorio de Wordpress)', TEXT_DOMAIN),
                                             'debug-css' => __('Estilos (Solo Debug)', TEXT_DOMAIN),
                                             'debug-js'  => __('Scripts (Solo Debug)', TEXT_DOMAIN),
                                             'all-css'   => __('Estilos (Frontal, Escritorio y Debug)', TEXT_DOMAIN),
                                             'all-js'    => __('Scripts (Frontal, Escritorio y Debug)', TEXT_DOMAIN),
                                             default => $type,
                                         }.':';
                                    ?>
                                </h3>
                            </div>
                        </td>
                    </tr>

                    <?php
                    foreach ($enqueue as $name => $script):
                        $key = str_replace(TEXT_DOMAIN.'-', '', $script['key']);
	                    $getQueue = $allQueues[$type][ $key ];
                    ?>
                    <tr>
                        <td>
                            <div>
                                <?= $key ?>
                                <?php if ($script['footer'] ?? false): ?>
                                    <span class="debug-badge">Footer</span>
                                <?php elseif( $script['media'] ?? false ): ?>
                                    <span class="debug-badge" title="Media load <?= $script['media'] ?>"><?= $script['media'] ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div>
                                <?php
                                    if ( is_array($getQueue['url']) ) {
	                                    printf('
                                            <p>Development: <a target="_blank" href="%1$s">%1$s</a></p>
                                            <p>Production: <a target="_blank" href="%2$s">%2$s</a></p>
                                        ',
                                            getUriTheme($getQueue['url'][0]),
                                            getUriTheme($getQueue['url'][1])
                                        );
                                    }
                                    else {
                                        printf('<a target="_blank" href="%1$s">%1$s</a>', $script['url']);
                                    }
                                ?>
                            </div>
                        </td>
                        <td><div><?= implode(', ', $script['deps'] ?? []) ?></div></td>
                        <td><div><?= $script['ver'] ?></div></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </article>

        <!-- Filters -->
        <article class="debug-item" data-item="filters">
            <table>
                <thead>
                <tr>
                    <th>
                        <div>Hook</div>
                    </th>
                    <th>
                        <div>Call / Filter</div>
                    </th>
                    <th>
                        <div style="text-align: center">Priority</div>
                    </th>
                    <th>
                        <div style="text-align: center">Arguments</div>
                    </th>
                </tr>
                </thead>
                <tbody>
		        <?php foreach (Configs::get('filters') as $params): ?>
                    <tr>
                        <td><div><?= $params['filter'] ?></div></td>
                        <td>
                            <div>
						        <?php
						        echo is_array($params['call'])
							        ? "{$params['call'][0]}::{$params['call'][1]}"
							        : $params['call'];
						        ?>
                            </div>
                        </td>
                        <td>
                            <div style="text-align: center"><?= $params['priority'] ?? 10 ?></div>
                        </td>
                        <td>
                            <div style="text-align: center"><?= $params['args'] ?? 1 ?></div>
                        </td>
                    </tr>
		        <?php endforeach; ?>
                </tbody>
            </table>
        </article>

        <!-- Support -->
        <article class="debug-item" data-item="support">
            <table>
                <thead>
                <tr>
                    <th>
                        <div>Support</div>
                    </th>
                    <th>
                        <div>Options</div>
                    </th>
                </tr>
                </thead>
                <tbody>
		        <?php
                    foreach (Configs::get('support') as $support => $options):
                        if ( is_bool($options) && $options === false ) {
                            continue;
                        }
                ?>
                    <tr>
                        <td><div><?= $support ?></div></td>
                        <td>
                            <div><?php echo is_array($options) ? implode(',', $options) : ''; ?></div>
                        </td>
                    </tr>
		        <?php endforeach; ?>
                </tbody>
            </table>
        </article>

        <!-- Sidebars -->
        <article class="debug-item" data-item="sidebars">
            <table>
                <thead>
                <tr>
                    <th>
                        <div>ID</div>
                    </th>
                    <th>
                        <div>Name</div>
                    </th>
                    <th>
                        <div>Description</div>
                    </th>
                </tr>
                </thead>
                <tbody>
		        <?php foreach (Configs::get('sidebars') as $sidebar): ?>
                    <tr>
                        <td>
                            <div><?= $sidebar['id'] ?? '' ?></div>
                        </td>
                        <td>
                            <div><?= $sidebar['name'] ?? '' ?></div>
                        </td>
                        <td>
                            <div><?= $sidebar['description'] ?? '' ?></div>
                        </td>
                    </tr>
		        <?php endforeach; ?>
                </tbody>
            </table>
        </article>

        <!-- Taxonomies -->
        <article class="debug-item" data-item="taxonomies">
            <table>
                <thead>
                    <tr>
                        <th><div>Name</div></th>
                        <th><div>Post types</div></th>
                        <th><div>Names</div></th>
                        <th><div>Description</div></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (Configs::get('taxonomies') as $taxonomy): ?>
                    <tr>
                        <td>
                            <div><?= $taxonomy['taxonomy'] ?></div>
                        </td>
                        <td>
                            <div><?= implode(', ', $taxonomy['object_type'] ?? []) ?></div>
                        </td>
                        <td>
                            <div><?= implode(', ', $taxonomy['names']) ?></div>
                        </td>
                        <td>
                            <div><?= $taxonomy['args']['description'] ?? '...' ?></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </article>

        <!-- Post Types -->
        <article class="debug-item" data-item="post_types">
            <table>
                <thead>
                    <tr>
                        <th><div style="width: 7rem">Post Type</div></th>
                        <th><div>Names</div></th>
                        <th><div>Taxonomies</div></th>
                        <th><div>Description</div></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (Configs::get('post_types') as $post_type): ?>
                    <tr>
                        <td>
                            <div>
                                <span class="dashicons <?= $post_type['args']['menu_icon'] ?? 'dashicons-dashboard' ?>"></span>
	                            <?= $post_type['post_type']; ?>
                            </div>
                        </td>
                        <td>
                            <div><?= implode(', ', $post_type['names']) ?></div>
                        </td>
                        <td>
                            <div><?= implode(', ', $post_type['args']['taxonomies'] ?? []) ?></div>
                        </td>
                        <td>
                            <div><?= $post_type['args']['description'] ?? '...' ?></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </article>
    </section>
</div>