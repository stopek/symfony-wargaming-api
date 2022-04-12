<?php

namespace App\Command;

use App\Service\ApiTankService;
use App\Wargaming\Api\Encyclopedia;
use App\Wargaming\ServerApi;
use App\Wargaming\WotClansApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUpdateTanksCommand extends Command
{
    protected static $defaultName = 'wg:update:tanks';

    public function __construct(
        public WotClansApi     $wotClansApi,
        private ApiTankService $apiTankService,
        private ServerApi      $api
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->apiTankService->createOrUpdate(new Encyclopedia($this->api));
        $output->writeln($result['message']);

        return Command::SUCCESS;
    }
}