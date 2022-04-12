<?php

namespace App\Command;

use App\Service\ApiPlayerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetPlayersInactiveCommand extends Command
{
    protected static $defaultName = 'wg:set:players:inactive';

    public function __construct(private ApiPlayerService $apiPlayerService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->apiPlayerService->setPlayersInactive();

        $output->writeln($result['message']);

        return Command::SUCCESS;
    }
}