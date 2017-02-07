<?php

namespace IntranetBundle\Controller;

use IntranetBundle\Entity\Matiere;
use IntranetUserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IntranetBundle\Form\MatiereType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MatiereController extends Controller
{
    public function indexAction()
    {
      $user = $this->getUser();
      $listMatieres = new ArrayCollection();

      if($user->hasRole('ROLE_ADMIN')){
        $listMatieres = $user->getMatiere();
        return $this->render('IntranetBundle:Matiere:listMatieres.html.twig', array(
          'listMatieres' => $listMatieres
        ));
      }else{
        $listMatieres = $this->getDoctrine()
        ->getManager()
        ->getRepository('IntranetBundle:Matiere')
        ->findAll()
      ;
      return $this->render('IntranetBundle:Matiere:index.html.twig', array(
        'listMatieres' => $listMatieres
      ));
      }

    }

    public function viewAction($id)
   {

    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('IntranetBundle:Matiere')
    ;


    $matiere = $repository->find($id);

    if (null === $matiere) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    return $this->render('IntranetBundle:Matiere:view.html.twig', array(
      'matiere' => $matiere
    ));

   }

   public function listAction()
  {

    $listMatieres = $this->getDoctrine()
      ->getManager()
      ->getRepository('IntranetBundle:Matiere')
    ;

   return $this->render('IntranetBundle:Matiere:list.html.twig', array(
     'listMatieres' => $listMatieres
   ));
  }
  /**
   * @Security("has_role('ROLE_SUPER_ADMIN')")
   */
   public function addAction(Request $request)
   {
     $matiere = new Matiere();
    $form   = $this->get('form.factory')->create(MatiereType::class, $matiere);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($matiere);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      return $this->redirectToRoute('intranet_view', array('id' => $matiere->getId()));
    }

      return $this->render('IntranetBundle:Matiere:add.html.twig', array(
      'form' => $form->createView(),
    ));

   }

   /**
    * @Security("has_role('ROLE_SUPER_ADMIN')")
    */
   public function editAction($id, Request $request)
   {

        $matiere = $this->getDoctrine()
          ->getManager()
          ->getRepository('IntranetBundle:Matiere')
          ->find($id)
        ;

        $form = $this->get('form.factory')->createBuilder(FormType::class, $matiere)
        ->add('date',      DateType::class)
        ->add('title',     TextType::class)
        ->add('content',   TextareaType::class)
        ->add('author',    TextType::class)
        ->add('save',      SubmitType::class)
        ->getForm();
      ;

      if ($request->isMethod('POST')) {

        $form->handleRequest($request);

        if ($form->isValid()) {

          $em = $this->getDoctrine()->getManager();
          $em->persist($matiere);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');


          return $this->redirectToRoute('intranet_view', array('id' => $matiere->getId()));
        }
      }


      return $this->render('IntranetBundle:Matiere:add.html.twig', array(
        'form' => $form->createView(),
      ));

   }

   /**
    * @Security("has_role('ROLE_SUPER_ADMIN')")
    */

   public function deleteAction(Request $request, $id)
   {


     $em = $this->getDoctrine()->getManager();

    $matiere = $em->getRepository('IntranetBundle:Matiere')->find($id);

    if (null === $matiere) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $form = $this->get('form.factory')->create();

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em->remove($matiere);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

      return $this->redirectToRoute('intranet_home');
    }

    return $this->render('IntranetBundle:Matiere:delete.html.twig', array(
      'matiere' => $matiere,
      'form'   => $form->createView(),
    ));
   }

   public function subscribeAction (Request $request, $id){
     $em = $this->getDoctrine()->getManager();
     $userManager = $this->get('fos_user.user_manager');
     $user = $this->getUser();
     $matiere = $em->getRepository('IntranetBundle:Matiere')->find($id);

     $matiere->setStudent($user);
     $user->setMatiere($matiere);
     $em->persist($matiere);
     $userManager->updateUser($user, false);
     $em->flush();

     $request->getSession()->getFlashBag()->add('notice', 'Inscription bien enregistrée.');

     return $this->redirectToRoute('intranet_view', array('id' => $matiere->getId()));
   }

   public function viewListAction (Request $request){

     $repo = $this->getDoctrine()->getManager()->getRepository('IntranetBundle:Matiere');
     $user = $this->getUser();

     if($user->hasrole('ROLE_ADMIN')){
      $query = $repo->createQueryBuilder('m')
      ->where('m.teacher = :user')
      ->setParameter('user', $user)
      ->orderBy('m.title', 'ASC')
    ->getQuery();

     $listMatieres = $query->getResult();


     return $this->render('IntranetBundle:Matiere:listMatieres.html.twig', array(
       'listMatieres' => $listMatieres
     ));
   } elseif($user->hasrole('ROLE_USER')){
        $listMatieres = array();
       $listAllMatieres = $repo->findAll();

       foreach($listAllMatieres as $matiere){
          $listAllStudent = $matiere->getStudent();
          foreach($listAllStudent as $student){
            if($student == $user){
              array_push($listMatieres, $matiere);
            }
          }
       }

     return $this->render('IntranetBundle:Matiere:listMatieres.html.twig', array(
       'listMatieres' => $listMatieres
     ));
     }

   }

   public function listStudentAction (Request $request, $id){
     $user = $this->getUser();
     $em = $this->getDoctrine()->getManager();
     $matiere = $em->getRepository('IntranetBundle:Matiere')->find($id);
     $listStudent = $matiere->getStudent();

     return $this->render('IntranetBundle:Matiere:listStudent.html.twig', array(
       'listStudent' => $listStudent, 'id' => $id
     ));
   }

   public function viewTeachersAction (Request $request, $id){
     $userManager = $this->get('fos_user.user_manager');
     $listUsers = $userManager->findUsers();
     $listTeacher = array();

     foreach ($listUsers as $user){
       if($user->hasRole('ROLE_ADMIN')){
         array_push($listTeacher, $user);
       }
     }

     return $this->render('IntranetBundle:Matiere:listTeacher.html.twig', array(
       'listTeacher' => $listTeacher
     ));


   }
}
