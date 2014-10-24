<?php

/**
 * This file is part of GESSEH project
 *
 * @author: Pierre-François ANGRAND <caragk@angrand.fr>
 * @copyright: Copyright 2013 Pierre-François Angrand
 * @license: GPLv3
 * See LICENSE file or http://www.gnu.org/licenses/gpl.html
 */

namespace Gesseh\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gesseh\CoreBundle\Entity\Hospital;
use Gesseh\CoreBundle\Form\HospitalType;
use Gesseh\CoreBundle\Form\HospitalDescriptionType;
use Gesseh\CoreBundle\Form\HospitalHandler;
use Gesseh\CoreBundle\Entity\Sector;
use Gesseh\CoreBundle\Form\SectorType;
use Gesseh\CoreBundle\Form\SectorHandler;
use Gesseh\CoreBundle\Entity\Department;
use Gesseh\CoreBundle\Form\DepartmentDescriptionType;
use Gesseh\CoreBundle\Form\DepartmentHandler;

/**
 * FieldSetAdmin controller.
 *
 * @Route("/admin")
 */
class FieldSetAdminController extends Controller
{
  /**
   * Lists all Sector entities.
   *
   * @Route("/sector", name="GCore_FSASector")
   * @Template()
   */
  public function sectorAction()
  {
    $em = $this->getDoctrine()->getManager();
    $sectors = $em->getRepository('GessehCoreBundle:Sector')->findAll();

    return array(
      'sectors'       => $sectors,
      'sector_id'     => null,
      'sector_form'   => null,
    );
  }

  /**
   * Displays a form to create a new Hospital entity.
   *
   * @Route("/hospital/new", name="GCore_FSANewHospital")
   * @Template("GessehCoreBundle:FieldSetAdmin:hospitalForm.html.twig")
   */
  public function newHospitalAction()
  {
    $em = $this->getDoctrine()->getManager();
    $manager = $this->container->get('kdb_parameters.manager');
    $mod_simul = $manager->findParamByName('simul_active');

    $hospital = new Hospital();
    $form   = $this->createForm(new HospitalType($mod_simul->getValue()), $hospital);
    $formHandler = new HospitalHandler($form, $this->get('request'), $em);

    if ( $formHandler->process() ) {
      $this->get('session')->getFlashBag()->add('notice', 'Hôpital "' . $hospital->getName() . '" enregistré.');

      return $this->redirect($this->generateUrl('GCore_FSIndex'));
    }

    return array(
      'hospital_form' => $form->createView(),
    );
  }

  /**
   * Displays a form to edit an existing Hospital entity.
   *
   * @Route("/hospital/{id}/edit", name="GCore_FSAEditHospital", requirements={"id" = "\d+"})
   * @Template("GessehCoreBundle:FieldSetAdmin:hospitalForm.html.twig")
   */
  public function editHospitalAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $manager = $this->container->get('kdb_parameters.manager');
    $mod_simul = $manager->findParamByName('simul_active');

    $hospital = $em->getRepository('GessehCoreBundle:Hospital')->find($id);

    if (!$hospital)
      throw $this->createNotFoundException('Unable to find Hospital entity.');

    $editForm = $this->createForm(new HospitalType($mod_simul->getValue()), $hospital);
    $formHandler = new HospitalHandler($editForm, $this->get('request'), $em);

    if ( $formHandler->process() ) {
      $this->get('session')->getFlashBag()->add('notice', 'Hôpital "' . $hospital->getName() . '" modifié.');

      return $this->redirect($this->generateUrl('GCore_FSIndex'));
    }

