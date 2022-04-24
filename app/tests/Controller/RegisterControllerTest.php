<?php /** @noinspection CssInvalidPseudoSelector */

namespace App\Tests\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{

    private \Doctrine\Persistence\ObjectManager $entityManager;
    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }


    public function testRegisterPage(): void
    {
        $this->client->request('GET', '/register');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    }

    public function testRegisterPageFromHome(): void
    {
        $crawler = $this->client->request('GET', '/');
        $link = $crawler->selectLink('Inscription')->link();
        $crawler = $this->client->click($link);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h2', 'Merci de vous inscrire pour continuer.');

        //echo $this->client->getResponse()->getContent();

    }

    public function testInscriptionIsValid(): void{
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->selectButton('Envoyer')->form();
        $form['register[pseudo]'] = "Pseudo";
        $form['register[firstname]'] = "firstname";
        $form['register[lastname]'] = "lastname";
        $form['register[email]'] = "email@email";
        $form['register[password][first]'] = "password";
        $form['register[password][second]'] = "password";
        $this->client->submit($form);
        $userBDD = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'email@email']);
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("Merci de vous connecter :")')->count());
        $this->entityManager->getRepository(User::class)->remove($userBDD);
    }

    public function testIfEmailAlreadyExist(): void{
        //on essaye de créer 2 utilisateurs avec la même adresse mail
        for ($i=0; $i < 2 ; $i++){
            $crawler = $this->client->request('GET', '/register');
            $form = $crawler->selectButton('Envoyer')->form();
            $form['register[pseudo]'] = "Pseudo";
            $form['register[firstname]'] = "firstname";
            $form['register[lastname]'] = "lastname";
            $form['register[email]'] = "email@email";
            $form['register[password][first]'] = "password";
            $form['register[password][second]'] = "password";
            $crawler = $this->client->submit($form);
        }
        //On supprime l'utilisateur en base
        $userBDD = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'email@email']);
        $this->entityManager->getRepository(User::class)->remove($userBDD);
        //On récupère l'erreur affichée
        $info = $crawler->filter('div.alert-danger')->text();
        $info = trim(preg_replace('/\s\s+/', ' ', $info));
        //On vérifie si l'erreur correspond bien
        $this->assertSame("L'adresse mail email@email existe déjà !", $info);
}
}
