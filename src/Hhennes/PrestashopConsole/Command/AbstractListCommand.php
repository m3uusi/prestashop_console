<?php

namespace Hhennes\PrestashopConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * Abstract class to group list command functionnalities
 */
abstract class AbstractListCommand extends Command
{
    const FORMAT_OPT_NAME = "format";
    
    protected function configure()
    {
        $this->addOption(
            AbstractListCommand::FORMAT_OPT_NAME, 
            null, 
            InputOption::VALUE_REQUIRED, 
            'The output format (txt, xml, json, or md)', 
            'txt'
        );
    }

    /**
     * Write a given set of datas to the output with a possible format
     * 
     * @param OutputInterface $output : The output in which the datas will be written
     * @param array $datas : The datas to write to the output
     * @param string $dataType : The type of data as a singular string. 
     *                           This will be used in the xml format for example to name the nodes.
     * @param string $format : The format in which the writing must be done (txt/json/xml)
     */
    protected function writeDatas(OutputInterface $output, array $datas, $dataType, $format = "txt")
    {
        if (0 === sizeof($datas)) {
            return;
        }

        switch ($format) {
            // Default text format
            case "txt":
                $table = new Table($output);
                $table->setHeaders(array_keys(reset($datas)));
                foreach ($datas as $data) {
                    $table->addRow(array_values($data));
                }
                $table->render();
                break;
            case "xml":
                $dom = new \DOMDocument('1.0', 'UTF-8');
                $dom->appendChild($datasNode = $dom->createElement($dataType . "s"));
                foreach ($datas as $data) {
                    $datasNode->appendChild($dataNode = $dom->createElement($dataType));
                    foreach (array_keys($data) as $dataAttributeName) {
                        $dataNode->setAttribute($dataAttributeName, $data[$dataAttributeName]);
                    }
                }
                $dom->formatOutput = true;
                $output->write($dom->saveXML());
                break;
            // Json format
            case "json":
                $output->writeln(\json_encode($datas));
                break;
            default:
                $output->writeln("<error>The given format is not supported.</error>");
                break;
        }
    }
}