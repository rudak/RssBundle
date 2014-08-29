<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 25/08/14
 * Time: 16:57
 */

namespace Rudak\RssBundle\Classes;

use Rudak\RssBundle\Classes\RssWriter;

/**
 *
 * @author rudak
 */
class RssGenerator
{

    private $routeur;
    private $templating;
    private $doctrine;
    private $entities;

    private $items_infos;
    private $channel_infos;

    private $parameters;

    public function __construct($parameters, $services)
    {
        $this->routeur    = $services['router'];
        $this->templating = $services['templating'];
        $this->doctrine   = $services['doctrine'];
        $this->parameters = $parameters;
    }

    public function setRssEntities(array $entities)
    {
        $this->entities = $entities;
    }

    public function makeTheRssContent()
    {
        $this->setHeadersInfos();
        $this->setItemsInfos();

        echo '<pre style="font-size:0.6em;">';
        print_r($this->items_infos);
        echo '</pre>';
    }

    private function setItemsInfos()
    {
        foreach ($this->entities as $entity) {
            $this->items_infos[] = array(
                'title'       => $this->getAssociation($entity, 'title'),
                'description' => $this->getAssociation($entity, 'description'),
                'pubDate'     => $this->getAssociation($entity, 'pubDate'),
                'guid'        => $this->getAssociation($entity, 'guid'),
                'link'        => $this->getItemLink($entity),
            );
        }
    }

    private function getItemLink($entity)
    {
        $route    = $this->parameters['items']['route'];
        $argsName = $this->parameters['items']['params'];

        $params = $this->getParamsForLink($entity, $argsName);
        return $this->routeur->generate($route, $params);
    }

    private function getParamsForLink($entity, $argsName)
    {
        $params = array();
        foreach ($argsName as $key => $arg) {
            if (is_array($arg)) {
                if ($arg['type'] == 'slug') {
                    $entityValue  = call_user_func(array($entity, $this->getMethodByAttributeName($arg['target'])));
                    $params[$key] = 'fghjk'; #TODO slugger la valeur de l'entité
                }
            } else {
                $params[$key] = call_user_func(array($entity, $this->getMethodByAttributeName($arg)));
            }
        }
        return $params;
    }

    private function getAssociation($entity, $association)
    {
        $attr = $this->parameters['associations'][$association];
        return $this->getEntityValue($entity, $attr);
    }

    private function getEntityValue($entity, $attr)
    { #TODO a étendre aux autres
        return call_user_func(array($entity, $this->getMethodByAttributeName($attr)));
    }

    private
    function getMethodByAttributeName($attribute)
    {
        return 'get' . ucfirst($attribute);
    }

    private
    function setHeadersInfos()
    {
        foreach ($this->parameters['channel'] as $key => $value) {
            if (is_array($value)) {
                $this->channel_infos[$key] = $this->getObjectValue($value);
            } else {
                $this->channel_infos[$key] = $value;
            }
        }
    }

    private
    function getObjectValue($object)
    {
        switch ($object['type']) {
            case 'datetime':
                $date = new \DateTime('NOW');
                return $date->format(\DateTime::RSS);
                break;
            case 'image':
                return
                    array(
                        'url'   => 'http://image.fr',
                        'title' => 'une image',
                        'link'  => 'http://kadur-arnaud.fr'
                    );
                break;
        }
    }


    public
    function writeTheRssFile()
    {
        $writer = new RssWriter;
        if (!$writer->writeTheFile('coucou')) {
            throw new \Exception('Le fichier ./web/RSS.xml doit etre créé, avec les permissions qui vont bien.');
        }
    }


}