<?php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegisterType;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function register(Request $request, UserPasswordEncoderInterface $encoder){

        //  Crear el formulario
        $user = new Users();
        $form = $this->createForm(RegisterType::class, $user);

        //  Rellenar el objeto con los datos del form
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
        //  Modificando el objeto usuario para guardarlo
            $user->setRole('ROLE_USER');

            //  Cifrando la contraseña
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
               $date = new \Datetime('now');
            $user->setCreatedAt($date);
            $user->setUpdatedAt($date);
           
            //Guardar el usuario
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush($user);
            
            return $this->redirectToRoute('bienvenida');
        }

        return $this->render('user/register.html.twig', [
            'formRegister' => $form->createView(),
        ]);
    }

    public function bienvenida(){

        $user = new Users();
        $email = $user->getEmail();

        return $this->render('user/welcomeLanding.html.twig', [
            'email' => $email,
        ]);
    }

    public function login(AuthenticationUtils $authenticationUtils){
        
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig',[
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }
    
    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(){

    }
}
