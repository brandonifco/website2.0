<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Addressing\Model\Zone;
use Sylius\Component\Addressing\Model\ZoneMember;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportZonesCommand extends Command
{
    protected static $defaultName = 'app:import-zones';

    private EntityManagerInterface $entityManager;
    private FactoryInterface $zoneFactory;
    private FactoryInterface $zoneMemberFactory;
    private RepositoryInterface $zoneRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FactoryInterface $zoneFactory,
        FactoryInterface $zoneMemberFactory,
        RepositoryInterface $zoneRepository
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->zoneFactory = $zoneFactory;
        $this->zoneMemberFactory = $zoneMemberFactory;
        $this->zoneRepository = $zoneRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import tax zones from CSV file.')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        if (!file_exists($filePath)) {
            $output->writeln('<error>File not found!</error>');
            return Command::FAILURE;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $output->writeln('<error>Unable to open file!</error>');
            return Command::FAILURE;
        }

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            [$state, $zipCode, $taxRegionName, $estimatedCombinedRate] = $row;

            // Check if zone exists
            $zone = $this->zoneRepository->findOneBy(['code' => $taxRegionName]);
            if (!$zone) {
                $zone = $this->zoneFactory->createNew();
                $zone->setCode($taxRegionName);
                $zone->setName($taxRegionName);
                $zone->setType(Zone::TYPE_ZONE);
                $this->entityManager->persist($zone);
            }

            // Add ZipCode to Zone
            $zoneMember = $this->zoneMemberFactory->createNew();
            $zoneMember->setCode($zipCode);
            $zone->addMember($zoneMember);

            $this->entityManager->persist($zoneMember);
        }

        fclose($handle);
        $this->entityManager->flush();

        $output->writeln('<info>Zones imported successfully!</info>');
        return Command::SUCCESS;
    }
}
