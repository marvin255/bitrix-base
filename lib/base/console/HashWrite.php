<?php

namespace app\base\console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Консольная команда, которая заменяет хэш битрикса.
 */
class HashWrite extends HashRead
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('base:hash.write')
            ->addArgument(
                'admin_passwordh',
                InputArgument::REQUIRED,
                'Hash for options table'
            )
            ->addArgument(
                'temporary_cache',
                InputArgument::REQUIRED,
                'Hash for file'
            )
            ->setDescription("Writes bitrix's hash");
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $output->writeln("<info>Writing new bitrix's hash...</info>");

        $this->setAdminPasswordh($input->getArgument('admin_passwordh'));
        $this->setTemporaryCache($input->getArgument('temporary_cache'));

        $output->writeln("<info>Bitrix's hash changed</info>");
    }

    /**
     * Задает значение хэша из b_option.
     *
     * @param string $admin_passwordh
     */
    protected function setAdminPasswordh($admin_passwordh)
    {
        $connection = $this->getConnection();
        $arPrepare = $connection->getSqlHelper()->prepareUpdate('b_option', ['VALUE' => $admin_passwordh]);
        $sql = "UPDATE `b_option` SET {$arPrepare[0]} WHERE `NAME` = 'admin_passwordh'";
        $connection->query($sql);
    }

    /**
     * Задает хэш в /bitrix/modules/main/admin/define.php.
     *
     * @param string $temporary_cache
     */
    protected function setTemporaryCache($temporary_cache)
    {
        $file = $this->getDefinePath();
        $oldHash = $this->getTemporaryCache();

        if ($oldHash) {
            $content = file_get_contents($file);
            file_put_contents($file, str_replace($oldHash, $temporary_cache, $content));
        }
    }
}
