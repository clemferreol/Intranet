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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use IntranetBundle\Transformer\MatiereTransformer;
use IntranetUserBundle\Transformer\UserTransformer;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use IntranetUserBundle\Repository\UserRepository;



class ChangeUserRoleType extends AbstractType{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $permissions = array(
          'ROLE_USER'        => 'First role',
          'ROLE_ADMIN'     => 'Second role',
          'ROLE_SUPER_ADMIN' => 'Third role'
      );

      $builder
          ->add('username',      EntityType::class,
              array(
                  'class'    => 'IntranetUserBundle:User',
                  'choice_label' => 'username',
              )
          )
          ->add('role', ChoiceType::class,
              array(
                  'choices' => $permissions,
              )
          )
          ->add(
              'save',
              SubmitType::class
          );
  }
  public function configureOptions(OptionsResolver $resolver)
  {
      $resolver->setDefaults(array(
          'data_class' => 'IntranetUserBundle\Entity\User',
      ));

  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
      return 'intranetuserbundle_user';
  }


}
