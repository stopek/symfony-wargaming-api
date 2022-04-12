<?php

namespace App\Command;

use App\Entity\ExpUpdate;
use App\Factory\ExpTanksFactory;
use App\Wargaming\WotClansApi;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateExpectedCommand extends Command
{
    protected static $defaultName = 'wg:update:expected';

    public function __construct(
        public WotClansApi      $wotClansApi,
        private ExpTanksFactory $expTanksFactory,
        private ManagerRegistry $managerRegistry
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->wotClansApi->get('tanks/wn8');
        if ($data->isEmptyResponse()) {
            $output->writeln('Tanks list is empty');
            return Command::FAILURE;
        }

        $data_response = $data->getResponse()->get();
        $manager = $this->managerRegistry->getManager();

        $update = new ExpUpdate();
        $update->setIsActive(true);
        $update->setVersion($data_response['Version']);
        $manager->persist($update);
        $manager->flush();

        $this->expTanksFactory->create($data_response['Tanks'], $update);

        return Command::SUCCESS;
    }
}