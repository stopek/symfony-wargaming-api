<?php

namespace App\Command;

use App\Factory\MoeFactory;
use App\Wargaming\WotClansApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMoeCommand extends Command
{
    protected static $defaultName = 'wg:update:moe';

    public function __construct(
        public WotClansApi $wotClansApi,
        private MoeFactory $moeFactory
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->wotClansApi->get('tanks/moe');
        if ($data->isEmptyResponse()) {
            $output->writeln('Tanks list is empty');
            return Command::FAILURE;
        }

        $data_response = $data->getResponse()->get();
        $update = $this->moeFactory->createUpdate($data_response);
        $this->moeFactory->create($data_response['Tanks'], $update);

        return Command::SUCCESS;
    }
}