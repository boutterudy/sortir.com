<?php

namespace App\Tests\UnitTest\Entity;

use App\Entity\Campus;
use PHPUnit\Framework\TestCase;

class CampusUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $campus = new Campus();

        $campus->setName('Rennes');
        $this->assertTrue($campus->getName() === 'Rennes');
    }

    public function testIsFalse(): void
    {
        $campus = new Campus();

        $campus->setName('Rennes');
        $this->assertFalse($campus->getName() === 'En Ligne');
    }
}
