<?php

namespace App\Command;

use App\Service\ApiClanService;
use App\Wargaming\Api\Clan;
use App\Wargaming\ServerApi;
use App\Wargaming\WotClansApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Proces aktualizujący wszystkie dostępne klany oraz
 * aktualizujący podstawowe informacje o graczach
 * należących do tego klanu.
 *
 * Proces sprawdza też już dodanych graczy i w przypadku
 * usunięcia lub zbanowania klanu automatycznie odpina
 * od klanu użytkowników.
 *
 * Odpinani są także gracze, którzy nie są już w tym klanie
 *
 * Class UpdateClansCommand
 * @package App\Command
 */
class UpdateClansCommand extends Command
{
    protected static $defaultName = 'wg:update:clans';

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
        $this->apiClanService->clansUpdater(new Clan($this->api));

        return Command::SUCCESS;
    }
}