<?php

namespace App\DataFixtures\Purchase\Component;

use App\Entity\Purchase\Component\Attribute;
use App\Entity\Purchase\Component\Family;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use InvalidArgumentException;

final class AttributeFixtures extends Fixture {
    public function __construct(private readonly string $attrJson, private readonly string $familyJson) {
    }

    private static function getContent(string $file): string {
        if (empty($content = file_get_contents($file))) {
            throw new InvalidArgumentException("Invalid file: $file");
        }
        return $content;
    }

    public function load(ObjectManager $manager): void {
        /** @var array{libelle: string}[] $attrJson */
        $attrJson = json_decode(self::getContent($this->attrJson), true);
        $decoded = collect($attrJson);
        /** @var array{family_name:string, id: int}[] $familyJson */
        $familyJson = json_decode(self::getContent($this->familyJson), true);
        $oldFamilies = collect($familyJson);
        $attrRepo = $manager->getRepository(Attribute::class);
        $familyRepo = $manager->getRepository(Family::class);
        foreach ($attrRepo->findAll() as $attribute) {
            $data = $decoded->first(static fn (array $old): bool => $old['libelle'] === $attribute->getName());
            if (!empty($data)) {
                foreach (explode('#', $data['attribut_id_family']) as $id) {
                    $oldFamily = $oldFamilies->first(static fn (array $old): bool => $old['id'] === $id);
                    if (!empty($oldFamily)) {
                        $family = $familyRepo->findOneBy(['name' => $oldFamily['family_name']]);
                        if (!empty($family)) {
                            $attribute->addFamily($family);
                        }
                    }
                }
            }
        }
        $manager->flush();
    }
}
