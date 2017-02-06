<?php

namespace IntranetUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="IntranetUserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
     * @ORM\ManyToMany(targetEntity="IntranetBundle\Entity\Matiere",mappedBy="student", cascade={"persist"})
     * @ORM\JoinTable(name="user_matiere",
     * joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)})

     */

  private $matiere;
  /*
   * ORM\@OneToMany(targetEntity="IntranetBundle\Entity\Matiere", mappedBy="teacher")
   *
   */
  private $matiereTeach;

  /**
     * @ORM\ManyToMany(targetEntity="IntranetBundle\Entity\Note",mappedBy="student", cascade={"persist"})
     * @ORM\JoinTable(name="user_matiere_note",
     * joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)})
     */

  private $notes;

  public function __construct()
{
   $this->matiere = new ArrayCollection();
   $this->notes = new ArrayCollection();
   $this->matiereTeach = new ArrayCollection();

}

/**
 * Get matiere
 * @return integer
 */
public function getId()
{
  return $this->id;
}

  /**
     * Set matiere
     *
     * @param \Doctrine\Common\Collection\ArrayCollection $matiere
     * @return User
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
       * Set matiere
       *
       * @param \Doctrine\Common\Collection\ArrayCollection $matiereTeach
       * @return User
       */
      public function setMatiereForTeacher(\IntranetBundle\Entity\Matiere $matiereTeach = null)
      {
          $this->matiereTeach->add($matiereTeach);

          return $this;
      }

      /**
       * Get $matiereTeach
       * @return (\IntranetBundle\Entity\Matiere[]|ArrayCollection
       */
      public function getMatiereForTeacher()
      {
          return $this->matiereTeach;
      }

    /**
       * Set Notes
       *
       * @param \Doctrine\Common\Collection\ArrayCollection $notes
       * @return User
       */
      public function setNotes(\IntranetBundle\Entity\Note $notes = null)
      {
          $this->notes->add($notes);

          return $this;
      }

      /**
       * Get notes
       * @return (\IntranetBundle\Entity\Note[]|ArrayCollection
       */
      public function getNotes()
      {
          return $this->notes;
      }

}
