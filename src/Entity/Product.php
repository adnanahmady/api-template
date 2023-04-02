<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[
    ApiResource(
        operations: [
            new GetCollection(security: 'is_granted("ROLE_USER")'),
            new Post(security: 'is_granted("ROLE_ADMIN")'),
            new Put(security: 'is_granted("ROLE_USER") and object.getOwner() == user'),
            new Patch(security: 'is_granted("ROLE_USER")'),
            new Delete(security: 'is_granted("ROLE_USER")'),
        ],
        normalizationContext: [
            'groups' => ['product.read']
        ],
        denormalizationContext: [
            'groups' => ['product.write']
        ],
        paginationItemsPerPage: 5
    ),
    ApiResource(
        uriTemplate: '/api/products/{id}/manufacturer',
        operations: [new Get(security: 'is_granted("ROLE_USER")')],
        uriVariables: [
            'id' => new Link(
                fromProperty: 'products',
                fromClass: Manufacturer::class,
            )
        ]
    ),
    ApiFilter(
        filterClass: SearchFilter::class,
        properties: [
            'name' => SearchFilterInterface::STRATEGY_PARTIAL,
            'description' => SearchFilterInterface::STRATEGY_PARTIAL,
            'manufacturer.countryCode' => SearchFilterInterface::STRATEGY_EXACT,
            'manufacturer.id' => SearchFilterInterface::STRATEGY_EXACT,
        ]
    ),
    ApiFilter(
        filterClass: OrderFilter::class,
        properties: [
            'issueDate' => OrderFilterInterface::DIRECTION_ASC
        ]
    )
]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['product.read', 'product.write'])]
    private ?string $mpn = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups(['product.read', 'product.write'])]
    private string $name = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['product.read', 'product.write'])]
    private string $description = '';

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull]
    #[Groups(['product.read', 'product.write'])]
    private ?\DateTimeInterface $issueDate = null;

    #[ORM\ManyToOne(
        targetEntity: Manufacturer::class,
        inversedBy: 'products'
    )]
    #[Groups(['product.read', 'product.write'])]
    #[Assert\NotNull]
    private ?Manufacturer $manufacturer = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'products')]
    #[Groups(['product.read', 'product.write'])]
    private ?User $owner = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    /**
     * @param string|null $mpn
     */
    public function setMpn(?string $mpn): void
    {
        $this->mpn = $mpn;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    /**
     * @param \DateTimeInterface|null $issueDate
     */
    public function setIssueDate(?\DateTimeInterface $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

    /**
     * @return Manufacturer|null
     */
    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    /**
     * @param Manufacturer|null $manufacturer
     */
    public function setManufacturer(?Manufacturer $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}