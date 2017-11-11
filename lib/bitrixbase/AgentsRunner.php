<?php

namespace app\bitrixbase;

use Bitrix\Sender\MailingManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Консольная командя для запуска агентов битрикса по cron.
 */
class AgentsRunner extends Command
{
    /**
     * @var string
     */
    protected $documentRoot;

    /**
     * @inheritdoc
     * @throws \InvalidArgumentException
     */
    public function __construct($documentRoot)
    {
        if (empty($documentRoot)) {
            throw new \InvalidArgumentException('Document root can not be empty');
        }
        $this->documentRoot = $documentRoot;

        parent::__construct();
    }

    /**
     * @inheritdoc
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this->setName('bitrixbase:agents.runner')
            ->setDescription('Run bitrix agents from cli script');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Running bitrix's agents...</info>");

        \CAgent::CheckAgents();
        define('BX_CRONTAB_SUPPORT', true);
        define('BX_CRONTAB', true);
        \CEvent::CheckEvents();

        if (\CModule::IncludeModule('sender')) {
            MailingManager::checkPeriod(false);
            MailingManager::checkSend();
        }

        require $this->documentRoot . '/bitrix/modules/main/tools/backup.php';

        $output->writeln("<info>Bitrix's agents completed</info>");
    }
}
