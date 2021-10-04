<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\Extension;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @file Configuration.php
 * @class Configuration
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Configuration implements ConfigurationInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder('glossar');

		$treeBuilder
			->getRootNode()
				->children()
					->arrayNode('css')
						->children()
							->scalarNode('maxWidth')
								->defaultValue(450)
							->end()
							->scalarNode('maxHeight')
								->defaultValue(300)
							->end()
						->end()
					->end()
					->scalarNode('illegal')
						->defaultValue('\-_\.&><;')
					->end()
					->arrayNode('templates')
						->scalarPrototype(['ce_glossar', 'glossar_default', 'glossar_error', 'glossar_layer'])->end()
					->end()
					->arrayNode('tables')
						->scalarPrototype(['ce_glossar', 'glossar_default', 'glossar_error', 'glossar_layer'])->end()
					->end()
			->end();

    	return $treeBuilder;
	}
}
