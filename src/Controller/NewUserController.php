<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class NewUserController extends AbstractController {

    /**
     * @Route("/user-signUp", name="user_signUp")
     */
    public function signUp(\Symfony\Component\HttpFoundation\Request $req) {
        $error = "";
        $dto = new \App\Entity\User();
        $form = $this->createForm(\App\Form\UserType::class, $dto);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->select("u")
                    ->FROM("App:User", "u")
                    ->WHERE("u.username = :username")
                    ->setParameter("username", $dto->getUsername());

            $userExist = $qb->getQuery()->getOneOrNullResult();

            if ($userExist) {
                $error = "Username already exists !";
            } else {
                $qb2 = $this->getDoctrine()->getManager()->createQueryBuilder();
                $qb2->select("u2")
                        ->FROM("App:User", "u2")
                        ->WHERE("u2.email = :email")
                        ->setParameter("email", $dto->getEmail());

                $userExist2 = $qb2->getQuery()->getOneOrNullResult();
                if ($userExist2) {
                    $error = "E-mail already exists !";
                } else {
                    $user = new \App\Entity\User();
                    $user->setUsername($dto->getUsername())->setEmail($dto->getEmail())
                            ->setPassword($dto->getPassword());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    return $this->redirectToRoute('user_signIn');
                }
            }
        }
        return $this->render('new_user/signUp.html.twig', [
                    'signUpForm' => $form->createView(),
                    'errorMessage' => $error
        ]);
    }

    /**
     * @Route("/user-signIn", name="user_signIn")
     */
    public function signIn(\Symfony\Component\HttpFoundation\Request $req, UserRepository $user) {
        $dto = new \App\Entity\User();
        $form = $this->createForm(\App\Form\SignInType::class, $dto);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $login = $user->findBy(['username' => $dto->getUsername(),
                'password' => $dto->getPassword()]);

            if (!empty($login)) {
                $req->getSession()->set("username", $dto->getUsername());
            }
            return $this->redirectToRoute('home');
        }
        return $this->render('new_user/signIn.html.twig', [
                    'signInForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/user-logOut", name="user_logOut")
     */
    public function logOut(\Symfony\Component\HttpFoundation\Request $req) {
        $req->getSession()->invalidate();
        return $this->redirectToRoute('home');
    }

}
