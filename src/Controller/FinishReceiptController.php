<?php

namespace App\Controller;

use App\Entity\Receipt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class FinishReceiptController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(Receipt $receipt): Receipt
    {
        $receipt->setFinished(true);
        $receipt->setFinishedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return $receipt;
    }
}
