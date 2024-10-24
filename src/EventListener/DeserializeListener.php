<?php

namespace App\EventListener;

use ApiPlatform\Core\EventListener\DeserializeListener as ApiDeserializeListener;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Collection;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Purchase\Supplier\Supplier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class DeserializeListener {
    public function __construct(
        private readonly ApiDeserializeListener $decorated,
        private readonly DenormalizerInterface $denormalizer,
        private readonly EntityManagerInterface $em,
        private readonly SerializerContextBuilderInterface $serializer
    ) {
    }

    public function __invoke(RequestEvent $event): void {
        $request = $event->getRequest();
//        dump($request);
        if ($request->isMethodCacheable() || $request->isMethod(Request::METHOD_DELETE)) {
            return;
        }

        if (in_array($request->getContentType(), ['form', 'multipart'])) {
            $this->denormalizeMultipart($request);
        } else {
           if ($request->getMethod()==='PATCH' && str_contains($request->getPathInfo(),'/api/suppliers/')) {
               preg_match('/\/api\/suppliers\/(\d+)/', $request->getPathInfo(),$supplierIdArr);
               $supplierId = intval($supplierIdArr[1]);
               preg_match_all('/\/api\/companies\/(\d+)/', $request->getContent(),$companyIdsArr);
               $companyIds = $companyIdsArr[1];
               $supplierRepository = $this->em->getRepository(Supplier::class);
               $companyRepository = $this->em->getRepository(Company::class);
               $supplier = $supplierRepository->find($supplierId);
               /** @var Company $item */
              foreach ($supplier->getAdministeredBy() as $item){
                 $currentId = $item->getId();
                  if (!in_array($currentId, $companyIds)) {
                     $aCompany = $companyRepository->find($currentId);
                     $supplier->removeAdministeredBy($aCompany);
                  }
               }
               foreach ($companyIds as $companyId) {
                  $aCompany = $companyRepository->find($companyId);
                  $supplier->addAdministeredBy($aCompany);
               }
               $this->em->persist($supplier);
               $this->em->flush();
           }
           if ($request->getMethod()==='POST' && str_contains($request->getPathInfo(),'/api/balance-sheet-items/from-excel')){
//               dump("POST from Excel");
           } else $this->decorated->onKernelRequest($event);
        }
    }

    private function denormalizeMultipart(Request $request): void {
        if (empty($attrs = RequestAttributesExtractor::extractAttributes($request))) {
            return;
        }
        /** @var array{resource_class: class-string} $context */
        $context = $this->serializer->createFromRequest($request, false, $attrs);
        if (!empty($populated = $request->attributes->get('data'))) {
            $context['object_to_populate'] = $populated;
        }

        $request->attributes->set('data', $this->denormalizer->denormalize(
            data: $this->getData($context, $request),
            type: $attrs['resource_class'],
            context: $context
        ));
    }

    /**
     * @param array{resource_class: class-string} $context
     *
     * @return mixed[]
     */
    private function getData(array $context, Request $request): array {
        $metadata = $this->em->getClassMetadata($context['resource_class']);
        return Collection::collect(array_merge($request->request->all(), array_filter($request->files->all())))
            ->map(static function ($value, string $name) use ($metadata) {
                if ($metadata->getTypeOfField($name) === 'boolean') {
                    return $value === 'true';
                }
                if (is_string($value) && strlen($value) === 0) {
                    return;
                }
                return $value;
            })
            ->all();
    }
}
