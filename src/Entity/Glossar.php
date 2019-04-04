<?php

namespace Sioweb\Glossar\Entity;
use \Doctrine\ORM\Mapping as ORM;

/**
 * Class TlVippKonfiguration
 *
 * @ORM\Entity
 * @ORM\Table(name="tl_glossar")
 * @ORM\Entity(repositoryClass="Sioweb\Glossar\Repository\GlossarRepository")
 */
class Glossar
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    protected $tstamp;
    
    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $title = '';
    
    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $alias;
    
    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $language;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $fallback;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $allowComments;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=16, options={"default" : ""})
     */
    protected $notify;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=32, options={"default" : ""})
     */
    protected $sortOrder;
    
    /**
     * @var int
     * @ORM\Column(type="smallint", length=5, options={"default" : 0})
     */
    protected $perPage;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $moderate;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $bbcode;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $requireLogin;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : "", "fixed" : true})
     */
    protected $disableCaptcha;


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
     * Get the value of title
     *
     * @return  string
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param  string  $title
     *
     * @return  self
     */ 
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of alias
     *
     * @return  string
     */ 
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set the value of alias
     *
     * @param  string  $alias
     *
     * @return  self
     */ 
    public function setAlias(string $alias)
    {
        $this->alias = $alias;

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
     * Get the value of fallback
     *
     * @return  string
     */ 
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * Set the value of fallback
     *
     * @param  string  $fallback
     *
     * @return  self
     */ 
    public function setFallback(string $fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * Get the value of allowComments
     *
     * @return  string
     */ 
    public function getAllowComments()
    {
        return $this->allowComments;
    }

    /**
     * Set the value of allowComments
     *
     * @param  string  $allowComments
     *
     * @return  self
     */ 
    public function setAllowComments(string $allowComments)
    {
        $this->allowComments = $allowComments;

        return $this;
    }

    /**
     * Get the value of notify
     *
     * @return  string
     */ 
    public function getNotify()
    {
        return $this->notify;
    }

    /**
     * Set the value of notify
     *
     * @param  string  $notify
     *
     * @return  self
     */ 
    public function setNotify(string $notify)
    {
        $this->notify = $notify;

        return $this;
    }

    /**
     * Get the value of sortOrder
     *
     * @return  string
     */ 
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set the value of sortOrder
     *
     * @param  string  $sortOrder
     *
     * @return  self
     */ 
    public function setSortOrder(string $sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get the value of perPage
     *
     * @return  int
     */ 
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Set the value of perPage
     *
     * @param  int  $perPage
     *
     * @return  self
     */ 
    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get the value of moderate
     *
     * @return  string
     */ 
    public function getModerate()
    {
        return $this->moderate;
    }

    /**
     * Set the value of moderate
     *
     * @param  string  $moderate
     *
     * @return  self
     */ 
    public function setModerate(string $moderate)
    {
        $this->moderate = $moderate;

        return $this;
    }

    /**
     * Get the value of bbcode
     *
     * @return  string
     */ 
    public function getBbcode()
    {
        return $this->bbcode;
    }

    /**
     * Set the value of bbcode
     *
     * @param  string  $bbcode
     *
     * @return  self
     */ 
    public function setBbcode(string $bbcode)
    {
        $this->bbcode = $bbcode;

        return $this;
    }

    /**
     * Get the value of requireLogin
     *
     * @return  string
     */ 
    public function getRequireLogin()
    {
        return $this->requireLogin;
    }

    /**
     * Set the value of requireLogin
     *
     * @param  string  $requireLogin
     *
     * @return  self
     */ 
    public function setRequireLogin(string $requireLogin)
    {
        $this->requireLogin = $requireLogin;

        return $this;
    }

    /**
     * Get the value of disableCaptcha
     *
     * @return  string
     */ 
    public function getDisableCaptcha()
    {
        return $this->disableCaptcha;
    }

    /**
     * Set the value of disableCaptcha
     *
     * @param  string  $disableCaptcha
     *
     * @return  self
     */ 
    public function setDisableCaptcha(string $disableCaptcha)
    {
        $this->disableCaptcha = $disableCaptcha;

        return $this;
    }
}