<?php

namespace App\Tests;

use App\Entity\Town;
use PHPUnit\Framework\TestCase;

class TownUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $town = new Town();

        $town->setName('Nantes')
            ->setPostalCode('44000');

        $this->assertTrue($town->getName() === 'Nantes');
        $this->assertTrue($town->getPostalCode() === '44000');

    }

    public function testIsFalse(): void
    {
        $town = new Town();

        $town->setName('Nantes')
            ->setPostalCode('44000');

        $this->assertFalse($town->getName() === 'Paris');
        $this->assertFalse($town->getPostalCode() === '75000');
    }
}
