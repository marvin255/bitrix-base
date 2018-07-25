<?php

namespace app\base\console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Консольная команда для управления кэшем битрикса.
 */
class CacheClear extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('base:cache.clear')
            ->setDescription('Clears bitrix cache');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Clearing bitrix's cache...</info>");
        BXClearCache(true, '/');
        $output->writeln("<info>Bitrix's cache cleared</info>");
    }
}
