<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 25/08/14
 * Time: 17:28
 */

namespace Rudak\RssBundle\Classes;


class RssWriter
{
    private $fileName;

    public function __construct($fileName = null)
    {
        $this->fileName = $fileName ? $fileName : 'rss.xml';
    }

    public function writeTheFile($data = '')
    {
        if (!is_writable($this->fileName)) {
            return false;
        } else {
            return file_put_contents($this->fileName, $data);
        }
    }

    /**
     * @return null|string
     */
    public function getFileName()
    {
        return $this->fileName;
    }
    

} 