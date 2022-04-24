<?php

namespace App\Controller;

use App\Entity\User;
use App\libraries\Steam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private Steam $steam;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Steam $steam
     */
    public function __construct(Steam $steam)
    {
        $this->steam = $steam;
    }


    #[Route('/users', name: 'app_users')]
    public function index(Request $request): Response
    {
        //Création des variables

        $session = $request->getSession(); //session
        $page = (int)$request->query->get('page', 1); //numéro de la page
        $friendsList = []; //Liste d'amis
        $limit = 10; //Nombre maximum d'éléments par page

        //récupération des amis steam si pas deja en session on actualise si l'utilisateur retourne page 1
        if ($session->get('friendList') == null || $page == 1) {
            $i = 0;
            foreach ($this->steam->getPlayerFriendList($this->getUser()->getSteamID()) as $friend) {
                $friendsList[$i]['steamid'] = $friend['steamid'];
                $friendsList[$i]['friendSince'] = $friend['friend_since'];
                $i++;
            }
            $session->set('friendList', $friendsList); //On stocke la session dans une session
        }
        $friendsList = ($session->get('friendList'));
        //récupération de la liste à afficher
        $friendsListPaginate = $this->steam->getPaginatedFriendsList($page, $limit, $friendsList);



        return $this->render('user/index.html.twig', [
            'nbFriends' => count($friendsList),
            'users' => $friendsListPaginate,
            'itemLimits' => $limit,
            'actualPage' => $page
        ]);
    }

    #[Route('/user/{steamid}', name: 'app_user_details')]
    public function user($steamid): Response
    {
        $response = $this->steam->GetUserStatsForGame($steamid, '730');
        $info = $this->steam->getInfoWithId($steamid, ['avatarmedium', 'personaname']);
        $userStats = [];
        if($response != null) {
            foreach ($response as $stats) {
                $userStats[$stats['name']] = (int)$stats['value'];

            }
        }

        return $this->render('user/user.html.twig', [
            'users' => $userStats,
            'pseudo' => $info['personaname'],
            'avatar' => $info['avatarmedium']
        ]);
    }
}
