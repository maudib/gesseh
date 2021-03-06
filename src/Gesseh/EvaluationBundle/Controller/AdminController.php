<?php

/**
 * This file is part of GESSEH project
 *
 * @author: Pierre-François ANGRAND <gesseh@medlibre.fr>
 * @copyright: Copyright 2013-2016 Pierre-François Angrand
 * @license: GPLv3
 * See LICENSE file or http://www.gnu.org/licenses/gpl.html
 */

namespace Gesseh\EvaluationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Symfony\Component\HttpFoundation\Response;
use Gesseh\EvaluationBundle\Entity\EvalForm,
    Gesseh\EvaluationBundle\Form\EvalFormType,
    Gesseh\EvaluationBundle\Form\EvalFormHandler;
use Gesseh\EvaluationBundle\Entity\EvalSector,
    Gesseh\EvaluationBundle\Form\EvalSectorType,
    Gesseh\EvaluationBundle\Form\EvalSectorHandler;
use Gesseh\EvaluationBundle\Entity\Evaluation,
    Gesseh\EvaluationBundle\Form\ModerationType,
    Gesseh\EvaluationBundle\Form\ModerationHandler;

/**
 * Admin controller.
 *
 * @Route("/admin/evaluation")
 */
class AdminController extends Controller
{
  /**
   * @Route("/", name="GEval_AIndex")
   * @Template()
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();
    $eval_forms = $em->getRepository('GessehEvaluationBundle:EvalForm')->findAll();
    $sectors = $em->getRepository('GessehEvaluationBundle:EvalSector')->getAllByForm($eval_forms);

    return array(
      'eval_forms'     => $eval_forms,
      'eval_form_id'   => null,
      'eval_form_form' => null,
      'sectors'        => $sectors,
      'sector_form'    => null,
      'form_id'        => null,
    );
  }

  /**
   * Displays a form to create a new eval_form entity.
   *
   * @Route("/new", name="GEval_ANew")
   * @Template("GessehEvaluationBundle:Admin:form.html.twig")
   */
  public function newFormAction()
  {
    $em = $this->getDoctrine()->getManager();

    $eval_form = new EvalForm();
    $form = $this->createForm(new EvalFormType(), $eval_form);
    $formHandler = new EvalFormHandler($form, $this->get('request'), $em);

    if ( $formHandler->process() ) {
      $this->get('session')->getFlashBag()->add('notice', 'Formulaire d\'évaluation "' . $eval_form->getName() . '" enregistré.');

      return $this->redirect($this->generateUrl('GEval_AIndex'));
    }

    return array(
      'form' => $form->createView(),
    );
  }

   /**
   * Displays a form to edit an existing eval_form entity.
   *
   * @Route("/{id}/edit", name="GEval_AEdit", requirements={"id" = "\d+"})
   * @Template("GessehEvaluationBundle:Admin:form.html.twig")
   */
  public function editFormAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $eval_form = $em->getRepository('GessehEvaluationBundle:EvalForm')->find($id);

    if (!$eval_form)
      throw $this->createNotFoundException('Unable to find eval_form entity.');

    $form = $this->createForm(new EvalFormType(), $eval_form);
    $formHandler = new EvalFormHandler($form, $this->get('request'), $em);

    if ( $formHandler->process() ) {
      $this->get('session')->getFlashBag()->add('notice', 'Formulaire d\'évaluation "' . $eval_form->getName() . '" modifié.');

      return $this->redirect($this->generateUrl('GEval_AIndex'));
    }

