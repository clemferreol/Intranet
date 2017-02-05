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

class MatiereController extends Controller
{
    public function indexAction()
    {
        $listMatieres = $this->getDoctrine()
        ->getManager()
        ->getRepository('IntranetBundle:Matiere')
        ->findAll()
      ;
// Notre liste d'annonce en dur
    /*$listMatieres = array(
      array(
        'title'   => 'Symfony',
        'id'      => 1,
        'author'  => 'Kyllian',
        'content' => 'Cours de symfony blablabla',
        'date'    => new \Datetime()),
      array(
        'title'   => 'PHP',
        'id'      => 2,
        'author'  => 'Julien',
        'content' => 'Cours de PHP blablabla',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Javascript',
        'id'      => 3,
        'author'  => 'Cyril',
        'content' => 'Cours de Javascript Blabla…',
        'date'    => new \Datetime())
    );*/

    // Et modifiez le 2nd argument pour injecter notre liste
    return $this->render('IntranetBundle:Matiere:index.html.twig', array(
      'listMatieres' => $listMatieres
    ));

    }

    public function viewAction($id)
   {
     // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('IntranetBundle:Matiere')
    ;

    // On récupère l'entité correspondante à l'id $id
    $matiere = $repository->find($id);

    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $matiere) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // Le render ne change pas, on passait avant un tableau, maintenant un objet
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
    /*$listMatieres = array(
     array('id' => 2, 'title' => 'Symfony'),
     array('id' => 5, 'title' => 'PHP'),
     array('id' => 9, 'title' => 'Javascript')
   );*/

   return $this->render('IntranetBundle:Matiere:list.html.twig', array(
     // Tout l'intérêt est ici : le contrôleur passe
     // les variables nécessaires au template !
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

        //$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Access Denied!');
     // Récupération d'une annonce déjà existante, d'id $id.
        $matiere = $this->getDoctrine()
          ->getManager()
          ->getRepository('IntranetBundle:Matiere')
          ->find($id)
        ;

        // Et on construit le formBuilder avec cette instance de l'annonce, comme précédemment
        $form = $this->get('form.factory')->createBuilder(FormType::class, $matiere)
        ->add('date',      DateType::class)
        ->add('title',     TextType::class)
        ->add('content',   TextareaType::class)
        ->add('author',    TextType::class)
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
          $em->persist($matiere);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

          // On redirige vers la page de visualisation de l'annonce nouvellement créée
          return $this->redirectToRoute('intranet_view', array('id' => $matiere->getId()));
        }
      }

      // À ce stade, le formulaire n'est pas valide car :
      // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
      // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
      return $this->render('IntranetBundle:Matiere:add.html.twig', array(
        'form' => $form->createView(),
      ));

   }

   /**
    * @Security("has_role('ROLE_SUPER_ADMIN')")
    */

   public function deleteAction(Request $request, $id)
   {

    //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Access Denied !');
     $em = $this->getDoctrine()->getManager();

    $matiere = $em->getRepository('IntranetBundle:Matiere')->find($id);

    if (null === $matiere) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
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

     $user->setMatiere($matiere);
     $userManager->updateUser($user);

     $request->getSession()->getFlashBag()->add('notice', 'Inscription bien enregistrée.');

     return $this->redirectToRoute('intranet_view', array('id' => $matiere->getId()));





   }
}
