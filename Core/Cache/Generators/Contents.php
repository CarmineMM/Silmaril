<?php

namespace Silmaril\Core\Cache\Generators;

use Silmaril\Core\Contents\PostTypes;
use Silmaril\Core\Contents\Taxonomies;
use Silmaril\Core\Support\Collection;

trait Contents
{
    /**
     * Habilitar el uso de taxonomies
     *
     * @var bool
     */
    protected bool $useTaxonomies = true;

    /**
     * Habilitar el uso de post types
     *
     * @var bool
     */
    protected bool $usePostTypes = true;

    /**
     * Nombre de la función de la taxonomies
     *
     * @var string
     */
    protected string $funcTaxonomies = 'register_taxonomies';

	/**
	 * Nombre de la función de registro de taxonomies
	 *
	 * @var string
	 */
	protected string $funcPostTypes = 'register_post_types';

    /**
     * Nombre de la función en para la carga de scripts y estilos
     *
     * @return string
     */
    public function getNameFuncTaxonomies(): string
    {
        return TEXT_DOMAIN .'_'. $this->funcTaxonomies;
    }

    /**
     * Nombre de la función en para la carga de scripts y estilos
     *
     * @return string
     */
    public function getNameFuncPostTypes(): string
    {
        return TEXT_DOMAIN .'_'. $this->funcPostTypes;
    }

    /**
     * Funciones de taxonomies
     *
     * @param array $config
     * @return string
     * @throws \Exception
     */
    public function createFunctionTaxonomies(array $config): string
    {
        $funcTaxonomies = "\nfunction {$this->getNameFuncTaxonomies()}(): void {";

        foreach ($config as $taxonomy) {
            $contentsTaxonomies = new Taxonomies();

            $fields = $contentsTaxonomies->fields(
                names: $taxonomy['names'],
                labels: $taxonomy['labels'] ?? [],
                arg: $taxonomy['args'] ?? [],
                genderName: $taxonomy['gender_name'] ?? 'o'
            );

            $args = $this->createArrayToString($fields->toArray());
            $labels = $this->generateDeps($taxonomy['object_type']);

            $funcTaxonomies .= "\nregister_taxonomy('{$taxonomy['taxonomy']}', {$labels}, {$args});";
        }

        $funcTaxonomies .= "\n}";

        return $funcTaxonomies;
    }

    /**
     * @param array $fields
     * @return string
     */
    protected function createArrayToString(array $fields): string
    {
        $return = "[";

        foreach ($fields as $key => $value) {
            if ( is_array($value) ) {
                $return .= "\n\t'{$key}' => ".$this->createArrayToString($value).",";
            }
            elseif (is_bool($value)) {
                $bool = $value ? 'true' : 'false';
                $return .= "\n\t'{$key}' => {$bool},";
            }
            else {
                $return .= "\n\t'{$key}' => '{$value}',";
            }
        }

        $return .= "\n]";

        return $return;
    }

	/**
	 * Crear funciones para los post types
	 *
	 * @param array $config
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function createFunctionPostTypes(array $config): string
	{
		$funcPostTypes = "\nfunction {$this->getNameFuncPostTypes()}(): void {";

		foreach ($config as $post_type){
			$contentsPostTypes = new PostTypes();

			$fields = $contentsPostTypes->fields(
				$post_type['names'],
				$post_type['labels'] ?? [],
				$post_type['args'] ?? [],
				$post_type['gender_name'] ?? 'o'
			);

			$args = $this->createArrayToString($fields->toArray());
			$funcPostTypes .= "\nregister_post_type('{$post_type['post_type']}', {$args});";
		}

		$funcPostTypes .= "\n}";

		return $funcPostTypes;
	}
}