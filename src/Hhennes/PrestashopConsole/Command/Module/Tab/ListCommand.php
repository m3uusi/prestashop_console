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

namespace Hhennes\PrestashopConsole\Command\Module\Tab;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Module;
use Tab;

use Hhennes\PrestashopConsole\Command\AbstractListCommand;

/**
 * This command list module tabs
 */
class ListCommand extends AbstractListCommand
{
    protected function configure()
    {
        $this
            ->setName('module:tab:list')
            ->setDescription('list module admin tab')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'module name'
            );
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|string|void|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleName = $input->getArgument('name');
        if ($module = Module::getInstanceByName($moduleName)) {
            $tabs = Tab::getCollectionFromModule($moduleName);
            $results = $tabs->getResults();
            if (count($results)) {
                $datas = [];
                foreach ($results as $tab) {
                    /** @var Tab $tab */
                    $datas[] = [
                        'id' => $tab->id, 
                        'class' => $tab->class_name, 
                        'label' => $tab->name
                    ];
                }
                // Header only in text mode
                if ("txt" === $input->getOption(AbstractListCommand::FORMAT_OPT_NAME))
                    $output->writeln('<info>Module ' . $moduleName . 'admin tabs</info>');
                // Write results
                $this->writeDatas($output, $datas, "admin_tab", $input->getOption(AbstractListCommand::FORMAT_OPT_NAME));
            } else {
                $output->writeln('<info>Module ' . $moduleName . ' has no admin tabs');
            }
        } else {
            $output->writeln('<error>Error the module ' . $moduleName . ' doesn\'t exists</error>');
            return 1;
        }
    }
}
