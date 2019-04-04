<?php

namespace Sioweb\Glossar\Entity;
use \Doctrine\ORM\Mapping as ORM;

/**
 * Class Log
 *
 * @ORM\Entity
 * @ORM\Table(name="tl_glossar_log")
 * @ORM\Entity(repositoryClass="Sioweb\Glossar\Repository\LogsRepository")
 */
class Log
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Terms")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id")
     */
    protected $pid;

    /**
     * @var int
     * @ORM\Column(type="integer", length=10)
     */
    protected $tstamp;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $page;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $host;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $language;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $action;


    public function getData() {
        $arrData = [];
        foreach(preg_grep('|^get(?!Data)|', get_class_methods($this)) as $method) {
            $arrData[($Field = lcfirst(substr($method, 3)))] = $this->{$method}();
            if(is_object($arrData[$Field])) {
                $arrData[$Field] = $arrData[$Field]->getData();
            }
        }
        
        return $arrData;
    }


    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of tid
     */ 
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * Set the value of tid
     *
     * @return  self
     */ 
    public function setTid($tid)
    {
        $this->tid = $tid;

        return $this;
    }

    /**
     * Get the value of tstamp
     *
     * @return  int
     */ 
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * Set the value of tstamp
     *
     * @param  int  $tstamp
     *
     * @return  self
     */ 
    public function setTstamp(int $tstamp)
    {
        $this->tstamp = $tstamp;

        return $this;
    }

    /**
     * Get the value of user
     *
     * @return  string
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @param  string  $user
     *
     * @return  self
     */ 
    public function setUser(string $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of pid
     *
     * @return  string
     */ 
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set the value of pid
     *
     * @param  string  $pid
     *
     * @return  self
     */ 
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get the value of page
     *
     * @return  string
     */ 
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set the value of page
     *
     * @param  string  $page
     *
     * @return  self
     */ 
    public function setPage(string $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get the value of host
     *
     * @return  string
     */ 
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the value of host
     *
     * @param  string  $host
     *
     * @return  self
     */ 
    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the value of language
     *
     * @return  string
     */ 
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the value of language
     *
     * @param  string  $language
     *
     * @return  self
     */ 
    public function setLanguage(string $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get the value of action
     *
     * @return  string
     */ 
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the value of action
     *
     * @param  string  $action
     *
     * @return  self
     */ 
    public function setAction(string $action)
    {
        $this->action = $action;

        return $this;
    }
}