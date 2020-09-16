<?php

/**
 * 2007-2019 Hennes Hervé
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@h-hennes.fr so we can send you a copy immediately.
 *
 * @author    Hennes Hervé <contact@h-hennes.fr>
 * @copyright 2007-2020 Hennes Hervé
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * http://www.h-hennes.fr/blog/
 */


namespace Hhennes\PrestashopConsole\Command\Hook;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Hook;

use Hhennes\PrestashopConsole\Command\AbstractListCommand;

/**
 * Class Module
 * List hook with registered modules
 */
class ListCommand extends AbstractListCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('hook:list')
            ->setDescription('List all hooks registered in database');
        parent::configure();
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        //Get Hooks list
        $hooks = Hook::getHooks();

        if (sizeof($hooks)) {
            //Extract only hooks name
            $hooks = array_map(function ($row) {
                return ['hook_name' => $row['name']];
            }, $hooks);
            //Sort hooks by name
            usort($hooks, array($this, "cmp"));
            // Write the datas
            $this->writeDatas($output, $hooks, "hook", $input->getOption(AbstractListCommand::FORMAT_OPT_NAME));
        } else {
            $output->writeln('<info>No hook found on this project');
        }
    }

    /**
     * Function to sort hook by name
     * @param $a
     * @param $b
     * @return int|\lt
     */
    private function cmp($a, $b)
    {
        return strcmp($a['hook_name'], $b['hook_name']);
    }
}
