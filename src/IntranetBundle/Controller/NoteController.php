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

class NoteController extends Controller
{
    public function indexAction()
    {
        $listNotes = $this->getDoctrine()
        ->getManager()
        ->getRepository('IntranetBundle:Note')
        ->findAll()
      ;

    // Et modifiez le 2nd argument pour injecter notre liste
    return $this->render('IntranetBundle:Note:index.html.twig', array(
      'listNotes' => $listNotes
    ));

    }

    public function viewAction($id)
   {
     // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('IntranetBundle:Note')
    ;

    // On récupère l'entité correspondante à l'id $id
    $note = $repository->find($id);

    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $note) {
      throw new NotFoundHttpException("La note d'id ".$id." n'existe pas.");
    }

    // Le render ne change pas, on passait avant un tableau, maintenant un objet
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

$note->setMatiere($matiere);

     $form   = $this->get('form.factory')->create(NoteType::class, $note);

      //$form->get('student')->setData($user);
      //$form->get('matiere')->setData($matiere);
      //var_dump($note);die;

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {


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
     // Récupération d'une annonce déjà existante, d'id $id.
        $note = $this->getDoctrine()
          ->getManager()
          ->getRepository('IntranetBundle:Note')
          ->find($id)
        ;

        // Et on construit le formBuilder avec cette instance de l'annonce, comme précédemment
        $form = $this->get('form.factory')->createBuilder(FormType::class, $note)
        ->add('value',     TextType::class)
        ->add('comment',   TextareaType::class)
        ->add('save',      SubmitType::class)
        ->getForm();
      ;
      // Si la requête est en POST
      if ($request->isMethod('POST')) {
        // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid()) {
          // On enregistre notre objet $advert dans la base de données, par exemple
          $em = $this->getDoctrine()->getManager();
          $em->persist($note);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Note bien enregistrée.');

          // On redirige vers la page de visualisation de l'annonce nouvellement créée
          return $this->redirectToRoute('note_view', array('id' => $note->getId()));
        }
      }

      // À ce stade, le formulaire n'est pas valide car :
      // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
      // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
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
}
