<?php

namespace AdSite\AdSiteBundle\Controller;


use AdSite\AdSiteBundle\Entity\Connexion;
use AdSite\AdSiteBundle\Manager\UsersManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdSite\AdSiteBundle\Form\LoginFormType;
use Symfony\Component\HttpFoundation\Request;


class LoginController extends Controller
{
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $connexion = new Connexion();
        $form = $this->createForm(LoginFormType::class, $connexion);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user_access = new UsersManager($this->getDoctrine()->getManager());
            $login = $form->get('pseudo')->getData();
            $password = $form->get('password')->getData();
            if ($user_access->checkUserExist($login, $password) > 0){
                $user_id = $user_access->getUserId($login,$password);
                $session->set('user_login', $user_id);

                //return $this->render('AdSiteBundle:Default:welcomeTemp.html.twig');
                return $this->redirectToRoute('test_homepage');
            }
        }

        $form_inscr = $this->createForm(LoginFormType::class, $connexion);

        return $this->render('AdSiteBundle:Login:login.html.twig', array('form' => $form->createView(),'form_inscr'=>$form_inscr->createView()));


    }
}
