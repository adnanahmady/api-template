<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata as Api;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Api\GetCollection(),
        new Api\Get(),
        new Api\Post(),
        new Api\Put(),
        new Api\Patch(),
    ],
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 10,
)]
#[ORM\Entity]
class Manufacturer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $name = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $description = '';

    #[ORM\Column(length: 3)]
    #[Assert\NotBlank]
    private string $countryCode = '';

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull]
    private ?\DateTimeInterface $listedDate = null;

    #[ORM\OneToMany(
        mappedBy: 'manufacturer',
        targetEntity: Product::class,
        cascade: ['persist', 'remove']
    )]
    private iterable $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getListedDate(): ?\DateTimeInterface
    {
        return $this->listedDate;
    }

    /**
     * @param \DateTimeInterface|null $listedDate
     */
    public function setListedDate(?\DateTimeInterface $listedDate): void
    {
        $this->listedDate = $listedDate;
    }

    public function getProducts(): iterable|ArrayCollection
    {
        return $this->products;
    }
}