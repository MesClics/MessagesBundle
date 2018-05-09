<?php

namespace MC\MessagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MC\UserBundle\Entity\User;

/**
 * Message
 *
 * @ORM\Table(name="mesclics_message")
 * @ORM\Entity(repositoryClass="MC\MessagesBundle\Repository\MessageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;
    
    /**
     * @ORM\ManyToMany(targetEntity="\MC\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="mesclics_readmessage_user")
     */
    private $readers;

    /**
     * @ORM\ManyToOne(targetEntity="\MC\UserBundle\Entity\User", cascade={"persist"})
     */
    private $author;

    /**
     * @ORM\ManyToMany(targetEntity="\MC\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="mesclics_receivedmessage_user")
     */
    private $recipients;

    /**
     * @ORM\OneToOne(targetEntity="\MC\MessagesBundle\Entity\Message")
     */
    private $parent;

    /**
     * @ORM\Column(name="draft", type="boolean")
     */
    private $draft;

    /**
     * @ORM\Column(name="creation_date", type="datetime")
     */
    private $creationDate;

    private $widget;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Message
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set draft
     *
     * @param boolean $draft
     *
     * @return Message
     */
    public function setDraft($draft)
    {
        $this->draft = $draft;

        return $this;
    }

    /**
     * Get draft
     *
     * @return boolean
     */
    public function isDraft()
    {
        return $this->draft;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Message
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @ORM\PrePersist
     */
    public function saveCreationDate(){
        $this->setCreationDate(new \DateTime());
    }


    /**
     * Set author
     *
     * @param \MC\UserBundle\Entity\User $author
     *
     * @return Message
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Get author
     *
     * @return \MC\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add recipient
     *
     * @param \MC\UserBundle\Entity\User $recipient
     *
     * @return Message
     */
    public function addRecipient(User $recipient)
    {
        $this->recipients[] = $recipient;

        return $this;
    }

    /**
     * Remove recipient
     *
     * @param \MC\UserBundle\Entity\User $recipient
     */
    public function removeRecipient(User $recipient)
    {
        $this->recipients->removeElement($recipient);
    }

    /**
     * Get recipients
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecipients()
    {
        return $this->recipients;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->readers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->recipients = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Add reader
     *
     * @param \MC\UserBundle\Entity\User $reader
     *
     * @return Message
     */
    public function addReader(\MC\UserBundle\Entity\User $reader)
    {
        $this->readers[] = $reader;

        return $this;
    }

    /**
     * Remove reader
     *
     * @param \MC\UserBundle\Entity\User $reader
     */
    public function removeReader(\MC\UserBundle\Entity\User $reader)
    {
        $this->readers->removeElement($reader);
    }

    /**
     * Get readers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReaders()
    {
        return $this->readers;
    }

    /**
     * Set parent
     *
     * @param \MC\MessagesBundle\Entity\Message $parent
     *
     * @return Message
     */
    public function setParent(\MC\MessagesBundle\Entity\Message $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \MC\MessagesBundle\Entity\Message
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function hasBeenRead(User $user){
        return $this->getReaders()->contains($user);
    }
}
