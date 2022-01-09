<?php

namespace App\Serializer\Normalizer;

use App\Entity\Hr\Employee\Employee;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EmployeeNormalizer implements CacheableSupportsMethodInterface, NormalizerInterface {
    public function __construct(private ObjectNormalizer $normalizer, private RequestStack $requestStack) {
    }

    public function hasCacheableSupportsMethod(): bool {
        return true;
    }

    /**
     * @return mixed[]
     */
    public function normalize($object, $format = null, array $context = []): array {
        $data = $this->normalizer->normalize($object, $format, $context);

        $request = $this->requestStack->getCurrentRequest();
        $process = null;

        if (null !== $request) {
            $process = $request->attributes->get('process');

            if ($process && is_array($data)) {
                $returnedData = [];

                switch ($process) {
                    case 'main':
                        $returnedData = [
                            'notes' => $data['notes'] ?? null
                        ];
                        break;
                    case 'hr':
                        $returnedData = [
                            'name' => $data['name'] ?? null,
                            'surname' => $data['surname'] ?? null,
                            'initials' => $data['initials'] ?? null,
                            'address' => $data['address'] ?? null,
                            'birthday' => $data['birthday'] ?? null,
                            'birthCity' => $data['birthCity'] ?? null,
                            'gender' => $data['gender'] ?? null,
                            'socialSecurityNumber' => $data['socialSecurityNumber'] ?? null,
                            'situation' => $data['situation'] ?? null,
                            'levelOfStudy' => $data['levelOfStudy'] ?? null,
                            'entryDate' => $data['entryDate'] ?? null,
                        ];
                        break;
                    case 'it':
                        $returnedData = [
                            'roles' => $data['roles'] ?? null,
                            'username' => $data['username'] ?? null,
                            'password' => $data['password'] ?? null,
                            'userEnabled' => $data['userEnabled'] ?? null,
                        ];
                        break;
                    case 'production':
                        $returnedData = [
                            'team' => $data['team'] ?? null
                        ];
                        break;
                    default:
                        break;
                }

                return $returnedData;
            }
        }

        return is_array($data) ? $data : [];
    }

    public function supportsNormalization($data, $format = null): bool {
        return $data instanceof Employee;
    }
}
