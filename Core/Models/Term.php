<?php

namespace Silmaril\Core\Models;

use Silmaril\Core\Model;
use Silmaril\Core\Support\Collection;

/**
 * Modelo que se comunica con la tabla wp_terms
 * siendo "wp_" el prefix
 *
 * @author Carmine Maggio
 * @version 1.0.0
 * @package Silmaril Theme
 */
class Term extends Model
{
	/**
	 * Obtiene todas las taxonomies de los posts
	 *
	 * @param array $posts_id
	 * @param array $onlyTaxonomies
	 * @return Collection
	 */
	public function getAllTaxonomies(array $posts_id, array $onlyTaxonomies = []): Collection
	{
		global $wpdb;

		$term_taxonomy_table = "{$wpdb->prefix}term_taxonomy AS taxonomy";
		$term_relations_table = "{$wpdb->prefix}term_relationships AS relation";
		$posts_id = implode(', ', $posts_id);
		$taxonomies = '';

		foreach ($onlyTaxonomies as $key => $taxonomy) {
			// Ultima
			if ( count($onlyTaxonomies) - 1 === $key  ) {
				$taxonomies .= "'{$taxonomy}'";
				continue;
			}

			$taxonomies .= "'{$taxonomy}', ";
		}

		$query = $this->select([
			"{$this->table}.term_id",
			"{$this->table}.name",
			"{$this->table}.slug",
			'taxonomy.taxonomy',
			'relation.object_id AS post_id',
		])
			->join($term_taxonomy_table, "{$this->table}.term_id = taxonomy.term_id")
			->join($term_relations_table, "taxonomy.term_taxonomy_id = relation.term_taxonomy_id");

		if ( count($onlyTaxonomies) > 1 ) {
			$query->where('taxonomy.taxonomy', 'IN', "({$taxonomies})");
		}

		return $query
			->where("relation.object_id", 'IN', "({$posts_id})")
			->get();
	}
}