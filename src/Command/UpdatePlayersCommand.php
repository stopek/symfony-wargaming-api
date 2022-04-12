<?php

namespace App\Command;

use App\Service\ApiPlayerService;
use App\Wargaming\Api\Account;
use App\Wargaming\Api\Tanks;
use App\Wargaming\ServerApi;
use App\Wargaming\WotClansApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePlayersCommand extends Command
{
    protected static $defaultName = 'wg:update:players';

    public function __construct(
        public WotClansApi       $wotClansApi,
        private ApiPlayerService $apiPlayerService,
        private ServerApi        $api
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->apiPlayerService->updatePlayersStatistics(
            new Account($this->api),
            new Tanks($this->api),
            $output,
            false
        );

        $output->writeln($result['message']);

        return Command::SUCCESS;
    }
}