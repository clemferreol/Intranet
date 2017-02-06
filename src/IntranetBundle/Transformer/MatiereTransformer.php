<?php
namespace IntranetBundle\Transformer;

use IntranetBundle\Entity\Matiere;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class MatiereTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($matiere)
    {
        if (null === $matiere) {
            return '';
        }

        return $matiere->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $issueNumber
     * @return Issue|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($matiereId)
    {
        // no issue number? It's optional, so that's ok
        if (!$matiereId) {
            return;
        }

        $matiereId = $this->manager
            ->getRepository('IntranetBundle:Matiere')
            // query for the issue with this id
            ->find($matiereId)
        ;

        if (null === $matiere) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'A matter with number "%s" does not exist!',
                $matiereId
            ));
        }

        return $matiere;
    }
}
