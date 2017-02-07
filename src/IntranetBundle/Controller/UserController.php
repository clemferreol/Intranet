<?php

namespace IntranetBundle\Controller;

use IntranetBundle\Entity\Note;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IntranetBundle\Form\NoteType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use IntranetBundle\Form\ChangeUserRoleType;

class UserController extends Controller{
  public function editRoleAction(Request $request, $username)
  {

    $userManager = $this->container->get('fos_user.user_manager');
    $user = $userManager->findUserByUsername($username);

      $formEditUser = $this->createForm(ChangeUserRoleType::class, $user);

      $formEditUser->get('entity')->setData($user);

      $formEditUser->handleRequest($request);
      if ($formEditUser->isValid()) {

          // Changing the role of the user
          $user->setRoles(array($selectedUser['role']));
          // Updating the user
          $userManager->updateUser($user);
      }

      return $this->render(
          'ReportingAdminBundle:Admin:index.html.twig',
          array(
              'editUserForm' => $formEditUser->createView()
          )
      );
  }

  public function listUsersAction (Request $request){
    $userManager = $this->get('fos_user.user_manager');
     $listUsers = $userManager->findUsers();


     return $this->render('IntranetBundle:User:listUsers.html.twig', array(
       'listUsers' => $listUsers
     ));
  }

}
