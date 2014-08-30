<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 25/08/14
 * Time: 16:57
 */

namespace Rudak\RssBundle\Classes;

use Rudak\RssBundle\Classes\RssWriter;
use Rudak\RssBundle\Classes\Slug;

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

    /*
     *Fabrique le contenu du RSS
     */
    public function makeTheRssContent()
    {
        $this->setHeadersInfos();
        $this->setItemsInfos();

        echo '<pre style="font-size:0.6em;">';
        print_r($this->items_infos);
        echo '</pre>';
    }

    /*
     * Boucle sur les entités pour sortir les items du RSS
     */
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

    /*
     * Retourne la valeur de l'entité correspondant a l'association passée en parametres
     */
    private function getAssociation($entity, $association)
    {
        $attr = $this->parameters['associations'][$association];
        return $this->getEntityValue($entity, $attr);
    }

    /*
     * renvoie un lien correspondant a l'entité et a la route passée en parametres
     */
    private function getItemLink($entity)
    {
        $route    = $this->parameters['items']['route'];
        $argsName = $this->parameters['items']['params'];

        $params = $this->getParamsForLink($entity, $argsName);
        return $this->routeur->generate($route, $params);
    }

    /*
     * Va chercher les parametres pour la création des URL
     */
    private function getParamsForLink($entity, $argsName)
    {
        $params = array();
        foreach ($argsName as $key => $arg) {
            if (is_array($arg)) {
                if ($arg['type'] == 'slug') {
                    $entityValue  = $this->getEntityValue($entity, $arg['target']);
                    $slug         = new Slug($entityValue);
                    $params[$key] = $slug->getSlug();
                }
            } else {
                $params[$key] = $this->getEntityValue($entity, $arg);
            }
        }
        return $params;
    }

    /*
     * retourne la valeur de l'attribut $attr dans l'entité $entity
     */
    private function getEntityValue($entity, $attr)
    {
        # TODO faire des verifs de methodes
        return call_user_func(array($entity, $this->getMethodByAttributeName($attr)));
    }

    /*
     * Renvoie le nom de la methode en fonction du nom de l'attribut
     */
    private function getMethodByAttributeName($attribute)
    {
        return 'get' . ucfirst($attribute);
    }

    /*
     * Creation de l'entete
     */
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

    /*
     * Renvoie la valeur de l'objet selon le type
     */
    private function getObjectValue($object)
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

    /*
     * Ecriture du fichier
     */
    public function writeTheRssFile()
    {
        $writer = new RssWriter;
        if (!$writer->writeTheFile('coucou')) {
            throw new \Exception('Le fichier ./web/RSS.xml doit etre créé, avec les permissions qui vont bien.');
        }
    }


}