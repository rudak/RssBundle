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
                'pubdate'     => $this->getAssociation($entity, 'pubDate'),
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

        if (is_array($attr)) {
            return $this->getArrayAssociation($entity, $attr);
        } else {
            $value = $this->getEntityValue($entity, $attr);
        }
        return $value;
    }

    /*
     * Renvoie le type spécifique demandé si il s'agit d'un parametre spécial
     */
    private function getArrayAssociation($entity, $attr)
    {
        #TODO verifier si les parametres requis sont la pour chaques types
        switch (strtolower($attr['type'])) {
            case 'datetime':
                if ($entity) {
                    $value = $this->getEntityValue($entity, $attr['index']);
                } else {
                    $value = new \DateTime('NOW');
                }
                return $value->format(\DateTime::RSS);
                break;
            case 'slug':
                if ($entity) {
                    $value = $this->getEntityValue($entity, $attr['index']);
                } else {
                    $value = $attr['index'];
                }
                $Slug = new Slug($value);
                return $Slug->getSlug();
                break;
            case 'image':
                return array(
                    'url'   => $attr['url'],
                    'title' => $attr['title'],
                    'link'  => $attr['link'] ? $attr['link'] : ''
                );
                break;
        }
    }

    /*
     * renvoie un lien correspondant a l'entité et a la route passée en parametres
     */
    private function getItemLink($entity)
    {
        $route    = $this->parameters['items']['route'];
        $argsName = $this->parameters['items']['params'];

        $params = $this->getParamsForLink($entity, $argsName);
        return $this->routeur->generate($route, $params, true);
    }

    /*
     * Va chercher les parametres pour la création des URL
     */
    private function getParamsForLink($entity, $argsName)
    {
        $params = array();
        foreach ($argsName as $key => $arg) {
            if (is_array($arg)) {
                $params[$key] = $this->getArrayAssociation($entity, $arg);
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
        #TODO faire des verifs de methodes
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
                $this->channel_infos[$key] = $this->getArrayAssociation(null, $value);
            } else {
                $this->channel_infos[$key] = $value;
            }
        }
    }


    /*
     * Ecriture du fichier
     */
    public function writeTheRssFile()
    {
        $writer = new RssWriter();

        $content = $this->templating->render('RudakRssBundle:Default:rss.xml.twig', array(
            'channel' => $this->channel_infos,
            'items'   => $this->items_infos,
        ));

        if (!$writer->writeTheFile($content)) {
            throw new \Exception('Le fichier ./web/RSS.xml doit etre créé, avec les permissions qui vont bien.');
        }
    }


}