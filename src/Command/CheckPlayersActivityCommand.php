<?php

namespace App\Command;

use App\Service\ApiPlayerService;
use App\Wargaming\Api\Account;
use App\Wargaming\ServerApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Proces sprwający czy użytkownik jest aktywny oraz oznaczająca
 * wszystkich, którzy nie zagrali bitwy w ciągu 2 miesięcy jako
 * graczy nieaktywnych.
 *
 * Class CheckPlayersActivityCommand
 * @package App\Command
 */
class CheckPlayersActivityCommand extends Command
{
    protected static $defaultName = 'wg:check:players:activity';

    public function __construct(
        private ApiPlayerService $apiPlayerService,
        private ServerApi        $api
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->apiPlayerService->checkPlayersActivity(new Account($this->api));

        $output->writeln($result['message']);

        return Command::SUCCESS;
    }
}