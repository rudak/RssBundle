<?php

namespace Rudak\RssBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('rudak_rss');

        $rootNode
            ->children()
                ->arrayNode('rssparameters')
                    ->children()
                        ->arrayNode('entity')
                            ->children()
                                ->scalarNode('repository')->defaultValue('AcmeBlogBundle:Article')->end()
                                ->scalarNode('methodName')->defaultValue('getAllArticles')->end()
                            ->end()
                        ->end()
                        ->arrayNode('channel')
                            ->children()
                                ->scalarNode('title')->defaultValue('')->end()
                                ->scalarNode('link')->defaultValue('')->end()
                                ->scalarNode('description')->defaultValue('')->end()
                                ->arrayNode('pubDate')
                                    ->children()
                                        ->scalarNode('type')->defaultValue('datetime')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('image')
                                    ->children()
                                        ->scalarNode('type')->defaultValue('image')->end()
                                        ->scalarNode('url')->defaultValue('http://www.image.com/mon-image.jpg')->end()
                                        ->scalarNode('title')->defaultValue('mon blog de cuisine')->end()
                                        ->scalarNode('link')->defaultValue('http://www.mon-site.com')->end()
                                    ->end()
                                ->end()
                                ->scalarNode('language')->defaultValue('fr')->end()
                                ->scalarNode('copyright')->defaultValue('you@mail.fr')->end()
                                ->scalarNode('webmaster')->defaultValue('default webmaster')->end()
                                ->arrayNode('lastBuildDate')
                                    ->children()
                                        ->scalarNode('type')->defaultValue('datetime')->end()
                                    ->end()
                                ->end()
                                ->scalarNode('managingEditor')->defaultValue('default manager')->end()
                                ->scalarNode('generator')->defaultValue('rudak rss generator')->end()
                                ->scalarNode('ttl')->defaultValue('10505')->end()
                            ->end()
                        ->end()
                        ->arrayNode('items')
                            ->children()
                                ->scalarNode('route')->defaultValue('AcmeBlogBundle:Article')->end()
                                ->arrayNode('params')
                                    ->children()
                                        ->scalarNode('id')->defaultValue('id')->end()
                                        ->arrayNode('slug')
                                            ->children()
                                                ->scalarNode('type')->defaultValue('slug')->end()
                                                ->scalarNode('index')->defaultValue('title')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('associations')
                            ->children()
                                ->scalarNode('title')->defaultValue('title')->end()
                                ->scalarNode('description')->defaultValue('description')->end()
                                ->arrayNode('pubDate')
                                    ->children()
                                        ->scalarNode('type')->defaultValue('datetime')->end()
                                        ->scalarNode('index')->defaultValue('createdAt')->end()
                                    ->end()
                                ->end()
                                ->scalarNode('guid')->defaultValue('id')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
