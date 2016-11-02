<?php
namespace Ntech\Customers\Models\CustomerContacts\ListView;

use Ntech\Customers\Models\CustomerContactDoctrine\CustomerContactDoctrineModel;
use Ntech\Support\ValueObjects\ValueObject;
use Ntech\Uuid\Uuid;

class CustomerContactListItem extends ValueObject
{
    /**
     * @var Uuid
     */
    protected $id;
    /**
     * @var bool
     */
    protected $primary;
    /**
     * @var string
     */
    protected $alias;
    /**
     * @var string
     */
    protected $fullName;
    /**
     * @var string
     */
    protected $email;
    /**
     * @var string
     */
    protected $phone;

    public function __construct(
        Uuid $contactId,
        bool $primary,
        string $alias,
        string $fullName,
        string $email,
        string $phone
    ) {
        $this->id = $contactId;
        $this->primary = $primary;
        $this->alias = $alias;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->phone = $phone;
    }

    public static function fromDoctrineModel(CustomerContactDoctrineModel $doctrine)
    {
        return new self(
            $doctrine->getId(),
            $doctrine->isPrimary(),
            $doctrine->getAlias(),
            $doctrine->getFullName(),
            $doctrine->getEmail(),
            $doctrine->getPhone()
        );
    }

}
