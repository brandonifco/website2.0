<?php

namespace App\Command;

use App\Service\TaxRateCreator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import-tax-rates', // Command name must match here
    description: 'Import Tax Rates from CSV FILE.'
)]
class ImportTaxRatesCommand extends Command
{
    private $taxRateCreator;

    public function __construct(TaxRateCreator $taxRateCreator)
    {
        parent::__construct();
        $this->taxRateCreator = $taxRateCreator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import tax rates from a CSV file.')
            ->setHelp('This command allows you to import tax rates from a CSV file and create them programmatically.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = __DIR__ . '/TestRates.csv'; // Adjust the path as needed

        if (!file_exists($filePath)) {
            $output->writeln('<error>CSV file not found at: ' . $filePath . '</error>');
            return Command::FAILURE;
        }

        $csv = array_map('str_getcsv', file($filePath));
        $headers = array_map('trim', $csv[0]);
        $rows = array_slice($csv, 1);

        $output->writeln('<info>Importing tax rates from the CSV file...</info>');

        foreach ($rows as $row) {
            $data = array_combine($headers, $row);

            try {
                $this->taxRateCreator->createTaxRate(
                    $data['Code'],
                    $data['Name'],
                    (float)$data['Amount'],
                    $data['Zone'],
                    $data['Category']
                );

                $output->writeln('<info>Tax Rate created: ' . $data['Name'] . '</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>Error creating tax rate: ' . $e->getMessage() . '</error>');
            }
        }

        $output->writeln('<info>Tax rates import completed.</info>');

        return Command::SUCCESS;
    }
}
