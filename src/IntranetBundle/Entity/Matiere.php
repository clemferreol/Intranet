<?php

namespace IntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Matiere
 *
 * @ORM\Table(name="matiere")
 * @ORM\Entity(repositoryClass="IntranetBundle\Repository\MatiereRepository")
 */
class Matiere
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IntranetUserBundle\Entity\User", inversedBy="matiereTeach")
     * @ORM\JoinColumn(name="teach_id", referencedColumnName="id")
     */
    private $teacher;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
       * @ORM\ManyToMany(targetEntity="IntranetUserBundle\Entity\User",inversedBy="matiere")
       * @ORM\JoinTable(name="user_matiere",
       * joinColumns={@ORM\JoinColumn(name="matiere_id", referencedColumnName="id", nullable=true)})
       */
    private $student;

    /**
       * @ORM\ManyToMany(targetEntity="IntranetBundle\Entity\Note",mappedBy="matiere", cascade={"persist"})
       * @ORM\JoinTable(name="user_matiere_note",
       * joinColumns={@ORM\JoinColumn(name="matiere_id", referencedColumnName="id", nullable=true)})
       */
    private $notes;

    public function __construct()
  {
    $this->date         = new \Datetime();
    $this->student = new ArrayCollection();
    $this->notes = new ArrayCollection();
  }


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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Matiere
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Matiere
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
     * Set author
     *
     * @param string $author
     *
     * @return Matiere
     */
    public function setTeacher($author)
    {
        $this->teacher = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Matiere
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
       * Set student
       *
       * @param \Doctrine\Common\Collection\ArrayCollection $student
       * @return Matiere
       */
      public function setStudent(\IntranetUserBundle\Entity\User $student = null)
      {
          $this->student->add($student);

          return $this;
      }

      /**
       * Get notes
       * @return (\IntranetUserBundle\Entity\User[]|ArrayCollection
       */
      public function getNotes()
      {
          return $this->notes;
      }

      /**
         * Set notes
         *
         * @param \Doctrine\Common\Collection\ArrayCollection $notes
         * @return Matiere
         */
        public function setNotes(\IntranetBundle\Entity\Note $notes = null)
        {
            $this->notes->add($notes);

            return $this;
        }

        /**
         * Get student
         * @return (\IntranetUserBundle\Entity\User[]|ArrayCollection
         */
        public function getStudent()
        {
            return $this->student;
        }
}
