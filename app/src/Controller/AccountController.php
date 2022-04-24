<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use xPaw\Steam\SteamOpenID;
use App\Form\SteamIdType;
use App\libraries\Steam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig',);
    }


    /**
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    #[Route('/account/steamid', name: 'app_account_steamid')]
    public function steamid(Request $request, Steam $steam): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $form = $this->createForm(SteamIdType::class, $this->getUser());
        $notification = $request->query->get('info', null);
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $actual_link = strtok($actual_link, '?');
        if (isset($_GET[ 'openid_claimed_id' ])) {
            $CommunityID = SteamOpenID::ValidateLogin($actual_link);

            if ($CommunityID === null) {
                return $this->redirectToRoute('app_account_steamid', ['info' => 'Un problème a été détecté lors de l\'ajout de votre SteamID']);
            }
            elseif($steam->checkSteamId($CommunityID)) {
                $user->setSteamID($CommunityID);
                $user->setRoles(array('ROLE_USER_WITH_STEAMID'));
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return $this->redirectToRoute('app_account_steamid', ['info' => 'Votre Steam ID a bien été ajouté']);
            }
        }

        return $this->render('account/steamid.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification,
            'actual_link' => $actual_link
        ]);
    }
}
