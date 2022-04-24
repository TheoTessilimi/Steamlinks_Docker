<?php

namespace App\libraries;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Config\FrameworkConfig;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Steam //TODO faire des test pour les différentes requetes
{
    private string $key;
    private HttpClientInterface $client;
    private FrameworkConfig $framework;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBagInterface)
    {
        $this->client = $client;
        $this->key = $parameterBagInterface->get('KEY_STEAM');
    }


    public function getPlayerSummaries($id){
        $response = $this->client->request('GET',
            'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='. $this->key .'&steamids='.$id
        );
        return json_decode($response->getContent(), true)['response'];

    }

    public function getPlayerFriendList($id){
            $response = $this->client->request('GET',
                'https://api.steampowered.com/ISteamUser/GetFriendList/v1?key=' . $this->key . '&steamid=' . $id,
            );
        return json_decode($response->getContent(), true)['friendslist']['friends'];

    }

    public function GetUserStatsForGame($id, $appid){
        $response = $this->client->request('GET',
            'https://api.steampowered.com/ISteamUserStats/GetUserStatsForGame/v2?key='. $this->key .'&steamid='. $id .'&appid='.$appid
        );
        if ($response->getStatusCode() == 200) {
            return json_decode($response->getContent(), true)['playerstats']['stats'];
        }
        else{
            return false;
        }

    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     */
    public function checkSteamId($id): bool
    {
        if ($this->getPlayerSummaries($id)['players'] == null )
            return false;
        else{
            return true;
        }
    }

    public function getPlayerInfoWithId($id){
        return $this->getPlayerSummaries($id)['players']['0'];

    }

    public function getPaginatedFriendsList(int $page, int $limit, array $friendsList): array
    {
        $firstResult = ($page * $limit) - $limit;
        $maxResult = (count($friendsList));
        $response = [];

        for ($i = $firstResult; $i < ($firstResult+$limit); $i++) {
            if ($i < $maxResult && $i>0) {
                $response[$i]['steam'] = $this->getPlayerInfoWithId($friendsList[$i]['steamid']);
                $response[$i]['friendSince'] = $friendsList[$i]['friendSince'];
            }
        }

        return $response;

    }

    public function getInfoWithId($steamid, array $infoToGet):mixed
    {
        $response = [];
        $infos = $this->getPlayerInfoWithId($steamid);
        if (count($infoToGet) == 1){
            $response = $infos[implode($infoToGet)];
        }else {
            foreach ($infoToGet as $info) {
                $response[$info] = $infos[$info];
            }
        }
        return $response;

    }

}