    return array(
      'hospital_form' => $editForm->createView(),
    );
  }

  /**
   * Deletes a Hospital entity.
   *
   * @Route("/hospital/{id}/delete", name="GCore_FSADeleteHospital", requirements={"id" = "\d+"}))
   */
  public function deleteHospitalAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $hospital = $em->getRepository('GessehCoreBundle:Hospital')->find($id);

    if (!$hospital)
      throw $this->createNotFoundException('Unable to find Hospital entity.');

    $em->remove($hospital);
    $em->flush();

    $this->get('session')->getFlashBag()->add('notice', 'Hôpital "' . $hospital->getName() . '" supprimé.');

    return $this->redirect($this->generateUrl('GCore_FSAIndex'));
  }

  /**
   * Deletes a Department entity.
   *
   * @Route("/department/{id}/delete", name="GCore_FSADeleteDepartment", requirements={"id" = "\d+"}))
   */
  public function deleteDepartmentAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $department = $em->getRepository('GessehCoreBundle:Department')->find($id);

    if (!$department)
      throw $this->createNotFoundException('Unable to find Department entity.');

    $em->remove($department);
    $em->flush();

    $this->get('session')->getFlashBag()->add('notice', 'Service "' . $department->getName() . '" supprimé.');

    return $this->redirect($this->generateUrl('GCore_FSIndex'));
  }

  /**
   * Displays a form to create a new Sector entity.
   *
   * @Route("/sector/new", name="GCore_FSANewSector")
   * @Template("GessehCoreBundle:FieldSetAdmin:sector.html.twig")
   */
  public function newSectorAction()
  {
    $em = $this->getDoctrine()->getManager();
    $sectors = $em->getRepository('GessehCoreBundle:Sector')->findAll();

    $sector = new Sector();

    $editForm = $this->createForm(new SectorType(), $sector);
    $formHandler = new SectorHandler($editForm, $this->get('request'), $em);

    if ( $formHandler->process() ) {
      $this->get('session')->getFlashBag()->add('notice', 'Catégorie "' . $sector->getName() . '" enregistrée.');

      return $this->redirect($this->generateUrl('GCore_FSASector'));
    }

    return array (
      'sectors'       => $sectors,
      'sector_id'     => null,
      'sector_form'   => $editForm->createView(),
    );
  }

  /**
   * Displays a form to edit an existing Sector entity.
   *
   * @Route("/sector/{id}/edit", name="GCore_FSAEditSector", requirements={"id" = "\d+"})
   * @Template("GessehCoreBundle:FieldSetAdmin:sector.html.twig")
   */
  public function editSectorAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $sectors = $em->getRepository('GessehCoreBundle:Sector')->findAll();

    $sector = $em->getRepository('GessehCoreBundle:Sector')->find($id);

    if (!$sector)
        throw $this->createNotFoundException('Unable to find Sector entity.');

    $editForm = $this->createForm(new SectorType(), $sector);
    $formHandler = new SectorHandler($editForm, $this->get('request'), $em);

    if ( $formHandler->process() ) {
      $this->get('session')->getFlashBag()->add('notice', 'Catégorie "' . $sector->getName() . '" modifiée.');

      return $this->redirect($this->generateUrl('GCore_FSASector'));
    }

    return array (
      'sectors'       => $sectors,
      'sector_id'     => $id,
      'sector_form'   => $editForm->createView(),
    );
  }

  /**
   * Deletes a Sector entity.
   *
   * @Route("/sector/{id}/delete", name="GCore_FSADeleteSector")
   */
  public function deleteSectorAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $sector = $em->getRepository('GessehCoreBundle:Sector')->find($id);

    if (!$sector)
      throw $this->createNotFoundException('Unable to find Sector entity.');

    $em->remove($sector);
    $em->flush();

    $this->get('session')->getFlashBag()->add('notice', 'Catégorie "' . $sector->getName() . '" supprimée.');

    return $this->redirect($this->generateUrl('GCore_FSASector'));
  }

  /**
   * Edit the description of the Department entity.
   *
   * @Route("/department/{id}", name="GCore_FSAEditDepartmentDescription", requirements={"id" = "\d+"})
   * @Template("GessehCoreBundle:FieldSetAdmin:editDescription.html.twig")
   */
  public function editDepartmentDescriptionAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $department = $em->getRepository('GessehCoreBundle:Department')->find($id);

    if (!$department)
      throw $this->createNotFoundException('Unable to find Department entity.');

    $editForm = $this->createForm(new DepartmentDescriptionType(), $department);
    $formHandler = new DepartmentHandler($editForm, $this->get('request'), $em);

    if ( $formHandler->process() ) {
      $this->get('session')->getFlashBag()->add('notice', 'Description du service "' . $department->getName() . '" enregistrée.');

      return $this->redirect($this->generateUrl('GCore_FSAEditDepartmentDescription', array('id' => $id)));
    }

    return array(
      'entity'  => $department,
      'edit_form' => $editForm->createView(),
    );
  }
  /**
   * Edit the description of the Hospital entity.
   *
   * @Route("/hospital/{id}", name="GCore_FSAEditHospitalDescription", requirements={"id" = "\d+"})
   * @Template("GessehCoreBundle:FieldSetAdmin:editDescription.html.twig")
   */
  public function editHospitalDescriptionAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $hospital = $em->getRepository('GessehCoreBundle:Hospital')->find($id);

    if (!$hospital)
      throw $this->createNotFoundException('Unable to find Hospital entity.');

    $editForm = $this->createForm(new HospitalDescriptionType(), $hospital);
    $formHandler = new HospitalHandler($editForm, $this->get('request'), $em);

    if ( $formHandler->process() ) {
      $this->get('session')->getFlashBag()->add('notice', 'Description de l\'hôpital "' . $hospital->getName() . '" enregistrée.');

      return $this->redirect($this->generateUrl('GCore_FSAEditHospitalDescription', array('id' => $id)));
    }

    return array(
      'entity'  => $hospital,
      'edit_form' => $editForm->createView(),
    );
  }
}
