<?php

namespace Sioweb\Glossar\Entity;
use \Doctrine\ORM\Mapping as ORM;

/**
 * Class Log
 *
 * @ORM\Entity
 * @ORM\Table(name="tl_sw_glossar")
 * @ORM\Entity(repositoryClass="Sioweb\Glossar\Repository\TermsRepository")
 */
class Terms
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
  
    /**
     * @ORM\ManyToOne(targetEntity="Glossar")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id")
     */
    protected $pid;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    protected $tstamp;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $alias;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $source;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $jumpTo;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $articleId;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $url;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $target;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $maxWidth;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $maxHeight;

    /**
     * @var string
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    protected $ignoreInTags;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""}, nullable=true)
     */
    protected $illegalChars;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $date;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $noPlural;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $strictSearch;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $termAsHeadline;

    /**
     * @var string
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    protected $teaser;

    /**
     * @var string
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $explanation;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $seo;

    /**
     * @var string
     * @ORM\Column(name="term_in_title_tag", type="string", length=1, options={"default" : ""})
     */
    protected $termInTitleTag;

    /**
     * @var string
     * @ORM\Column(name="term_in_title_str_tag", type="string", options={"default" : ""})
     */
    protected $termInTitleStrTag;

    /**
     * @var string
     * @ORM\Column(name="replace_pageTitle", type="string", type="string", length=1, options={"default" : ""})
     */
    protected $replacePageTitle;

    /**
     * @var string
     * @ORM\Column(name="term_description_tag", type="string", options={"default" : ""})
     */
    protected $termDescriptionTag;

    /**
     * @var string
     * @ORM\Column(type="string", type="string", length=1, options={"default" : ""})
     */
    protected $tags;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $published = 1;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $start;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $stop;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $addImage;

    /**
     * @var string
     * @ORM\Column(type="binary", length=16, nullable=true)
     */
    protected $singleSRC;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $size;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $floating;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default" : ""})
     */
    protected $imagemargin;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $fullsize;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, options={"default" : ""})
     */
    protected $overwriteMeta;

    public function row()
    {
        return $this->getData();
    }

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

    public function setId($id)
    {
        $this->id = $id;
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
     * Get the value of type
     *
     * @return  string
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param  string  $type
     *
     * @return  self
     */ 
    public function setType(string $type)
    {
        $this->type = $type;

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
     * Get the value of source
     *
     * @return  string
     */ 
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the value of source
     *
     * @param  string  $source
     *
     * @return  self
     */ 
    public function setSource(string $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get the value of jumpTo
     *
     * @return  string
     */ 
    public function getJumpTo()
    {
        return $this->jumpTo;
    }

    /**
     * Set the value of jumpTo
     *
     * @param  string  $jumpTo
     *
     * @return  self
     */ 
    public function setJumpTo(string $jumpTo)
    {
        $this->jumpTo = $jumpTo;

        return $this;
    }

    /**
     * Get the value of articleId
     *
     * @return  string
     */ 
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * Set the value of articleId
     *
     * @param  string  $articleId
     *
     * @return  self
     */ 
    public function setArticleId(string $articleId)
    {
        $this->articleId = $articleId;

        return $this;
    }

    /**
     * Get the value of url
     *
     * @return  string
     */ 
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @param  string  $url
     *
     * @return  self
     */ 
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of target
     *
     * @return  string
     */ 
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the value of target
     *
     * @param  string  $target
     *
     * @return  self
     */ 
    public function setTarget(string $target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get the value of maxWidth
     *
     * @return  string
     */ 
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * Set the value of maxWidth
     *
     * @param  string  $maxWidth
     *
     * @return  self
     */ 
    public function setMaxWidth(string $maxWidth)
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    /**
     * Get the value of maxHeight
     *
     * @return  string
     */ 
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * Set the value of maxHeight
     *
     * @param  string  $maxHeight
     *
     * @return  self
     */ 
    public function setMaxHeight(string $maxHeight)
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    /**
     * Get the value of ignoreInTags
     *
     * @return  string
     */ 
    public function getIgnoreInTags()
    {
        return $this->ignoreInTags;
    }

    /**
     * Set the value of ignoreInTags
     *
     * @param  string  $ignoreInTags
     *
     * @return  self
     */ 
    public function setIgnoreInTags(string $ignoreInTags)
    {
        $this->ignoreInTags = $ignoreInTags;

        return $this;
    }

    /**
     * Get the value of illegalChars
     *
     * @return  string
     */ 
    public function getIllegalChars()
    {
        return $this->illegalChars;
    }

    /**
     * Set the value of illegalChars
     *
     * @param  string  $illegalChars
     *
     * @return  self
     */ 
    public function setIllegalChars(string $illegalChars)
    {
        $this->illegalChars = $illegalChars;

        return $this;
    }

    /**
     * Get the value of date
     *
     * @return  string
     */ 
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @param  string  $date
     *
     * @return  self
     */ 
    public function setDate(string $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of noPlural
     *
     * @return  string
     */ 
    public function getNoPlural()
    {
        return $this->noPlural;
    }

    /**
     * Set the value of noPlural
     *
     * @param  string  $noPlural
     *
     * @return  self
     */ 
    public function setNoPlural(string $noPlural)
    {
        $this->noPlural = $noPlural;

        return $this;
    }

    /**
     * Get the value of strictSearch
     *
     * @return  string
     */ 
    public function getStrictSearch()
    {
        return $this->strictSearch;
    }

    /**
     * Set the value of strictSearch
     *
     * @param  string  $strictSearch
     *
     * @return  self
     */ 
    public function setStrictSearch(string $strictSearch)
    {
        $this->strictSearch = $strictSearch;

        return $this;
    }

    /**
     * Get the value of termAsHeadline
     *
     * @return  string
     */ 
    public function getTermAsHeadline()
    {
        return $this->termAsHeadline;
    }

    /**
     * Set the value of termAsHeadline
     *
     * @param  string  $termAsHeadline
     *
     * @return  self
     */ 
    public function setTermAsHeadline(string $termAsHeadline)
    {
        $this->termAsHeadline = $termAsHeadline;

        return $this;
    }

    /**
     * Get the value of teaser
     *
     * @return  string
     */ 
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Set the value of teaser
     *
     * @param  string  $teaser
     *
     * @return  self
     */ 
    public function setTeaser(string $teaser)
    {
        $this->teaser = $teaser;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return  string
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string  $description
     *
     * @return  self
     */ 
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of explanation
     *
     * @return  string
     */ 
    public function getExplanation()
    {
        return $this->explanation;
    }

    /**
     * Set the value of explanation
     *
     * @param  string  $explanation
     *
     * @return  self
     */ 
    public function setExplanation(string $explanation)
    {
        $this->explanation = $explanation;

        return $this;
    }

    /**
     * Get the value of seo
     *
     * @return  string
     */ 
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * Set the value of seo
     *
     * @param  string  $seo
     *
     * @return  self
     */ 
    public function setSeo(string $seo)
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get the value of termInTitleTag
     *
     * @return  string
     */ 
    public function getTermInTitleTag()
    {
        return $this->termInTitleTag;
    }

    /**
     * Set the value of termInTitleTag
     *
     * @param  string  $termInTitleTag
     *
     * @return  self
     */ 
    public function setTermInTitleTag(string $termInTitleTag)
    {
        $this->termInTitleTag = $termInTitleTag;

        return $this;
    }

    /**
     * Get the value of termInTitleStrTag
     *
     * @return  string
     */ 
    public function getTermInTitleStrTag()
    {
        return $this->termInTitleStrTag;
    }

    /**
     * Set the value of termInTitleStrTag
     *
     * @param  string  $termInTitleStrTag
     *
     * @return  self
     */ 
    public function setTermInTitleStrTag(string $termInTitleStrTag)
    {
        $this->termInTitleStrTag = $termInTitleStrTag;

        return $this;
    }

    /**
     * Get the value of replacePageTitle
     *
     * @return  string
     */ 
    public function getReplacePageTitle()
    {
        return $this->replacePageTitle;
    }

    /**
     * Set the value of replacePageTitle
     *
     * @param  string  $replacePageTitle
     *
     * @return  self
     */ 
    public function setReplacePageTitle(string $replacePageTitle)
    {
        $this->replacePageTitle = $replacePageTitle;

        return $this;
    }

    /**
     * Get the value of termDescriptionTag
     *
     * @return  string
     */ 
    public function getTermDescriptionTag()
    {
        return $this->termDescriptionTag;
    }

    /**
     * Set the value of termDescriptionTag
     *
     * @param  string  $termDescriptionTag
     *
     * @return  self
     */ 
    public function setTermDescriptionTag(string $termDescriptionTag)
    {
        $this->termDescriptionTag = $termDescriptionTag;

        return $this;
    }

    /**
     * Get the value of tags
     *
     * @return  string
     */ 
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set the value of tags
     *
     * @param  string  $tags
     *
     * @return  self
     */ 
    public function setTags(string $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get the value of published
     *
     * @return  string
     */ 
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set the value of published
     *
     * @param  string  $published
     *
     * @return  self
     */ 
    public function setPublished(string $published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get the value of start
     *
     * @return  string
     */ 
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set the value of start
     *
     * @param  string  $start
     *
     * @return  self
     */ 
    public function setStart(string $start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get the value of stop
     *
     * @return  string
     */ 
    public function getStop()
    {
        return $this->stop;
    }

    /**
     * Set the value of stop
     *
     * @param  string  $stop
     *
     * @return  self
     */ 
    public function setStop(string $stop)
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * Get the value of pid
     */ 
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set the value of pid
     *
     * @return  self
     */ 
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get the value of addImage
     *
     * @return  string
     */ 
    public function getAddImage()
    {
        return $this->addImage;
    }

    /**
     * Set the value of addImage
     *
     * @param  string  $addImage
     *
     * @return  self
     */ 
    public function setAddImage(string $addImage)
    {
        $this->addImage = $addImage;

        return $this;
    }

    /**
     * Get the value of singleSRC
     *
     * @return  string
     */ 
    public function getSingleSRC()
    {
        if($this->singleSRC === null) {
            return null;
        }
        return stream_get_contents($this->singleSRC);
    }

    /**
     * Set the value of singleSRC
     *
     * @param  string  $singleSRC
     *
     * @return  self
     */ 
    public function setSingleSRC(string $singleSRC)
    {
        $this->singleSRC = $singleSRC;

        return $this;
    }

    /**
     * Get the value of size
     *
     * @return  string
     */ 
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the value of size
     *
     * @param  string  $size
     *
     * @return  self
     */ 
    public function setSize(string $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get the value of floating
     *
     * @return  string
     */ 
    public function getFloating()
    {
        return $this->floating;
    }

    /**
     * Set the value of floating
     *
     * @param  string  $floating
     *
     * @return  self
     */ 
    public function setFloating(string $floating)
    {
        $this->floating = $floating;

        return $this;
    }

    /**
     * Get the value of imagemargin
     *
     * @return  string
     */ 
    public function getImagemargin()
    {
        return $this->imagemargin;
    }

    /**
     * Set the value of imagemargin
     *
     * @param  string  $imagemargin
     *
     * @return  self
     */ 
    public function setImagemargin(string $imagemargin)
    {
        $this->imagemargin = $imagemargin;

        return $this;
    }

    /**
     * Get the value of fullsize
     *
     * @return  string
     */ 
    public function getFullsize()
    {
        return $this->fullsize;
    }

    /**
     * Set the value of fullsize
     *
     * @param  string  $fullsize
     *
     * @return  self
     */ 
    public function setFullsize(string $fullsize)
    {
        $this->fullsize = $fullsize;

        return $this;
    }

    /**
     * Get the value of overwriteMeta
     *
     * @return  string
     */ 
    public function getOverwriteMeta()
    {
        return $this->overwriteMeta;
    }

    /**
     * Set the value of overwriteMeta
     *
     * @param  string  $overwriteMeta
     *
     * @return  self
     */ 
    public function setOverwriteMeta(string $overwriteMeta)
    {
        $this->overwriteMeta = $overwriteMeta;

        return $this;
    }
}
