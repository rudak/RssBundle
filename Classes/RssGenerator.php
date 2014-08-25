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

    public function __construct($parameters, $services)
    {
        $this->routeur    = $services['router'];
        $this->templating = $services['templating'];
        $this->doctrine   = $services['doctrine'];
    }

    public function setRssEntities(array $entities)
    {

    }

    public function writeTheRssFile()
    {
        $writer = new RssWriter;
        if (!$writer->writeTheFile('coucou')) {
            throw new \Exception('Le fichier ./web/RSS.xml doit etre créé, avec les permissions qui vont bien.');
        }
    }


}