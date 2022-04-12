<?php

namespace App\Command;

use App\Service\PlayersHistoryService;
use App\Wargaming\WotClansApi;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SavePlayersHistoryCommand extends Command
{
    protected static $defaultName = 'wg:save:players:history';

    public function __construct(
        public WotClansApi            $wotClansApi,
        private PlayersHistoryService $playersHistoryService
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->playersHistoryService->saveUserOverallStatistics(100, new DateTime());
        $output->writeln($result['message']);

        return Command::SUCCESS;
    }
}