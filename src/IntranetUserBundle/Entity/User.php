<?php
// src/OC/UserBundle/Entity/User.php

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
     * @ORM\ManyToMany(targetEntity="IntranetBundle\Entity\Matiere",inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(name="matiere_id", referencedColumnName="id")
     * @Assert\Type(type="IntranetBundle\Entity\Matiere")
     * @Assert\Valid()
     */

  private $matiere;

  public function __construct()
{
   $this->matiere = new ArrayCollection();
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

}
