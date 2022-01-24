<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $user = new User();

        $user->setNickName('pseudo')
            ->setLastName('Nom de famille')
            ->setFirstName('Prénom')
            ->setPhoneNumber('0698765432')
            ->setEmail('pseudo@fai.fr')
            ->setPassword('password')
            ->setIsAdmin(true)
            ->setIsActive(true)
            ->setImageFile('randomImage.png')
            ->setRoles(['ROLE_ADMIN']);

        $this->assertTrue($user->getNickName() === 'pseudo');
        $this->assertTrue($user->getLastName() === 'Nom de famille');
        $this->assertTrue($user->getFirstName() === 'Prénom');
        $this->assertTrue($user->getPhoneNumber() === '0698765432');
        $this->assertTrue($user->getEmail() === 'pseudo@fai.fr');
        $this->assertTrue($user->getPassword() === 'password');
        $this->assertTrue($user->getIsAdmin() === true);
        $this->assertTrue($user->getIsActive() === true);
        $this->assertTrue($user->getImageFile() === 'randomImage.png');
        $this->assertTrue($user->getRoles()[0] === 'ROLE_ADMIN');
    }

    public function testIsFalse(): void
    {
        $user = new User();

        $user->setNickName('pseudo')
            ->setLastName('Nom de famille')
            ->setFirstName('Prénom')
            ->setPhoneNumber('0698765432')
            ->setEmail('pseudo@fai.fr')
            ->setPassword('password')
            ->setIsAdmin(true)
            ->setIsActive(true)
            ->setImageFile('randomImage.png')
            ->setRoles(['ROLE_ADMIN']);


        $this->assertFalse($user->getNickName() === 'nick');
        $this->assertFalse($user->getLastName() === 'lastname');
        $this->assertFalse($user->getFirstName() === 'firstname');
        $this->assertFalse($user->getPhoneNumber() === '0623456789');
        $this->assertFalse($user->getEmail() === 'nick@free.fr');
        $this->assertFalse($user->getPassword() === 'p@ssw0rd');
        $this->assertFalse($user->getIsAdmin() === false);
        $this->assertFalse($user->getIsActive() === false);
        $this->assertFalse($user->getImageFile() === 'avatar.png');
        $this->assertFalse($user->getRoles()[0] === 'ROLE_USER');
    }
}
