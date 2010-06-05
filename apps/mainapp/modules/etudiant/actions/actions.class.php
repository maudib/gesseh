<?php

/**
 * etudiant actions.
 *
 * @package    gesseh
 * @subpackage etudiant
 * @author     Pierre-FrançoisPilouAngrand
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class etudiantActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->user = $this->getUser()->getUsername();
    $this->gesseh_stages = Doctrine::getTable('GessehStage')->getStagesEtudiant($this->user);
  }

  public function executeEdit(sfWebRequest $request)
  {
//    $this->forward404Unless($gesseh_stage = Doctrine::getTable('GessehStage')->find(array($request->getParameter('id'))), sprintf('Object gesseh_stage does not exist (%s).', $request->getParameter('id')));
    $gesseh_etudiant = Doctrine::getTable('GessehEtudiant')->find(array('id' => $this->getUser()->getUsername()));
    $this->form = new GessehEtudiantForm($gesseh_etudiant);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($gesseh_etudiant = Doctrine::getTable('GessehEtudiant')->find(array($request->getParameter('id'))), sprintf('Object gesseh_etudiant does not exist (%s).', $request->getParameter('id')));
    $this->form = new GessehEtudiantForm($gesseh_etudiant);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeMail(sfWebRequest $request)
  {
    if (isset($request['token']))
      Doctrine::getTable('GessehEtudiant')->validTokenMail($request['iduser'], $request['token']);
      
    $gesseh_etudiant = Doctrine::getTable('GessehEtudiant')->find(array('id' => $this->getUser()->getUsername()));
    if ($gesseh_etudiant->getEmail() == null)
    {
      $this->getUser()->setFlash('error','Vous n\'avez pas encore enregistré d\'adresse email.');
      $this->redirect('etudiant/edit');
    }
    elseif ($gesseh_etudiant->getTokenMail() != null)
    {
      $this->getUser()->setFlash('error','Votre email n\'a pas été validé.');
      $this->redirect('etudiant/edit');
    }
    else
    {
      $this->redirect('etudiant/index');
    }
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
    echo $form;
      if ($form->getValue('cmdp') != null)
      {
        if($this->getUser()->getGuardUser()->checkPassword($form->getValue('cmdp')))
	{
	  if($form->getValue('nmdp') == $form->getValue('vnmdp'))
	  {
	    $this->getUser()->getGuardUser()->setPassword($form->getValue('nmdp'));
	    $this->getUser()->getGuardUser()->save();
	    $this->getUser()->setFlash('notice','Mot de passe changé avec succès.');
	  }
	  else
	  {
	    $this->getUser()->setFlash('error','Erreur : les 2 mots de passes ne sont pas identiques.');
	  }
	}
	else
	{
	  $this->getUser()->setFlash('error','Erreur : le mot de passe est erroné.');
	}
      }

      $form->save();
//      $this->getUser()->setFlash('notice','Mise à jour du profil effectuée.');

      $this->redirect('etudiant/edit');
    }
  }
}
