<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\User as EntityUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/register', name: 'app_register')]
    public function index(UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        $user = new User();

        /**
         * @var UserRepository
         */
        $userEntity = $this->entityManager->getRepository(User::class);
        
        $error = "";
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            if ($userEntity->verifyIfEmailIsUnique($user->getEmail())) {
                $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return $this->redirectToRoute('app_login');
            } else {
                $error = 'L\'adresse mail '. $user->getEmail() . ' existe déjà !';
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

}
