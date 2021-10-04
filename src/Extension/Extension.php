<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\Extension;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @file Extension.php
 * @class Extension
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Extension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'sioweb_glossar';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $baseConfig = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/glossar.yml'), Yaml::PARSE_CONSTANT);
        $configs = array_filter(array_merge([$baseConfig['glossar']], $configs));

        $rootDir = $container->getParameter('kernel.project_dir');
        
        if (file_exists($rootDir . '/config/glossar.yml')) {
            $root_baseConfig = Yaml::parse(file_get_contents($rootDir . '/config/glossar.yml'), Yaml::PARSE_CONSTANT);
            $configs = [array_filter(array_merge($configs[0], $root_baseConfig['glossar']))];
        }

        $mergedConfig = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('listener.yml');
        $loader->load('services.yml');

        $container->setParameter('glossar.config', $mergedConfig);
    }
}
