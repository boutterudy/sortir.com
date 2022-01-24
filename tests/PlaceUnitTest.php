<?php

namespace App\Tests;

use App\Entity\Place;
use PHPUnit\Framework\TestCase;

class PlaceUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $place = new Place();

        $place->setName('Centre-ville')
            ->setStreet('51, rue de la soif')
            ->setLongitude(-1.472266)
            ->setLatitude(47.652696);

        $this->assertTrue($place->getName() === 'Centre-ville');
        $this->assertTrue($place->getStreet() === '51, rue de la soif');
        $this->assertTrue($place->getLongitude() === -1.472266);
        $this->assertTrue($place->getLatitude() === 47.652696);

    }

    public function testIsFalse(): void
    {
        $place = new Place();

        $place->setName('Centre-ville')
            ->setStreet('51, rue de la soif')
            ->setLongitude(-1.472266)
            ->setLatitude(47.652696);

        $this->assertFalse($place->getName() === 'Centre ville');
        $this->assertFalse($place->getStreet() === '53, rue de la soif');
        $this->assertFalse($place->getLongitude() === -1.452266);
        $this->assertFalse($place->getLatitude() === 47.642696);
    }
}
