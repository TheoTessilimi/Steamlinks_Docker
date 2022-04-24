<?php

namespace App\Controller;

use App\Entity\User;
use App\libraries\Steam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Steam $steam, EntityManagerInterface $entityManager): Response
    {
        //TODO stocker pseudo dans une session
        //TODO faire de même pour l'image de profil ?
        //TODO Optimisation des requètes
        if ($this->getUser()) {
            /**
             * @var User $user
             */
            $user = $this->getUser();
            if ($user->getSteamId() != null) {
                $user->setPseudo($steam->getInfoWithId($user->getSteamId(), ['personaname']));
                $entityManager->flush();
            }
        }
        return $this->render('home/index.html.twig');
    }
}
