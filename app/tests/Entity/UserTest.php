<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 *
 */
class UserTest extends KernelTestCase
{
    /**
     * @return User
     */
    public function getEntity(): User
    {
        return (new User())
            ->setEmail('test@gmail.com')
            ->setFirstname('théo')
            ->setLastname('tessilimi')
            ->setSteamID('125884233')
            ->setRoles([]);
}

    /**
     * @param User $user
     * @param int $number
     * @return void
     */
    public function assertHasErrors(User $user, int $number = 0){
        self::bootKernel();
        $error = self::getContainer()->get('validator')->validate($user);
        $this->assertCount($number, $error);
    }

    /**
     * @return void
     */
    public function testValidEntity(){

        $this->assertHasErrors($this->getEntity(), 0);

    }

    /**
     * @return void
     */
    public function testIfEmailIsEmptyInEntity(){
        $user = $this->getEntity()->setEmail('');
        $this->assertHasErrors($user, 1);
    }


    /**
     * @return void
     */
    public function testIfGetFullNameReturnFullName(){
        $this->assertEquals('Théo TESSILIMI', $this->getEntity()->getFullName());

    }
}