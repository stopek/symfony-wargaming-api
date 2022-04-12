<?php

namespace App\Command;

use App\Service\ApiClanService;
use App\Wargaming\Api\Clan;
use App\Wargaming\ServerApi;
use App\Wargaming\WotClansApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateClansCommand extends Command
{
    protected static $defaultName = 'wg:create:clans';

    public function __construct(
        public WotClansApi     $wotClansApi,
        private ApiClanService $apiClanService,
        private ServerApi      $api
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->apiClanService->clansCreator(new Clan($this->api));

        return Command::SUCCESS;
    }
}