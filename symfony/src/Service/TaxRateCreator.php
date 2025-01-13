<?php

namespace App\Service;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

class TaxRateCreator
{
    private $taxRateFactory;
    private $taxRateRepository;
    private $zoneRepository;
    private $taxCategoryRepository;

    public function __construct(
        FactoryInterface $taxRateFactory,
        RepositoryInterface $taxRateRepository,
        RepositoryInterface $zoneRepository,
        RepositoryInterface $taxCategoryRepository
    ) {
        $this->taxRateFactory = $taxRateFactory;
        $this->taxRateRepository = $taxRateRepository;
        $this->zoneRepository = $zoneRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
    }

    public function createTaxRate(): void
    {
        // Logic will be added in future steps
    }
}
