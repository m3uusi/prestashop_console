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

namespace Hhennes\PrestashopConsole\Command\Admin\User;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Configuration;
use Db;

use Hhennes\PrestashopConsole\Command\AbstractListCommand;

class ListCommand extends AbstractListCommand
{
    protected function configure()
    {
        $this
            ->setName('admin:user:list')
            ->setDescription('List admin users')
            ->setHelp('List admin users registered in employee table');
        
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Function Employee::getEmployees() has not enough information , use db query instead
        $employeesQuery = "SELECT e.email,e.firstname,e.lastname,p.name,e.active,e.last_connection_date
                           FROM " . _DB_PREFIX_ . "employee e
                           LEFT JOIN " . _DB_PREFIX_ . "profile_lang p ON ( 
                           e.id_profile = p.id_profile AND p.id_lang=" . Configuration::get('PS_LANG_DEFAULT')
                            .")";

        $employees = Db::getInstance()->executeS($employeesQuery);
        if ($employees) {
            $this->writeDatas($output, $employees, "employee", $input->getOption(AbstractListCommand::FORMAT_OPT_NAME));
        } else {
            $output->writeln('<error>No admin user on this shop</error>');
            return 1;
        }
    }
}
