<?php

namespace App\Tests\UnitTest\Entity;

use App\Entity\Status;
use PHPUnit\Framework\TestCase;

class StatusUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $status = new Status();

        $status->setLibelle('Clôturée');
        $this->assertTrue($status->getLibelle() === 'Clôturée');
    }

    public function testIsFalse(): void
    {
        $status = new Status();

        $status->setLibelle('Passée');
        $this->assertFalse($status->getLibelle() === 'Clôturée');
    }
}
