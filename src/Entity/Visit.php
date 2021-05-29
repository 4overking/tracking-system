<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\VisitRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=VisitRepository::class)
 * @ORM\Table(
 *     name="visits",
 *     indexes={
 *      @ORM\Index(name="visits_checkout_lookup_idx", columns={"is_checkout"}),
 *      @ORM\Index(name="visits_purchase_lookup_idx", columns={"client_id", "type", "date"}),
 *  }
 * )
 */
class Visit
{
    public const TYPE_NONE = 'none';
    public const TYPE_ORGANIC = 'organic';
    public const TYPE_OURS = 'ours';
    public const TYPE_FOREIGN = 'foreign';
    public const TYPE_DIRECT = 'direct';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotNull()
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("client_id")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $clientId;

    /**
     * @JMS\Type("string")
     * @JMS\SerializedName("User-Agent")
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userAgent;

    /**
     * @Assert\NotNull()
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("document.location")
     *
     * @ORM\Column(type="string", length=1024)
     */
    private $location;

    /**
     * @JMS\Type("string")
     * @JMS\SerializedName("document.referer")
     *
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $referer;

    /**
     * @Assert\NotNull()
     *
     * @JMS\Type("DateTime<'Y-m-d\TH:i:s.uT'>")
     * @JMS\SerializedName("date")
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @JMS\Exclude()
     *
     * @ORM\Column(type="string", length=10)
     */
    private $type = self::TYPE_NONE;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCheckout = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setReferer(?string $referer): self
    {
        $this->referer = $referer;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIsCheckout(): ?bool
    {
        return $this->isCheckout;
    }

    public function setIsCheckout(bool $isCheckout): self
    {
        $this->isCheckout = $isCheckout;

        return $this;
    }
}
