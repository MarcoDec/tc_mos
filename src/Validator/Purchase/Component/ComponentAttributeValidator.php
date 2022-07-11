<?php

namespace App\Validator\Purchase\Component;

use App\Doctrine\DBAL\Types\Purchase\Component\AttributeType;
use App\Entity\Purchase\Component\ComponentAttribute;
use App\Validator\Purchase\Component\ComponentAttribute as ComponentAttributeAttribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ComponentAttributeValidator extends ConstraintValidator {
    public function validate(mixed $value, Constraint $constraint): void {
        if (!($constraint instanceof ComponentAttributeAttribute)) {
            throw new UnexpectedTypeException($constraint, ComponentAttributeAttribute::class);
        }

        if (empty($value)) {
            return;
        }

        if (!($value instanceof ComponentAttribute)) {
            throw new UnexpectedValueException($value, ComponentAttribute::class);
        }

        switch ($value->getType()) {
            case AttributeType::TYPE_PERCENT:
                if ($value->getValue() < 0 || ($value->getValue() > 100)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ type }}', 'un pourcentage')
                        ->addViolation();
                }
            break;
            case AttributeType::TYPE_COLOR:
                if (empty($value->getColor())) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ type }}', 'une couleur')
                        ->addViolation();
                }
            break;
            case AttributeType::TYPE_UNIT:
                if (empty($value->getMeasure()->getCode()) || empty($value->getMeasure()->getValue())) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ type }}', 'une mesure')
                        ->addViolation();
                }
        }
    }
}
