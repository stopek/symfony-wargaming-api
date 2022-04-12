<?php

namespace App\Service;

use Exception;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CommandService.
 */
class CommandService
{
    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param $command
     * @param $env
     *
     * @return Response
     */
    public function doCommand($command, $env = null)
    {
        try {
            $kernel = $this->getKernel();
            if (null === $env) {
                $env = $kernel->getEnvironment();
            }


            $application = new Application($kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput([
                'command' => $command,
                '--env' => $env,
            ]);

            $output = new BufferedOutput(
                OutputInterface::VERBOSITY_NORMAL,
                true
            );
            $application->run($input, $output);

            $converter = new AnsiToHtmlConverter();
            $content = $output->fetch();

            return new Response(nl2br($converter->convert($content)));
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }
    }

    /**
     * @return KernelInterface
     */
    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }
}
