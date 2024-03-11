<?php

namespace App\Tests\Entity\Embeddable;

use App\Entity\Embeddable\Measure;
use App\Entity\Management\Unit;
use PHPUnit\Framework\TestCase;

final class MeasureTest extends TestCase {
    public static function setUpBeforeClass(): void
    {
        echo "\nMeasureTest\n";
    }
    public function testAdd(): void {
        $units = self::getUnits();
        $added = (new Measure())
            ->setCode('kg')
            ->setUnit($units['kg'])
            ->setValue(2);
        $measure = (new Measure())
            ->setCode('g')
            ->setUnit($units['g'])
            ->setValue(2)
            ->add($added);
        $this->assertEquals('g', $measure->getCode());
        $this->assertEquals(2002, $measure->getValue());
        $this->assertEquals('kg', $added->getCode());
        $this->assertEquals(2, $added->getValue());

        $added = (new Measure())
            ->setCode('mg')
            ->setUnit($units['mg'])
            ->setValue(4);
        $measure = (new Measure())
            ->setCode('g')
            ->setUnit($units['g'])
            ->setValue(4)
            ->add($added);
        $this->assertEquals('mg', $measure->getCode());
        $this->assertEquals(4004, $measure->getValue());
        $this->assertEquals('mg', $added->getCode());
        $this->assertEquals(4, $added->getValue());
    }

    public function testAddDenominator(): void {
        $units = self::getUnits();
        $added = (new Measure())
            ->setCode('m')
            ->setDenominator('s')
            ->setDenominatorUnit($units['s'])
            ->setUnit($units['m'])
            ->setValue(10);
        $measure = (new Measure())
            ->setCode('km')
            ->setDenominator('h')
            ->setDenominatorUnit($units['h'])
            ->setUnit($units['km'])
            ->setValue(10)
            ->add($added);
        $this->assertEquals('m', $measure->getCode());
        $this->assertEquals('s', $measure->getDenominator());
        $this->assertEqualsWithDelta(12.777_78, $measure->getValue(), 0.000_01);
        $this->assertEquals('m', $added->getCode());
        $this->assertEquals('s', $added->getDenominator());
        $this->assertEquals(10, $added->getValue());

        $added = (new Measure())
            ->setCode('km')
            ->setDenominator('h')
            ->setDenominatorUnit($units['h'])
            ->setUnit($units['km'])
            ->setValue(1);
        $measure = (new Measure())
            ->setCode('m')
            ->setDenominator('s')
            ->setDenominatorUnit($units['s'])
            ->setUnit($units['m'])
            ->setValue(1)
            ->add($added);
        $this->assertEquals('m', $measure->getCode());
        $this->assertEquals('s', $measure->getDenominator());
        $this->assertEqualsWithDelta(1.277_78, $measure->getValue(), 0.000_01);
        $this->assertEquals('km', $added->getCode());
        $this->assertEquals('h', $added->getDenominator());
        $this->assertEquals(1, $added->getValue());
    }

    public function testConvert(): void {
        $units = self::getUnits();
        $measure = (new Measure())
            ->setCode('kg')
            ->setUnit($units['kg'])
            ->setValue(2)
            ->convert($units['g']);
        $this->assertEquals('g', $measure->getCode());
        $this->assertEquals(2000, $measure->getValue());

        $measure = (new Measure())
            ->setCode('mg')
            ->setUnit($units['mg'])
            ->setValue(4)
            ->convert($units['g']);
        $this->assertEquals('g', $measure->getCode());
        $this->assertEquals(0.004, $measure->getValue());
    }

    public function testConvertDenominator(): void {
        $units = self::getUnits();
        $measure = (new Measure())
            ->setCode('km')
            ->setDenominator('h')
            ->setDenominatorUnit($units['h'])
            ->setUnit($units['km'])
            ->setValue(10)
            ->convert($units['m'], $units['s']);
        $this->assertEquals('m', $measure->getCode());
        $this->assertEquals('s', $measure->getDenominator());
        $this->assertEqualsWithDelta(2.777_78, $measure->getValue(), 0.000_01);

        $measure = (new Measure())
            ->setCode('m')
            ->setDenominator('s')
            ->setDenominatorUnit($units['s'])
            ->setUnit($units['m'])
            ->setValue(1)
            ->convert($units['km'], $units['h']);
        $this->assertEquals('km', $measure->getCode());
        $this->assertEquals('h', $measure->getDenominator());
        $this->assertEquals(3.6, $measure->getValue());
    }

    public function testIsGreaterThanOrEqual(): void {
        $units = self::getUnits();
        $compared = (new Measure())
            ->setCode('m')
            ->setDenominator('s')
            ->setDenominatorUnit($units['s'])
            ->setUnit($units['m'])
            ->setValue(10);
        $measure = (new Measure())
            ->setCode('km')
            ->setDenominator('h')
            ->setDenominatorUnit($units['h'])
            ->setUnit($units['km'])
            ->setValue(10);
        $this->assertFalse($measure->isGreaterThanOrEqual($compared));
        $this->assertEquals('km', $measure->getCode());
        $this->assertEquals('h', $measure->getDenominator());
        $this->assertEquals(10, $measure->getValue());
        $this->assertEquals('m', $compared->getCode());
        $this->assertEquals('s', $compared->getDenominator());
        $this->assertEquals(10, $compared->getValue());
    }

    public static function getUnits(): array {
        $h = (new Unit())->setBase(3600)->setCode('h');
        $s = (new Unit())->setBase(1)->setCode('s')->addChild($h)->setParent(null);

        $mg = (new Unit())->setBase(0.001)->setCode('mg');
        $kg = (new Unit())->setBase(1000)->setCode('kg');
        $g = (new Unit())->setBase(1)->setCode('g')->addChild($kg)->addChild($mg)->setParent(null);

        $km = (new Unit())->setBase(1000)->setCode('km');
        $m = (new Unit())->setBase(1)->setCode('m')->addChild($km)->setParent(null);
        return [
            'h' => $h,
            'g' => $g,
            'kg' => $kg,
            'km' => $km,
            'm' => $m,
            'mg' => $mg,
            's' => $s
        ];
    }
}
