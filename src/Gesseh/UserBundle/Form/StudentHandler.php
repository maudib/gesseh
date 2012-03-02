<?php
// src/Gesseh/UserBundle/Form/StudentHandler.php

namespace Gesseh\UserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Gesseh\UserBundle\Entity\Student;
use FOS\UserBundle\Entity\UserManager;

class StudentHandler
{
  private $form;
  private $request;
  private $em;
  private $um;

  public function __construct(Form $form, Request $request, EntityManager $em, UserManager $um)
  {
    $this->form    = $form;
    $this->request = $request;
    $this->em      = $em;
    $this->um      = $um;
  }

  public function process()
  {
    if( $this->request->getMethod() == 'POST' ) {
      $this->form->bindRequest($this->request);

      if ($this->form->isValid()) {
        $this->onSuccess(($this->form->getData()));

        return true;
      }
    }

    return false;
  }

  public function onSuccess(Student $student)
  {
    $this->updateUser($student->getUser());
    $this->em->persist($student);
    $this->em->flush();
  }

  private function updateUser($user)
  {
    if( null == $user->getUsername() ) {
      $this->um->createUser();
      $user->setPlainPassword('toto');
      $user->setConfirmationToken(null);
      $user->setEnabled(true);
      $user->addRole('ROLE_STUDENT');
    }
    $user->setUsername($user->getEmail());

    $this->um->updateUser($user);
  }
}