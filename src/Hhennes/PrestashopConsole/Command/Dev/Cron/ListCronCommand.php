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
 * @copyright 2007-2019 Hennes Hervé
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * http://www.h-hennes.fr/blog/
 */

namespace Hhennes\PrestashopConsole\Command\Dev\Cron;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Module;

use Hhennes\PrestashopConsole\Command\AbstractListCommand;

class ListCronCommand extends AbstractListCommand
{

    /** @var string cron Module Name */
    protected $_cronModuleName = 'cronjobs';

    protected function configure()
    {
        $this
                ->setName('dev:cron:list')
                ->setDescription('List cron tasks configured with the module cronjobs');
        
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module = Module::getInstanceByName($this->_cronModuleName);
        // Check that module is found
        if (!$module) {
            $output->writeln('<error>' . $this->_cronModuleName . ' is not installed</error>');
            return 1;
        }
        // Check that module is installed and active
        if (!Module::isInstalled($module->name) || !$module->active) {
            $output->writeln('<error>' . $this->_cronModuleName . ' is not active or installed');
            return 1;
        }

        \CronJobsForms::init($module);
        $cronJobs = \CronJobsForms::getTasksListValues();

        foreach ($cronJobs as $cronJob) {
            // Keep only the datas we want to display
            $data = [];
            foreach (['id_cronjob','description', 'task', 'hour', 'day', 'month', 'week_day', 'last_execution', 'active'] as $key) {
                $data[$key] = $cronJob[$key];
            }
            $datas[] = $data;
        }

        if (sizeof($datas)) {
            $this->writeDatas($output, $datas, 'cron_job', $input->getOption(AbstractListCommand::FORMAT_OPT_NAME));
        } else {
            $output->writeln('<info>No cron_job found on this project');
        }
    }
}
