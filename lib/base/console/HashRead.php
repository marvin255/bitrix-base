<?php

namespace app\base\console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Bitrix\Main\Application;

/**
 * Консольная команда, которая читает хэш битрикса.
 */
class HashRead extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('base:hash.read')
            ->setDescription("Reads bitrix's hash");
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Looking for bitrix's hash...</info>");

        $admin_passwordh = $this->getAdminPasswordh();
        if ($admin_passwordh) {
            $output->writeln("<info>admin_passwordh is: {$admin_passwordh}</info>");
        } else {
            $output->writeln('<error>admin_passwordh not found</error>');
        }

        $temporary_cache = $this->getTemporaryCache();
        if ($temporary_cache) {
            $output->writeln("<info>temporary_cache is: {$temporary_cache}</info>");
        } else {
            $output->writeln('<error>temporary_cache not found</error>');
        }
    }

    /**
     * Возвращает значение хэша из b_option.
     *
     * @return string|null
     */
    protected function getAdminPasswordh()
    {
        $connection = $this->getConnection();
        $res = $connection->query("SELECT * FROM `b_option` WHERE `NAME` = 'admin_passwordh'");
        $option = $res->fetch();

        return !empty($option['VALUE']) ? $option['VALUE'] : null;
    }

    /**
     * Возвращает хэш из /bitrix/modules/main/admin/define.php.
     *
     * @return string|null
     */
    protected function getTemporaryCache()
    {
        $file = $this->getDefinePath();
        $return = null;

        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (preg_match('#("|\')TEMPORARY_CACHE("|\')[\s,]*("|\')([^"\']+)("|\')#i', $content, $matches)) {
                $return = $matches[4];
            }
        }

        return $return;
    }

    /**
     * Возвращает объект для соединения с базой данных.
     *
     * @return \Bitrix\Main\DB\Connection
     */
    protected function getConnection()
    {
        return Application::getConnection();
    }

    /**
     * Возвращает абсолютный путь к /bitrix/modules/main/admin/define.php.
     *
     * @return string
     */
    protected function getDefinePath()
    {
        return dirname(dirname(dirname(__DIR__))) . '/web/bitrix/modules/main/admin/define.php';
    }
}
