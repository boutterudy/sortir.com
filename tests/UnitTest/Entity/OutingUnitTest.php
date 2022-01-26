<?php

namespace App\Tests\UnitTest\Entity;

use App\Entity\Outing;
use DateTime;
use PHPUnit\Framework\TestCase;

class OutingUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $outing = new Outing();

        $dateTime = new DateTime();
        $interval = date_interval_create_from_date_string('10 days');

        $outing->setName('Musée')
            ->setStartAt($dateTime)
            ->setDuration($interval)
            ->setLimitSubscriptionDate($dateTime)
            ->setMaxUsers(15)
            ->setAbout('10 jours au musée, oui c\'est long');

        $this->assertTrue($outing->getName() === 'Musée');
        $this->assertTrue($outing->getStartAt() === $dateTime);
        $this->assertTrue($outing->getDuration() === $interval);
        $this->assertTrue($outing->getLimitSubscriptionDate() === $dateTime);
        $this->assertTrue($outing->getMaxUsers() === 15);
        $this->assertTrue($outing->getAbout() === '10 jours au musée, oui c\'est long');
    }

    public function testIsFalse(): void
    {
        $outing = new Outing();

        $dateTime = new DateTime();
        $dateFalse = date_create('2022-04-01');
        $interval = date_interval_create_from_date_string('10 days');
        $intervalFalse = date_interval_create_from_date_string('10 weeks');


        $outing->setName('Musée')
            ->setStartAt($dateTime)
            ->setDuration($interval)
            ->setLimitSubscriptionDate($dateTime)
            ->setMaxUsers(15)
            ->setAbout('10 jours au musée, oui c\'est long');

        $this->assertFalse($outing->getName() === 'Beuverie');
        $this->assertFalse($outing->getStartAt() === $dateFalse);
        $this->assertFalse($outing->getDuration() === $intervalFalse);
        $this->assertFalse($outing->getLimitSubscriptionDate() === $dateFalse);
        $this->assertFalse($outing->getMaxUsers() === 25);
        $this->assertFalse($outing->getAbout() === '8 jours au bistrot, oui c\'est long');
    }
}
