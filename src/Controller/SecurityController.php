<?php

namespace App\Controller;

use App\Entity\Collections;
use App\Entity\Users;
use App\Entity\Wishlists;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="register")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws Exception
     */
    public function registerUser(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) : Response {
        $user = new Users();

        $form = $this->createForm(RegistrationFormType::Class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRole(['ROLE_USER']);
            $user->setRegisteredAt(new \DateTime('now'));

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setImage("default.jpg");

            $userCollection = new Collections();
            $userCollection->setName("Collection");
            $userCollection->setUsers($user);

            $userWishlist = new Wishlists();
            $userWishlist->setName("Default");
            $userWishlist->setUsers($user);

            $user->setDefaultWishlist($userWishlist);

            $manager->persist($userWishlist);
            $manager->persist($userCollection);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Inscription réussie !'
            );
            return $this->redirectToRoute('login');
        }

        return $this->render("security/registration.html.twig", [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/connexion", name="login")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function logUser(Request $request, EntityManagerInterface $manager) : Response {
        /*$form = $this->createForm(LoginFormType::Class);
        return $this->render("security/login.html.twig", [
            'form' => $form->createView()
        ]);*/

        return $this->render("security/login.html.twig");
    }
    /**
     * @Route("/deconnexion", name="logout")
     */
    public function logOut() {

    }
}
