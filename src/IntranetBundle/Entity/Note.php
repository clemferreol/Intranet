<?php

namespace IntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Note
 *
 * @ORM\Table(name="note")
 * @ORM\Entity(repositoryClass="IntranetBundle\Repository\NoteRepository")
 */
class Note
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
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
       * @ORM\ManyToMany(targetEntity="IntranetBundle\Entity\Matiere",inversedBy="notes", cascade={"persist"})
       * @ORM\JoinColumn(name="matiere_id", referencedColumnName="id")
       */
    private $matiere;

    /**
       * @ORM\ManyToMany(targetEntity="IntranetUserBundle\Entity\User",inversedBy="notes", cascade={"persist"})
       * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
       */
    private $student;

    public function __construct()
  {
     $this->matiere = new ArrayCollection();
     $this->student = new ArrayCollection();
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
     * Set value
     *
     * @param float $value
     *
     * @return Note
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Note
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
       * Set matiere
       *
       * @param \Doctrine\Common\Collection\ArrayCollection $matiere
       * @return Note
       */
      public function setMatiere(\IntranetBundle\Entity\Matiere $matiere = null)
      {
          $this->matiere->add($matiere);

          return $this;
      }

      /**
       * Get matiere
       * @return (\IntranetBundle\Entity\Matiere[]|ArrayCollection
       */
      public function getMatiere()
      {
          return $this->matiere;
      }

      /**
         * Set student
         *
         * @param \Doctrine\Common\Collection\ArrayCollection $student
         * @return Note
         */
        public function setStudent(\IntranetUserBundle\Entity\User $student = null)
        {
            $this->student->add($student);

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
