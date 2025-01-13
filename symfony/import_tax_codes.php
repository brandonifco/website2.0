<?php

use Symfony\Component\Dotenv\Dotenv;

// Bootstrap Symfony environment
require __DIR__ . '/config/bootstrap.php';

// Path to the CSV file
$csvFilePath = __DIR__ . '/WisconsinTaxRates.csv';

// Get the Symfony container
$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

// Open the CSV file and read the data
if (($handle = fopen($csvFilePath, 'r')) !== false) {
    // Read the header row to map the columns
    $header = fgetcsv($handle);

    while (($data = fgetcsv($handle)) !== false) {
        // Map CSV columns to variables
        $row = array_combine($header, $data);
        if (!$row) {
            echo "Failed to parse CSV row.\n";
            continue;
        }

        $taxRateCode = $row['tax_rate_code'] ?? null;
        $taxRateName = $row['tax_rate_name'] ?? null;
        $taxRateAmount = isset($row['tax_rate_amount']) ? (float) $row['tax_rate_amount'] : null;
        $zoneCode = $row['zone_code'] ?? null;
        $taxCategoryCode = $row['tax_category_code'] ?? null;

        if (!$taxRateCode || !$taxRateName || !$taxRateAmount || !$zoneCode || !$taxCategoryCode) {
            echo "Missing required data in row.\n";
            continue;
        }

        // Create a new TaxRate
        /** @var \Sylius\Component\Taxation\Model\TaxRateInterface $taxRate */
        $taxRate = $container->get('sylius.factory.tax_rate')->createNew();
        $taxRate->setCode($taxRateCode);
        $taxRate->setName($taxRateName);
        $taxRate->setAmount($taxRateAmount);
        $taxRate->setCalculator('default');

        // Get the Zone from the repository
        $zone = $container->get('sylius.repository.zone')->findOneBy(['code' => $zoneCode]);
        if ($zone) {
            $taxRate->setZone($zone);
        } else {
            echo "Zone not found: $zoneCode\n";
            continue;
        }

        // Get the Tax Category from the repository
        $taxCategory = $container->get('sylius.repository.tax_category')->findOneBy(['code' => $taxCategoryCode]);
        if ($taxCategory) {
            $taxRate->setCategory($taxCategory);
        } else {
            echo "Tax Category not found: $taxCategoryCode\n";
            continue;
        }

        // Persist the Tax Rate
        $container->get('sylius.repository.tax_rate')->add($taxRate);
        echo "Tax rate added: $taxRateCode\n";
    }
    fclose($handle);
} else {
    echo "Failed to open CSV file: $csvFilePath\n";
}
