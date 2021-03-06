<?php

namespace AdSite\AdSiteBundle\Controller;

use AdSite\AdSiteBundle\Entity\Connexion;
use AdSite\AdSiteBundle\Entity\User;
use AdSite\AdSiteBundle\Form\InscriptionFormType;
use AdSite\AdSiteBundle\Manager\UsersManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdSite\AdSiteBundle\Form\LoginFormType;
use Symfony\Component\HttpFoundation\Request;


class LoginController extends Controller
{
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $user = $session->get('user_login')[0]['id'];
        if ($user == null) {
            $connexion = new Connexion();
            $form_connexion = $this->createForm(LoginFormType::class, $connexion);
            $form_connexion->handleRequest($request);

            if ($form_connexion->isValid()) {
                $user_access = new UsersManager($this->getDoctrine()->getManager());
                $login = $form_connexion->get('pseudo')->getData();
                $password = $form_connexion->get('password')->getData();
                if ($user_access->checkUserExist($login, md5($password)) > 0) {
                    $user_id = $user_access->getUserId($login, md5($password));
                    $session->set('user_login', $user_id);
                    return $this->redirectToRoute('test_homepage');
                }
            }

            $user = new User();
            $form_inscription = $this->createForm(InscriptionFormType::class, $user);
            $form_inscription->handleRequest($request);

            if ($form_inscription->isValid()) {
                if ($form_inscription->get('Inscription')->isClicked()) {
                    $user_access = new UsersManager($this->getDoctrine()->getManager());
                    $newuser = $user_access->insertUser($form_inscription);
                    $user_id = $user_access->getUserId($newuser->getLogin(), $newuser->getPassword());
                    $session->set('user_login', $user_id);
                    return $this->redirectToRoute('test_homepage');
                }
            }

            return $this->render('AdSiteBundle:Login:login.html.twig', array('form' => $form_connexion->createView(), 'form_inscr' => $form_inscription->createView()));
        } else {
            return $this->redirectToRoute('my_account');
        }
    }
}
