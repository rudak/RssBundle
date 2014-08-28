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
        echo '<pre style="font-size:0.6em;">';
        print_r($this->parameters);
        echo '</pre>';

        $this->setHeadersInfos();

        echo '<pre style="font-size:0.6em;">';
        print_r($this->channel_infos);
        echo '</pre>';
    }

    private function setHeadersInfos()
    {
        foreach ($this->parameters['channel'] as $key => $value) {
            if (is_array($value)) {
                $this->channel_infos[$key] = $this->getObjectValue($value);
            } else {
                $this->channel_infos[$key] = $value;
            }
        }
    }

    private function getObjectValue($object)
    {
        switch ($object['type']) {
            case 'datetime':
                $date = new \DateTime('NOW');
                return $date->format(\DateTime::RSS);
                break;
            case 'image':
                return false;
                break;
        }
    }

    private function getMethodByAttributeName($attribute)
    {
        return 'get' . ucfirst($attribute);
    }

    public function writeTheRssFile()
    {
        $writer = new RssWriter;
        if (!$writer->writeTheFile('coucou')) {
            throw new \Exception('Le fichier ./web/RSS.xml doit etre créé, avec les permissions qui vont bien.');
        }
    }


}