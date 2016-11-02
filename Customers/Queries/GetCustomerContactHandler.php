<?php
namespace Ntech\Customers\Queries;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Ntech\Customers\CustomerContact;
use Ntech\Customers\Exceptions\CustomerContactDoesNotExist;
use Ntech\Customers\Models\CustomerContactDoctrine\CustomerContactDoctrineModel;
use NtechUtility\Cqrs\Query\QueryHandler;

class GetCustomerContactHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function handle(GetCustomerContactQuery $query)
    {
        $qb = $this->entityManager->getRepository(CustomerContactDoctrineModel::class)->createQueryBuilder('contact');
        switch ($query->getContactType()) {
            case CustomerContact::PRIMARY_CONTACT:
                $qb->where('contact.primary = :contactBoolean');
                break;
            case CustomerContact::BILLING_CONTACT:
                $qb->where('contact.billing = :contactBoolean');
                break;
        }
        $qb->setParameters([
            'contactBoolean' => true
        ]);

        try {
            $contact = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            $qb->where('contact.primary = :contactBoolean');
            try {
                $contact = $qb->getQuery()->getSingleResult();
            } catch (NoResultException $e) {
                throw CustomerContactDoesNotExist::forType($query->getCustomerId(), $query->getContactType());
            }
        }
        return $contact;
    }
}
