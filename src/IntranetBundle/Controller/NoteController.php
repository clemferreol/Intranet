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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class NoteController extends Controller
{
    public function indexAction()
    {
        $listNotes = $this->getDoctrine()
        ->getManager()
        ->getRepository('IntranetBundle:Note')
        ->findAll()
      ;


    return $this->render('IntranetBundle:Note:index.html.twig', array(
      'listNotes' => $listNotes
    ));

    }

    public function viewAction($id)
   {
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('IntranetBundle:Note')
    ;


    $note = $repository->find($id);


    if (null === $note) {
      throw new NotFoundHttpException("La note d'id ".$id." n'existe pas.");
    }


    return $this->render('IntranetBundle:Note:view.html.twig', array(
      'note' => $note
    ));

   }
  /**
   * @Security("has_role('ROLE_NOTE')")
   */
   public function addAction(Request $request, $id, $username)
   {
     $em = $this->getDoctrine()->getManager();
     $userManager = $this->get('fos_user.user_manager');
     $user = $userManager->findUserByUsername($username);

     $matiere = $em->getRepository('IntranetBundle:Matiere')->find($id);

     $note = new Note();

     $form   = $this->createForm(NoteType::class, $note, array(
       'entity_manager' => $em,
       'userManager' => $userManager
     ));

      $form->get('student')->setData($user);
      $form->get('matiere')->setData($matiere);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

      $user->setNotes($note);
      $matiere->setNotes($note);


      $userManager->updateUser($user, false);
      $em->persist($matiere);
      $em->persist($note);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Note bien enregistrée.');

      return $this->redirectToRoute('note_view', array('id' => $note->getId()));
    }

      return $this->render('IntranetBundle:Note:add.html.twig', array(
      'form' => $form->createView(),
    ));

   }
   /**
    * @Security("has_role('ROLE_NOTE')")
    */
   public function editAction($id, Request $request)
   {
        $note = $this->getDoctrine()
          ->getManager()
          ->getRepository('IntranetBundle:Note')
          ->find($id)
        ;

        $form = $this->get('form.factory')->createBuilder(FormType::class, $note)
        ->add('value',     TextType::class)
        ->add('comment',   TextareaType::class)
        ->add('save',      SubmitType::class)
        ->getForm();
      ;
      if ($request->isMethod('POST')) {

        $form->handleRequest($request);


        if ($form->isValid()) {

          $em = $this->getDoctrine()->getManager();
          $em->persist($note);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Note bien enregistrée.');

          return $this->redirectToRoute('note_view', array('id' => $note->getId()));
        }
      }

      return $this->render('IntranetBundle:Note:add.html.twig', array(
        'form' => $form->createView(),
      ));

   }

   /**
    * @Security("has_role('ROLE_SUPER_ADMIN')")
    */
   public function deleteAction(Request $request, $id)
   {

     $em = $this->getDoctrine()->getManager();

    $note = $em->getRepository('IntranetBundle:Note')->find($id);

    if (null === $note) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->get('form.factory')->create();

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em->remove($note);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "La note a bien été supprimée.");

      return $this->redirectToRoute('note_home');
    }

    return $this->render('IntranetBundle:Note:delete.html.twig', array(
      'note' => $note,
      'form'   => $form->createView(),
    ));
   }

   public function listNotesForUserAction (Request $request){
      $user = $this->getUser();
      $em = $this->getDoctrine()->getManager();
      $repo = $em->getRepository('IntranetBundle:Note');
      $listAllNotes = $em->getRepository('IntranetBundle:Note')->findAll();
      $listNotes = array();


      if($user->hasrole('ROLE_ADMIN')){


      $repo = $this->getDoctrine()->getManager()->getRepository('IntranetBundle:Matiere');
      $user = $this->getUser();

      if($user->hasrole('ROLE_ADMIN')){
       $query = $repo->createQueryBuilder('m')
       ->where('m.teacher = :user')
       ->setParameter('user', $user)
       ->orderBy('m.title', 'ASC')
     ->getQuery();

      $listMatieres = $query->getResult();

      foreach($listMatieres as $matiere){
       array_push($listNotes, $matiere->getNotes());
      }

       return $this->render('IntranetBundle:Note:listNotes.html.twig', array(
      'listNotes' => $listNotes,
    ));
  } elseif($user->hasrole('ROLE_USER')){
        foreach($listAllNotes as $note){
       $students = $note->getStudent();
        foreach($students as $student){
          if($student == $user){
            array_push($listNotes, $note);
          }
        }

      }

      return $this->render('IntranetBundle:Note:listNotes.html.twig', array(
      'listNotes' => $listNotes,
    ));
      }



    }
  }
}
