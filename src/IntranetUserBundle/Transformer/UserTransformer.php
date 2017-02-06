<?php
namespace IntranetUserBundle\Transformer;


use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use FOS\UserBundle\Doctrine\UserManager;

class UserTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($userid)
    {
      var_dump($userid);
      // no issue number? It's optional, so that's ok
      if (!$userid) {
          return;
      }

      $user = $this->manager

          // query for the issue with this id
          ->findUserBy([$userid])
      ;

      if (null === $userid) {
          // causes a validation error
          // this message is not shown to the user
          // see the invalid_message option
          throw new TransformationFailedException(sprintf(
              'A user with number "%s" does not exist!',
              $userid
          ));
      }

      return $user;

    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $issueNumber
     * @return Issue|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($user)
    {
      if (null === $user) {
          return '';
      }


      return $user->getId();
    }
}
