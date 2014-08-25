<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 25/08/14
 * Time: 16:57
 */

namespace Rudak\RssBundle\Classes;

/**
 *
 * @author rudak
 */
class RssGenerator
{

    private $parameters;
    // services
    private $routeur;
    private $templating;
    private $doctrine;
    private $entities;
    private $items_infos;
    private $channel_infos;

    public function __construct($parameters, $services)
    {
        $this->parameters = $parameters;
        $this->routeur    = $services['router'];
        $this->templating = $services['templating'];
        $this->doctrine   = $services['doctrine'];
    }

    public function make_the_rss_file()
    {
        return;
        $this->getEntities();
        $this->getItemsInfos();
        $this->getChannelInfos();
        $xml = $this->getRenderedXML();
        $this->write_xml_file($xml);
    }

    /**
     * Va chercher les entités necessaires a la creation du fichier xml
     * @return type
     */
    private function getEntities()
    {
        $repository = $this->parameters['entity']['repository'];
        $methodName = $this->parameters['entity']['methodName'];

        $em   = $this->doctrine->getManager();
        $repo = $em->getRepository($repository);

        $this->entities = call_user_func(array($repo, $methodName));
    }

    /**
     * Boucle sur les entités pour chopper les infos correspondant aux
     * parametres d'associations.
     *
     * @param type $entities
     */
    private function getItemsInfos()
    {

        $associations = $this->parameters['associations'];
        $infos        = array();
        foreach ($this->entities as $entity) {
            $date    = call_user_func(array($entity, 'get' . ucfirst($associations['pubDate'])));
            $infos[] = array(
                'title'       => call_user_func(array($entity, 'get' . ucfirst($associations['title']))),
                'description' => call_user_func(array($entity, 'get' . ucfirst($associations['description']))),
                'pubdate'     => $date->format(\DateTime::RSS),
                'guid'        => call_user_func(array($entity, 'get' . ucfirst($associations['guid']))),
                'link'        => $this->getItemUrl($this->parameters['items']['route'], $this->getRoutingParams($entity)),
            );
        }
        $this->items_infos = $infos;
    }

    /**
     * On choppe et on classe les informations du channel dans l'attribut
     */
    private function getChannelInfos()
    {
        $channel = $this->parameters['channel'];
        foreach ($channel as $param => $value) {
            if (is_string($value)) {
                $this->channel_infos[$param] = $value;
            } elseif (is_array($value)) {
                switch ($value['_object']['type']) {
                    case 'datetime':
                        $date                                            = new \DateTime();
                        $this->channel_infos[$value['_object']['index']] = $date->format(\DateTime::RSS);
                        break;
                    case 'image':
                        $this->channel_infos[$value['_object']['index']]['url'] = $value['_object']['url'];
                        break;
                }
            }
        }
    }

    /**
     * Renvoie le lien d'une route
     * @param type $route
     * @param type $args
     * @return type
     */
    private function getItemUrl($route, $args)
    {
        return $this->routeur->generate($route, $args, true);
    }

    /**
     * Renvoi le xml tel qu'il sera inscrit dans le fichier
     *
     * @return type
     */
    private function getRenderedXML()
    {
        return $this->templating->render('RssRssBundle:Default:rss.xml.twig', array(
            'channel' => $this->channel_infos,
            'items'   => $this->items_infos,
        ));
    }

    /**
     *
     * @param type $data ecrit dans le fichier XML
     */
    private function write_xml_file($data)
    {
        $file = $this->getXmlFilePath();
        file_put_contents($file, $data);
    }

    /**
     * Renvoie le chemin relatif vers le fichier XML
     *
     * @return type chaine de caractères
     */
    private function getXmlFilePath($both = true)
    {
        $path = __DIR__ . '/../../../../web';
        $file = 'rss.xml';
        return ($both) ? $path . '/' . $file : $path;
    }

    /**
     * Renvoie les urls pour les items
     *
     * @param type $entity
     */
    private function getRoutingParams($entity)
    {
        $params = $this->parameters['items']['params'];
        $tab    = array();
        foreach ($params as $index => $value) {

            if (is_string($value)) {
                $tab[$index] = call_user_func(array($entity, 'get' . ucfirst($value)));
            } else {
                switch ($value['type']) {
                    case 'slug':
                        $tab[$index] = $this->applyFilter('slug', call_user_func(array($entity, 'get' . ucfirst($value['target']))));
                        break;
                }
            }
        }
        return $tab;
    }

    private function applyFilter($filter, $string)
    {
        switch ($filter) {
            case 'slug':
                $slug = new \Perso\Tools\Slug\Slug();
                return $slug->giveTheString($string)->getMySlug();
                break;
        }
    }

}