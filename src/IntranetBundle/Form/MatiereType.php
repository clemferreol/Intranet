<?php

namespace IntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use IntranetUserBundle\Repository\UserRepository;

class MatiereType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
      ->add('date',      DateType::class)
      ->add('title',     TextType::class)
      ->add('teacher',    EntityType::class, array(
        'class' => 'IntranetUserBundle:User',
        'choice_label' => 'username',
        'query_builder' => function(UserRepository $er) { //query builder lance une requete dans le repository UserRepository
            return $er->createQueryBuilder('u')  //et ramène les entités qui correspondent a la requete.
            ->where('u.roles like :role')
            ->setParameter('role', '%"ROLE_ADMIN"%')
            ->orderBy('u.username', 'ASC');
                        },
      ))
      ->add('content',   TextareaType::class)

      ->add('save',      SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IntranetBundle\Entity\Matiere'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'intranetbundle_matiere';
    }


}
