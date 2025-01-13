<?php

use Symfony\Component\Dotenv\Dotenv;

// Bootstrap Symfony environment
require __DIR__ . '/config/bootstrap.php';

// Get the Symfony container
$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

// Check if a zone code is provided as a command-line argument
if ($argc < 2) {
    echo "Usage: php delete_zone.php <zone_code>\n";
    exit(1);
}

$zoneCode = $argv[1];

// Fetch the Zone repository
$zoneRepository = $container->get('sylius.repository.zone');
$entityManager = $container->get('doctrine.orm.entity_manager');

// Find the Zone
$zone = $zoneRepository->findOneBy(['code' => $zoneCode]);

if (!$zone) {
    echo "Zone not found: $zoneCode\n";
    exit(1);
}

// Check dependencies
$shippingMethodRepository = $container->get('sylius.repository.shipping_method');
$taxRateRepository = $container->get('sylius.repository.tax_rate');

// Check if the zone is used in shipping methods
$shippingMethods = $shippingMethodRepository->findBy(['zone' => $zone]);
if (!empty($shippingMethods)) {
    echo "Zone is in use by the following shipping methods:\n";
    foreach ($shippingMethods as $method) {
        echo "- " . $method->getName() . "\n";
    }
    echo "Please remove or update these shipping methods before deleting the zone.\n";
    exit(1);
}

// Check if the zone is used in tax rates
$taxRates = $taxRateRepository->findBy(['zone' => $zone]);
if (!empty($taxRates)) {
    echo "Zone is in use by the following tax rates:\n";
    foreach ($taxRates as $taxRate) {
        echo "- " . $taxRate->getName() . "\n";
    }
    echo "Please remove or update these tax rates before deleting the zone.\n";
    exit(1);
}

// Delete the Zone
$entityManager->remove($zone);
$entityManager->flush();

echo "Zone deleted successfully: $zoneCode\n";
?>