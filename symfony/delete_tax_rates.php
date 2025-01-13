<?php

use Symfony\Component\Dotenv\Dotenv;

// Bootstrap Symfony environment
require __DIR__ . '/config/bootstrap.php';

// Get the Symfony container
$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

// Fetch the TaxRate repository
$taxRateRepository = $container->get('sylius.repository.tax_rate');

// Fetch all Tax Rates
$taxRates = $taxRateRepository->findAll();

if (empty($taxRates)) {
    echo "No tax rates found to delete.\n";
    exit;
}

// Loop through and delete each Tax Rate
$entityManager = $container->get('doctrine.orm.entity_manager');
foreach ($taxRates as $taxRate) {
    echo "Deleting Tax Rate: " . $taxRate->getCode() . "\n";
    $entityManager->remove($taxRate);
}

// Flush the deletions
$entityManager->flush();

echo "All tax rates have been deleted successfully.\n";
?>