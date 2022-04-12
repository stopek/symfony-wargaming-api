<?php

namespace App\Command;

use App\Repository\ClanRepository;
use App\Repository\PlayerRepository;
use App\Repository\TankRepository;
use DateTime;
use Icamys\SitemapGenerator\SitemapGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UpdateSitemapCommand extends Command
{
    protected static $defaultName = 'update:sitemap';

    public function __construct(
        private PlayerRepository      $playerRepository,
        private ClanRepository        $clanRepository,
        private TankRepository        $tankRepository,
        private ParameterBagInterface $parameterBag
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputDir = getcwd();

        $path = $outputDir . '/../frontend/';
        $index = 'sitemap-index.xml';
        if (file_exists($path . $index)) {
            unlink($path . $index);
        }

        $generator = new SitemapGenerator($this->parameterBag->get('wg_frontend'), $path);

        $generator->setMaxUrlsPerSitemap(50000);
        $generator->setSitemapFileName("sitemap.xml");
        $generator->setSitemapIndexFileName($index);

        $custom = ['clans', 'search', 'expected', 'tanks', 'maps', 'contact', 'login'];
        foreach ($custom as $item) {
            $generator->addURL('/' . $item, new DateTime(), 'always', 1);
        }

        $players = $this->playerRepository->getAllPlayersForSitemap();
        foreach ($players as $player) {
            $generator->addURL('/player/' . $player['id'] . '/' . $player['name'], $player['updated_at'], 'daily', 0.8);
        }

        $clans = $this->clanRepository->getAllActiveClansTags();
        foreach ($clans as $tag) {
            $generator->addURL('/clan/' . $tag, new DateTime(), 'daily', 0.8);
        }

        $tanks = $this->tankRepository->getAllTanksIds();
        foreach ($tanks as $tank_id) {
            $generator->addURL('/tank/' . $tank_id, new DateTime(), 'weekly', 0.4);
        }

        $generator->flush();
        $generator->finalize();
        $generator->updateRobots();

        $generator->submitSitemap();

        return Command::SUCCESS;
    }
}