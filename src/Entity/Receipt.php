<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\FinishReceiptController;
use App\Repository\ReceiptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReceiptRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Post(),
        new Put(
            name: 'finish',
            uriTemplate: '/receipts/{id}/finish',
            controller: FinishReceiptController::class
        )
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
class Receipt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read'])]
    private bool $finished = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['read'])]
    private ?\DateTimeImmutable $finishedAt = null;

    #[ORM\OneToMany(mappedBy: 'receipt', targetEntity: ReceiptItem::class)]
    #[ApiProperty(fetchEager: true)]
    private Collection $receiptItems;

    public function __construct()
    {
        $this->receiptItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(\DateTimeImmutable $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * @return Collection<int, ReceiptItem>
     */
    public function getReceiptItems(): Collection
    {
        return $this->receiptItems;
    }

    public function addReceiptItem(ReceiptItem $receiptItem): self
    {
        if (!$this->receiptItems->contains($receiptItem)) {
            $this->receiptItems->add($receiptItem);
            $receiptItem->setReceipt($this);
        }

        return $this;
    }

    public function removeReceiptItem(ReceiptItem $receiptItem): self
    {
        if ($this->receiptItems->removeElement($receiptItem)) {
            // set the owning side to null (unless already changed)
            if ($receiptItem->getReceipt() === $this) {
                $receiptItem->setReceipt(null);
            }
        }

        return $this;
    }

    #[Groups(['read'])]
    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->receiptItems as $receiptItem) {
            /** @var ReceiptItem $receiptItem*/
            $total += $receiptItem->getAmount();
        }

        return $total;
    }

    #[Groups(['read'])]
    public function getProducts(): array
    {
        $products = [];
        foreach ($this->receiptItems as $receiptItem) {
            /** @var ReceiptItem $receiptItem*/
            $productId = $receiptItem->getProduct()->getId();
            if (!isset($products[$productId])) {
                $products[$productId] = ['amount' => 0, 'total' => 0];
            }
            $products[$productId]['name'] = $receiptItem->getProduct()->getName();
            $products[$productId]['amount'] += $receiptItem->getAmount();
            $products[$productId]['total'] += $receiptItem->getCost();
        }

        return $products;
    }

    #[Groups(['read'])]
    public function getTotalVatPerClass(): array
    {
        $vat = [];
        foreach ($this->receiptItems as $receiptItem) {
            /** @var ReceiptItem $receiptItem*/
            $vatClass = $receiptItem->getProduct()->getVat();
            if (!isset($vat[$vatClass])) {
                $vat[$vatClass] = 0;
            }
            $vat[$vatClass] += $receiptItem->getVat();
        }

        return $vat;
    }
}
