<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ReceiptItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ReceiptItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(),
        new Post(),
        new Put(denormalizationContext: ['groups' => ['put']])
    ],
    normalizationContext: ['groups' => ['receipt_item.read']],
    denormalizationContext: ['groups' => ['write']],
)]
class ReceiptItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['receipt_item.read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['receipt_item.read'])]
    private ?int $cost = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['receipt_item.read', 'write', 'put'])]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?int $amount = null;

    #[ORM\Column]
    #[Groups(['receipt_item.read'])]
    private ?int $vat = null;

    #[ORM\Column]
    #[Groups(['receipt_item.read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['receipt_item.read', 'write'])]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'receiptItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['receipt_item.read', 'write'])]
    private ?Receipt $receipt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getVat(): ?int
    {
        return $this->vat;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getReceipt(): ?Receipt
    {
        return $this->receipt;
    }

    public function setReceipt(?Receipt $receipt): self
    {
        $this->receipt = $receipt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    private function calculateAndSetCost()
    {
        $this->cost = $this->product->getCost() * $this->amount;
        $this->vat = $this->cost * ($this->product->getVat()/100);
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->calculateAndSetCost();
    }

    #[ORM\PreUpdate]
    public function calculateCost(): void
    {
        $this->calculateAndSetCost();
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->receipt->isFinished()) {
            $context->buildViolation(sprintf('Receipt #%s is already finished', $this->receipt->getId()))
                ->atPath('receipt')
                ->addViolation();
        }
    }
}
