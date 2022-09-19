<?php

namespace Notamedia\ConsoleJedi\Iblock\Command;

use Bitrix\Main\Loader;
use Notamedia\ConsoleJedi\Application\Command\BitrixCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command create faceted index block(s)
 */
class CreateIndexCommand extends BitrixCommand
{

    protected function configure()
    {
        $this
            ->setName('iblock:createindex')
            ->setDescription('Create faceted index block(s)')
            ->addArgument(
                'ID',
                InputArgument::REQUIRED,
                'Id block(s)'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        Loader::includeModule('iblock');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $index = \Bitrix\Iblock\PropertyIndex\Manager::createIndexer($input->getArgument('ID'));
        $index->startIndex();

        while ($countIndexed < $index->estimateElementCount()) {
            $countIndexed += $index->continueIndex(20);
        }

        $index->endIndex();

        \CBitrixComponent::clearComponentCache("bitrix:catalog.smart.filter");
        \CIBlock::clearIblockTagCache($id);

        $output->writeln('Indexed: ' . $countIndexed);

        return $this::SUCCESS;
    }
}