<?php

namespace App\Validator;

use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Service\MeasureHydrator;
use App\Validator\Measure as MeasureAttribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Psr\Log\LoggerInterface;

final class MeasureValidator extends ConstraintValidator {
    
    public function __construct(
        private LoggerInterface $logger,
        private MeasureHydrator $hydrator
    ) {
    }
    public function validate(mixed $value, Constraint $constraint): void {
        #$this->logger->debug('MeasureValidator:validate',[$value, $constraint]);
        if (!($constraint instanceof MeasureAttribute)) {
            throw new UnexpectedTypeException($constraint, MeasureAttribute::class);
        }

        if (empty($value)) {
            return;
        }
        $unitValue = $value->getUnit();
        $codeValue = $value->getCode();
        $valueValue = $value->getValue();
        $test1 = ($unitValue === null);
        $test2 = ($codeValue === null);
        $test3 = ($valueValue == 0);
//        dump([
//            'value' => $value,
//            'unitValue' => $unitValue,
//            'codeValue' => $codeValue,
//            'valueValue' => $valueValue,
//            'test1' => $test1,
//            'test2' => $test2,
//            'test3' => $test3
//        ]);
        if (($unitValue === null) && ($codeValue === null) && ($valueValue == 0)) {
//            dump('return');
            return;
        }
        if (!($value instanceof Measure)) {
            throw new UnexpectedValueException($value, Measure::class);
        }
        $unit = $this->getUnit();
        if ($value->getUnit() === null) {
            $this->hydrator->hydrateUnit($value);
        }
        if (isset($unitValue) && !$unitValue->has($unit)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ unit }}', (string) $this->getUnit()->getName())
                ->addViolation();
        }
        if ($constraint->positive && $value->getValue() < 0) {
            $this->context->buildViolation($constraint->positiveMessage)->addViolation();
        }
    }

    private function getObject(): MeasuredInterface {
        if (($object = $this->context->getObject()) instanceof MeasuredInterface) {
            return $object;
        }
        throw new UnexpectedValueException($object, MeasuredInterface::class);
    }

    private function getUnit(): Unit {
       $myObject=$this->getObject();
       $objectUnit = $myObject->getUnit();
//       dump(['myObject' => $myObject, 'objectUnit' => $objectUnit]);
       //dump(["MeasureValidator::getUnit myObject" => $myObject, "getUnit"=> $myObject->getUnit()]);
        if (!empty($objectUnit)) {
            return $objectUnit;
        }
        throw new InvalidArgumentException('Unit is not set in MeasuredInterface object.');
    }
}