    return array(
      'form' => $form->createView(),
    );
  }

  /**
   * Deletes a eval_form entity.
   *
   * @Route("/{id}/delete", name="GEval_ADelete", requirements={"id" = "\d+"}))
   */
  public function deleteFormAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $eval_form = $em->getRepository('GessehEvaluationBundle:EvalForm')->find($id);

    if (!$eval_form)
      throw $this->createNotFoundException('Unable to find eval_form entity.');

    foreach ($eval_form->getCriterias() as $criteria) {
      if ($evaluations = $em->getRepository('GessehEvaluationBundle:Evaluation')->findByEvalCriteria($criteria->getId())) {
        foreach ($evaluations as $evaluation) {
          $em->remove($evaluation);
        }
      }
    }

    $em->remove($eval_form);
    $em->flush();

    $this->get('session')->getFlashBag()->add('notice', 'Formulaire d\'évaluation "' . $eval_form->getName() . '" supprimé.');

    return $this->redirect($this->generateUrl('GEval_AIndex'));
  }

  /**
   * Deletes a eval_criteria entity.
   *
   * @Route("/criteria/{id}/delete", name="GEval_ADeleteCriteria", requirements={"id" = "\d+"}))
   */
  public function deleteCriteriaAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $criteria = $em->getRepository('GessehEvaluationBundle:EvalCriteria')->find($id);

    if (!$criteria)
      throw $this->createNotFoundException('Unable to find eval_criteria entity.');

    $em->remove($criteria);
    $em->flush();

    $this->get('session')->getFlashBag()->add('notice', 'Critère d\'évaluation "' . $criteria->getName() . '" supprimé.');

    return $this->redirect($this->generateUrl('GEval_AIndex'));
  }

  /**
   * Display a form to add a sector to an eval_form entity
   *
   * @Route("/{form_id}/sector/add", name="GEval_ASectorAdd", requirements={"form_id" = "\d+"})
   * @Template("GessehEvaluationBundle:Admin:index.html.twig")
   */
  public function addSectorAction($form_id)
  {
    $em = $this->getDoctrine()->getManager();
    $eval_forms = $em->getRepository('GessehEvaluationBundle:EvalForm')->findAll();
    $sectors = $em->getRepository('GessehEvaluationBundle:EvalSector')->getAllByForm($eval_forms);
    $exclude_sectors = $em->getRepository('GessehEvaluationBundle:EvalSector')->getAssignedSectors();
    $eval_form = $em->getRepository('GessehEvaluationBundle:EvalForm')->find($form_id);

    $eval_sector = new EvalSector();
    $form = $this->createForm(new EvalSectorType($exclude_sectors, $eval_form), $eval_sector);
    $formHandler = new EvalSectorHandler($form, $this->get('request'), $em, $eval_form);

    if ( $formHandler->process() ) {
      $this->get('session')->getFlashBag()->add('notice', 'Relation "' . $eval_sector->getSector() . " : " . $eval_sector->getForm() . '" enregistrée.');

      return $this->redirect($this->generateUrl('GEval_AIndex'));
    }

    return array(
      'eval_forms'     => $eval_forms,
      'eval_form_id'   => null,
      'eval_form_form' => null,
      'sectors'        => $sectors,
      'sector_form'    => $form->createView(),
      'form_id'        => $form_id,
    );
  }

  /**
   * Deletes a eval_sector entity.
   *
   * @Route("/sector/{id}/delete", name="GEval_ASectorDelete", requirements={"id" = "\d+"}))
   */
  public function deleteSectorAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $eval_sector = $em->getRepository('GessehEvaluationBundle:EvalSector')->find($id);

    if (!$eval_sector)
      throw $this->createNotFoundException('Unable to find eval_sector entity.');

    $em->remove($eval_sector);
    $em->flush();

    $this->get('session')->getFlashBag()->add('notice', 'Relation "' . $eval_sector->getSector() . " : " . $eval_sector->getForm() . '" supprimée.');

    return $this->redirect($this->generateUrl('GEval_AIndex'));
  }

  /**
   * Affiche les evaluations textuelles pour modération
   *
   * @Route("/moderation/", name="GEval_ATextIndex")
   * @Template()
   */
  public function textIndexAction()
  {
    $em = $this->getDoctrine()->getManager();
    $paginator = $this->get('knp_paginator');
    $evaluation_query = $em->getRepository('GessehEvaluationBundle:Evaluation')->getToModerate();
    $evaluations = $paginator->paginate($evaluation_query, $this->get('request')->query->get('page', 1), 20);

    return array(
      'evaluations' => $evaluations,
    );
  }

    /**
     * Valide une évaluation textuelle
     *
     * @Route("/moderation/{id}/valid", name="GEval_AModerationValid", requirements={"id" = "\d+"})
     */
    public function validModeration($id)
    {
        $em = $this->getDoctrine()->getManager();
        $um = $this->container->get('fos_user.user_manager');
        $user = $um->findUserByUsername($this->get('security.token_storage')->getToken()->getUsername());
        $evaluation = $em->getRepository('GessehEvaluationBundle:Evaluation')->getToModerate($id);

        if(!$evaluation)
          throw $this->createNotFoundException('Impossible de trouver l\'évaluation');

        $evaluation->setValidated(true);
        $evaluation->setModerator($user);
        $em->persist($evaluation);
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Évaluation validée.');

        return $this->redirect($this->generateUrl('GEval_ATextIndex'));
    }

    /**
     * Invalide une évaluation
     *
     * @Route("/moderation/{id}/delete", name="GEval_AModerationInvalid", requirements={"id" = "\d+"})
     */
    public function textDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $um = $this->container->get('fos_user.user_manager');
        $user = $um->findUserByUsername($this->get('security.token_storage')->getToken()->getUsername());
        $evaluation = $em->getRepository('GessehEvaluationBundle:Evaluation')->getToModerate($id);

        if(!$evaluation)
          throw $this->createNotFoundException('Impossible de trouver l\'évaluation');

        $evaluation->setValidated(false);
        $evaluation->setModerated(true);
        $evaluation->setModerator($user);
        $em->persist($evaluation);
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Évaluation supprimée.');

        return $this->redirect($this->generateUrl('GEval_ATextIndex'));
    }

    /**
     * Modère une évaluation
     *
     * @Route("/moderation/{id}/edit", name="GEval_AModerationEdit", requirements={"id" = "\d+"})
     * @Template()
     */
    public function moderationEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $um = $this->container->get('fos_user.user_manager');
        $user = $um->findUserByUsername($this->get('security.token_storage')->getToken()->getUsername());
        $evaluation = $em->getRepository('GessehEvaluationBundle:Evaluation')->getToModerate($id);

        if (!$evaluation)
            throw $this->createNotFoundException('Unable to find evaluation entity.');

        $form = $this->createForm(new ModerationType($evaluation));
        $formHandler = new ModerationHandler($form, $this->get('request'), $em, $evaluation, $user);

        if ( $formHandler->process() ) {
            $this->get('session')->getFlashBag()->add('notice', 'Évaluation modérée.');

            return $this->redirect($this->generateUrl('GEval_ATextIndex'));
        }
        return array(
            'evaluation' => $evaluation,
            'form'       => $form->createView(),
        );
    }

    /**
     * Exporte les évaluations en PDF
     *
     * @Route("/export", name="GEval_APdfExport")
     */
    public function pdfExportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pm = $this->container->get('kdb_parameters.manager');
        $hospitals = $em->getRepository('GessehCoreBundle:Hospital')->getAll();
        $limit['date'] = date('Y-m-d H:i:s', strtotime('-' . $pm->findParamByName('eval_limit')->getValue() . ' year'));
        $limit['role'] = null;
        $pdf = $this->get("white_october.tcpdf")->create();

        foreach ($hospitals as $hospital) {
            foreach ($hospital->getDepartments() as $department) {
                $eval[$department->getId()] = $em->getRepository('GessehEvaluationBundle:Evaluation')->getByDepartment($department->getId(), $limit);
            }
        }

        $content = $this->renderView('GessehEvaluationBundle:Admin:pdfExport.html.twig', array(
            'eval'        => $eval,
            'hospitals'   => $hospitals,
        ));

        $pdf->SetTitle($pm->findParamByName('title')->getValue() . ' : évaluations');
        $pdf->AddPage();
        $pdf->writeHTML($content);
        $pdf->lastPage();

        return new Response(
            $pdf->Output('Evaluations.pdf'),
            200,
            array(
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="Evaluations.pdf"',
            )
        );
    }

    /**
     * Envoie un mail de rappel aux étudiants n'ayant pas évalué tous leurs
     * stages
     *
     * @Route("/mail", name="GEval_ASendMails")
     */
    public function sendMailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $evaluatedList = $em->getRepository('GessehEvaluationBundle:Evaluation')->getEvaluatedList();
        $students = $em->getRepository('GessehUserBundle:Student')->getWithPlacementNotIn($evaluatedList);
        $count = 0;

        foreach($students as $student) {
            $mail = \Swift_Message::newInstance()
                ->setSubject('[GESSEH] Des évaluations sont en attente')
                ->setFrom('tmp@angrand.fr')
                ->setTo($student->getUser()->getEmail())
                ->setBody($this->renderView('GessehEvaluationBundle:Admin:sendMails.txt.twig', array(
                    'student' => $student,
                )));
            ;
            $this->get('mailer')->send($mail);
            $count++;
        }

        $this->get('session')->getFlashBag()->add('notice', $count . ' email(s) ont été envoyé(s).');
        return $this->redirect($this->generateUrl('GEval_AIndex'));
    }

    /**
     * Supprime l'évaluation d'un stage
     *
     * @Route("/placement/{id}/delete", name="GEval_ADeleteEval", requirements={"id" = "\d+"})
     */
    public function deleteEval($id)
    {
        $em = $this->getDoctrine()->getManager();
        $evaluations = $em->getRepository('GessehEvaluationBundle:Evaluation')->findByPlacement($id);

        if (!$evaluations)
          throw $this->createNotFoundException('Unable to find evaluation entity.');

        foreach($evaluations as $evaluation) {
            $em->remove($evaluation);
        }
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'Évaluation complète supprimée.');

        $queryArray = [];
        if($limit = $this->getRequest()->query->get('limit')) {
            $queryArray['limit'] = array(
                'type'        => $limit['type'],
                'value'       => $limit['value'],
                'description' => $limit['description'],
            );
        }
        return $this->redirect($this->generateUrl('GCore_PAPlacementIndex', $queryArray));
    }
}
