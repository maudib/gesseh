<?php

/**
 * This file is part of GESSEH project
 *
 * @author: Pierre-François ANGRAND <gesseh@medlibre.fr>
 * @copyright: Copyright 2015 Pierre-François Angrand
 * @license: GPLv3
 * See LICENSE file or http://www.gnu.org/licenses/gpl.html
 */

namespace Gesseh\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gesseh\RegisterBundle\Entity\MemberQuestion,
    Gesseh\RegisterBundle\Form\RegisterType,
    Gesseh\RegisterBundle\Form\RegisterHandler,
    Gesseh\RegisterBundle\Form\JoinType,
    Gesseh\RegisterBundle\Form\JoinHandler,
    Gesseh\RegisterBundle\Form\QuestionType,
    Gesseh\RegisterBundle\Form\QuestionHandler;

/**
 * RegisterBundle UserController
 *
 * @Route("/")
 */
class UserController extends Controller
{
    /**
     * Create Membership
     *
     * @Route("/register", name="GRegister_URegister")
     * @Template()
     */
    public function registerAction()
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
            return $this->redirect($this->generateUrl('GRegister_UJoin'));

        $em = $this->getDoctrine()->getManager();
        $um = $this->container->get('fos_user.user_manager');
        $pm = $this->container->get('kdb_parameters.manager');
        $tokenGenerator = $this->container->get('fos_user.util.token_generator');
        $token = $tokenGenerator->generateToken();

        $form = $this->createForm(new RegisterType($pm->findParamByName('simul_active')));
        $form_handler = new RegisterHandler($form, $this->get('request'), $em, $um, $pm->findParamByName('reg_payment'), $token);

        if($username = $form_handler->process()) {
            $this->get('session')->getFlashBag()->add('notice', 'Utilisateur ' . $username . ' créé.');

            return $this->redirect($this->generateUrl('GRegister_USendConfirmation', array('email' => $username)));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Complementary questions
     *
     * @Route("/user/questions", name="GRegister_UQuestion")
     * @Template()
     */
    public function questionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pm = $this->container->get('kdb_parameters.manager');
        $user = $this->get('request')->query->get('user');

        $questions = $em->getRepository('GessehRegisterBundle:MemberQuestion')->findAll();
        $membership = $em->getRepository('Gesseh\RegisterBundle\Entity\Membership')->getLastByUsername($user);

        $form = $this->createForm(new QuestionType($questions));
        $form_handler = new QuestionHandler($form, $this->get('request'), $em, $membership, $questions);
        if($form_handler->process()) {
            $this->get('session')->getFlashBag()->add('notice', 'Utilisateur créé.');

            return $this->redirect($this->generateUrl('GRegister_UValidate', array('user' => $user)));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Send confirmation email
     *
     * @Route("/register/send/{email}", name="GRegister_USendConfirmation", requirements={"email" = ".+\@.+\.\w+" })
     * @Template()
     */
    public function sendConfirmationAction($email)
    {
        $username = $this->get('request')->query->get('username');
        $um = $this->container->get('fos_user.user_manager');
        $user = $um->findUserByUsername($email);

        if(!$user)
            throw $this->createNotFoundException('Aucun utilisateur lié à cette adresse mail.');

        if(!$user->getConfirmationToken())
            throw $this->createNotFoundException('Cet utilisateur n\'a pas de jeton de confirmation défini. Est-il déjà validé ? Contactez un administrateur.');

        $url = $this->generateUrl('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), true);
        $sendmail = \Swift_Message::newInstance()
                ->setSubject('GESSEH - Confirmation d\'adresse mail')
                ->setFrom($this->container->getParameter('mailer_mail'))
                ->setTo($user->getEmailCanonical())
                ->setBody($this->renderView('GessehRegisterBundle:User:confirmation.html.twig', array('user' => $user, 'url' => $url)), 'text/html')
                ->addPart($this->renderView('GessehRegisterBundle:User:confirmation.txt.twig', array('user' => $user, 'url' => $url)), 'text/plain')
        ;
        $this->get('mailer')->send($sendmail);

        return array(
            'email' => $user->getEmailCanonical(),
        );
    }

    /**
     * Join action
     *
     * @Route("/user/join", name="GRegister_UJoin")
     * @Template()
     */
    public function joinAction()
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
            return $this->redirect($this->generateUrl('GRegister_URegister'));

        $em = $this->getDoctrine()->getManager();
        $pm = $this->container->get('kdb_parameters.manager');
        $username = $this->get('security.context')->getToken()->getUsername();
        $student = $em->getRepository('GessehUserBundle:Student')->getByUsername($username);

        if (null !== $em->getRepository('GessehRegisterBundle:Membership')->getCurrentForStudent($student)) {
            $this->get('session')->getFlashBag()->add('error', 'Adhésion déjà à jour de cotisation.');

            return $this->redirect($this->generateUrl('GRegister_UIndex'));
        }

        $form = $this->createForm(new JoinType());
        $form_handler = new JoinHandler($form, $this->get('request'), $em, $pm->findParamByName('reg_payment'), $student);

        if($form_handler->process()) {
            $this->get('session')->getFlashBag()->add('notice', 'Adhésion enregistrée pour ' . $student . '.');

            return $this->redirect($this->generateUrl('GCore_FSIndex'));
        }

        return array(
            'form' => $form->createView(),
        );

    }

    /**
     * Index action
     *
     * @Route("/user/membership", name="GRegister_UIndex")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $um = $this->container->get('fos_user.user_manager');
        $user = $um->findUserBy(array(
            'username' => $this->get('security.context')->getToken()->getUsername(),
        ));
        $student = $em->getRepository('GessehUserBundle:Student')->getByUsername($user->getUsername());

        $memberships = $em->getRepository('GessehRegisterBundle:Membership')->findBy(array('student' => $student));

        return array(
            'memberships' => $memberships,
        );
    }
}
