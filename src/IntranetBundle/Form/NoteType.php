<?php

namespace IntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use IntranetBundle\Transformer\MatiereTransformer;
use IntranetUserBundle\Transformer\UserTransformer;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use IntranetUserBundle\Repository\UserRepository;



class NoteType extends AbstractType
{

  private $entityManager;
  private $userManager;


public function __construct(EntityManager $manager, UserManager $userManager)

  {
      $this->entityManager = $manager;
      $this->userManager = $userManager;
  }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
      ->add('student', EntityType::class, array(
        'class' => 'IntranetUserBundle:User',
        'choice_label' => 'username',
        'em' => $options['entity_manager'],

      ))
      ->add('matiere', EntityType::class, array(
        'class' => 'IntranetBundle:Matiere',
        'choice_label' => 'title',
        'em' => $options['entity_manager'],
      ))
      ->add('value',     TextType::class)
      ->add('comment',   TextareaType::class)
      ->add('save',      SubmitType::class);

      //$builder->get('student')->addModelTransformer(new UserTansformer($this->userManager));
      //$builder->get('Matiere')->addModelTransformer(new MatiereTansformer($this->manager));
      /*'em' => $options['userManager'],
      $options['entity_manager'],
*/
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IntranetBundle\Entity\Note',
        ));
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('userManager');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'intranetbundle_note';
    }


}
