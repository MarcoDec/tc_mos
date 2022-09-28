<?php

namespace App\Tests\Entity\Embeddable;

use App\Entity\Embeddable\Measure;
use App\Entity\Management\Unit;
use App\Repository\Management\UnitRepository;
use PHPUnit\Framework\TestCase;

final class MeasureTest extends TestCase {
    /** @var array<string, Unit> */
    private array $units = [];

    public function testAdd(): void {
        $added = (new Measure())
            ->setCode('kg')
            ->setUnit($this->units['kg'])
            ->setValue(2);
        $measure = (new Measure())
            ->setCode('g')
            ->setUnit($this->units['g'])
            ->setValue(2)
            ->add($added);
        $this->assertEquals('g', $measure->getCode());
        $this->assertEquals(2002, $measure->getValue());
        $this->assertEquals('kg', $added->getCode());
        $this->assertEquals(2, $added->getValue());

        $added = (new Measure())
            ->setCode('mg')
            ->setUnit($this->units['mg'])
            ->setValue(4);
        $measure = (new Measure())
            ->setCode('g')
            ->setUnit($this->units['g'])
            ->setValue(4)
            ->add($added);
        $this->assertEquals('mg', $measure->getCode());
        $this->assertEquals(4004, $measure->getValue());
        $this->assertEquals('mg', $added->getCode());
        $this->assertEquals(4, $added->getValue());
    }

    public function testAddDenominator(): void {
        $added = (new Measure())
            ->setCode('m')
            ->setDenominator('s')
            ->setDenominatorUnit($this->units['s'])
            ->setUnit($this->units['m'])
            ->setValue(10);
        $measure = (new Measure())
            ->setCode('km')
            ->setDenominator('h')
            ->setDenominatorUnit($this->units['h'])
            ->setUnit($this->units['km'])
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
            ->setDenominatorUnit($this->units['h'])
            ->setUnit($this->units['km'])
            ->setValue(1);
        $measure = (new Measure())
            ->setCode('m')
            ->setDenominator('s')
            ->setDenominatorUnit($this->units['s'])
            ->setUnit($this->units['m'])
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
        $measure = (new Measure())
            ->setCode('kg')
            ->setUnit($this->units['kg'])
            ->setValue(2)
            ->convert($this->units['g']);
        $this->assertEquals('g', $measure->getCode());
        $this->assertEquals(2000, $measure->getValue());

        $measure = (new Measure())
            ->setCode('mg')
            ->setUnit($this->units['mg'])
            ->setValue(4)
            ->convert($this->units['g']);
        $this->assertEquals('g', $measure->getCode());
        $this->assertEquals(0.004, $measure->getValue());
    }

    public function testConvertDenominator(): void {
        $measure = (new Measure())
            ->setCode('km')
            ->setDenominator('h')
            ->setDenominatorUnit($this->units['h'])
            ->setUnit($this->units['km'])
            ->setValue(10)
            ->convert($this->units['m'], $this->units['s']);
        $this->assertEquals('m', $measure->getCode());
        $this->assertEquals('s', $measure->getDenominator());
        $this->assertEqualsWithDelta(2.777_78, $measure->getValue(), 0.000_01);

        $measure = (new Measure())
            ->setCode('m')
            ->setDenominator('s')
            ->setDenominatorUnit($this->units['s'])
            ->setUnit($this->units['m'])
            ->setValue(1)
            ->convert($this->units['km'], $this->units['h']);
        $this->assertEquals('km', $measure->getCode());
        $this->assertEquals('h', $measure->getDenominator());
        $this->assertEquals(3.6, $measure->getValue());
    }

    public function testIsGreaterThanOrEqual(): void {
        $compared = (new Measure())
            ->setCode('m')
            ->setDenominator('s')
            ->setDenominatorUnit($this->units['s'])
            ->setUnit($this->units['m'])
            ->setValue(10);
        $measure = (new Measure())
            ->setCode('km')
            ->setDenominator('h')
            ->setDenominatorUnit($this->units['h'])
            ->setUnit($this->units['km'])
            ->setValue(10);
        $this->assertFalse($measure->isGreaterThanOrEqual($compared));
        $this->assertEquals('km', $measure->getCode());
        $this->assertEquals('h', $measure->getDenominator());
        $this->assertEquals(10, $measure->getValue());
        $this->assertEquals('m', $compared->getCode());
        $this->assertEquals('s', $compared->getDenominator());
        $this->assertEquals(10, $compared->getValue());
    }

    protected function setUp(): void {
        $durationRepo = $this->createMock(UnitRepository::class);
        /** @var Unit $h */
        $h = (new Unit())
            ->setRepo($durationRepo)
            ->setBase(3600)
            ->setCode('h');
        /** @var Unit $s */
        $s = (new Unit())
            ->setRepo($durationRepo)
            ->setBase(1)
            ->setCode('s')
            ->addChild($h);
        $durationRepo->method('children')->willReturn(collect([$h, $s]));

        $massRepo = $this->createMock(UnitRepository::class);
        /** @var Unit $mg */
        $mg = (new Unit())
            ->setRepo($massRepo)
            ->setBase(0.001)
            ->setCode('mg');
        /** @var Unit $kg */
        $kg = (new Unit())
            ->setRepo($massRepo)
            ->setBase(1000)
            ->setCode('kg');
        /** @var Unit $g */
        $g = (new Unit())
            ->setRepo($massRepo)
            ->setBase(1)
            ->setCode('g')
            ->addChild($kg)
            ->addChild($mg);
        $massRepo->method('children')->willReturn(collect([$mg, $kg, $g]));

        $distanceRepo = $this->createMock(UnitRepository::class);
        /** @var Unit $km */
        $km = (new Unit())
            ->setRepo($distanceRepo)
            ->setBase(1000)
            ->setCode('km');
        /** @var Unit $m */
        $m = (new Unit())
            ->setRepo($distanceRepo)
            ->setBase(1)
            ->setCode('m')
            ->addChild($km);
        $distanceRepo->method('children')->willReturn(collect([$mg, $kg, $g]));

        $this->units = [
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
