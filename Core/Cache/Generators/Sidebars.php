<?php

namespace Silmaril\Core\Cache\Generators;

use Silmaril\Core\Support\Collection;

trait Sidebars
{
    /**
     * Nombre de la función de register sidebars
     *
     * @var string
     */
    protected string $funcRegisterSidebars = 'register_sidebars';

    /**
     * Función para el registro de sidebars
     *
     * @return string
     */
    protected function getNameFuncRegisterSidebars(): string
    {
        return TEXT_DOMAIN .'_'. $this->funcRegisterSidebars;
    }

    /**
     *
     *
     * @param array $sidebars
     * @return string
     */
    public function createSidebars(array $sidebars): string
    {
        $func = "\nfunction {$this->getNameFuncRegisterSidebars()}(): void {";

        foreach ($sidebars as $sidebar) {
            $sidebar = new Collection($sidebar);

            $showInRest = $sidebar->get('show_in_rest', false) ? 'true' : 'false';

            $func .= "\n\tregister_sidebar([";
            $func .= "'id' => '". $sidebar->get('id') ."', ";
            $func .= "'name' => '". $sidebar->get('name') ."', ";
            $func .= "'description' => '". $sidebar->get('description', '') ."', ";
            $func .= "'class' => '". $sidebar->get('class', '') ."', ";
            $func .= "'before_widget' => '". $sidebar->get('before_widget', '<li id="%1$s" class="widget %2$s">') ."', ";
            $func .= "'after_widget' => '". $sidebar->get('after_widget', '</li>') ."', ";
            $func .= "'before_title' => '". $sidebar->get('before_title', '<h2 class="widgettitle">') ."', ";
            $func .= "'after_title' => '". $sidebar->get('after_title', '</h2>') ."', ";
            $func .= "'before_sidebar' => '". $sidebar->get('before_sidebar', '') ."', ";
            $func .= "'after_sidebar' => '". $sidebar->get('after_sidebar', '') ."', ";
            $func .= "'show_in_rest' => {$showInRest}";
            $func .= "]);";
        }

        $func .= "\n}";

        return $func;
    }
}