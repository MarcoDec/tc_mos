<?php

namespace App\Tests\Entity\Embeddable;

use App\Doctrine\Entity\Embeddable\Measure;
use App\Doctrine\Entity\Management\Unit;
use PHPUnit\Framework\TestCase;

final class MeasureTest extends TestCase {
    /** @var array<string, Unit> */
    private array $units = [];

    public function testAdd(): void {
        $measure = (new Measure())
            ->setCode('g')
            ->setUnit($this->units['g'])
            ->setValue(2)
            ->add(
                (new Measure())
                    ->setCode('kg')
                    ->setUnit($this->units['kg'])
                    ->setValue(2)
            );
        $this->assertEquals('g', $measure->getCode());
        $this->assertEquals(2002, $measure->getValue());

        $measure = (new Measure())
            ->setCode('g')
            ->setUnit($this->units['g'])
            ->setValue(4)
            ->add(
                (new Measure())
                    ->setCode('mg')
                    ->setUnit($this->units['mg'])
                    ->setValue(4)
            );
        $this->assertEquals('mg', $measure->getCode());
        $this->assertEquals(4004, $measure->getValue());
    }

    public function testAddDenominator(): void {
        $measure = (new Measure())
            ->setCode('km')
            ->setDenominator('h')
            ->setDenominatorUnit($this->units['h'])
            ->setUnit($this->units['km'])
            ->setValue(10)
            ->add(
                (new Measure())
                    ->setCode('m')
                    ->setDenominator('s')
                    ->setDenominatorUnit($this->units['s'])
                    ->setUnit($this->units['m'])
                    ->setValue(10)
            );
        $this->assertEquals('m', $measure->getCode());
        $this->assertEquals('s', $measure->getDenominator());
        $this->assertEqualsWithDelta(12.777_78, $measure->getValue(), 0.000_01);

        $measure = (new Measure())
            ->setCode('m')
            ->setDenominator('s')
            ->setDenominatorUnit($this->units['s'])
            ->setUnit($this->units['m'])
            ->setValue(1)
            ->add(
                (new Measure())
                    ->setCode('km')
                    ->setDenominator('h')
                    ->setDenominatorUnit($this->units['h'])
                    ->setUnit($this->units['km'])
                    ->setValue(1)
            );
        $this->assertEquals('m', $measure->getCode());
        $this->assertEquals('s', $measure->getDenominator());
        $this->assertEqualsWithDelta(1.277_78, $measure->getValue(), 0.000_01);
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

    protected function setUp(): void {
        $h = (new Unit())->setBase(3600)->setCode('h');
        $mg = (new Unit())->setBase(0.001)->setCode('mg');
        $kg = (new Unit())->setBase(1000)->setCode('kg');
        $km = (new Unit())->setBase(1000)->setCode('km');
        $this->units = [
            'h' => $h,
            'g' => (new Unit())
                ->setBase(1)
                ->setCode('g')
                ->addChild($kg)
                ->addChild($mg),
            'kg' => $kg,
            'km' => $km,
            'm' => (new Unit())
                ->setBase(1)
                ->setCode('m')
                ->addChild($km),
            'mg' => $mg,
            's' => (new Unit())
                ->setBase(1)
                ->setCode('s')
                ->addChild($h),
        ];
    }
}
