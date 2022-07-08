<?php

namespace App\command;

use App\Manager\csv;
use App\Manager\data;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class createLibrairie extends Command
{
    private csv $csv;

    private data $data;

    private array $array = [];

    public function __construct(csv $csv, data $data)
    {
        $this->csv = $csv;
        $this->data = $data;
        parent::__construct();
    }

    //Configuration
    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Imports the products CSV data file')
        ;
    }

    //Open, read and format CSV File in array
    private function csv(): array
    {
        if(empty($this->array)) {

            //Your file
            $file = 'https://recrutement.dnd.fr/products.csv';

            //Enclosure
            $enclosure = '"';

            // Let's get the content of the file and store it in the string
            $csv_string = $this->csv->openCsv($file);

            // Let's detect what is the delimiter of the CSV file
            $delimiter = $this->csv->detectDelimiter($csv_string);

            // Get all the lines of the CSV string
            $lines = $this->csv->lineCsv($csv_string);

            // The first line of the CSV file is the headers that we will use as the keys
            $head = $this->csv->headerCsv($lines, $delimiter, $enclosure);

            // Combine the header and the lines data
            $array = $this->csv->combineCsv($lines, $delimiter, $enclosure, $head);

            unset($array[0]);

            $this->array = $array;

        }

        return $this->array;
    }

    //Recupe and format enabled
    private function isEnable($method = 'getStatus', $search = 'is_enabled')
    {
        return $this->data->search($method, $search, $this->csv());
    }

    //Recupe and format the date
    private function date($method = 'getCreatedAt', $search = 'created_at')
    {
        return $this->data->search($method, $search, $this->csv());
    }

    //Recupe and format the description
    private function description($method = 'getDescription', $search = 'description')
    {
       return $this->data->search($method, $search, $this->csv());
    }

    //Recupe and format the slug
    private function slug($method = 'getSlug', $search = 'title')
    {
         return $this->data->search($method, $search, $this->csv());
    }

    //Recupe and format the price
    private function price($method = 'getPrice', $search = 'price')
    {
        return $this->data->search($method, $search, $this->csv());
    }

    //Execution
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $table
            ->setHeaders(['Sku', 'Status', 'Price', 'Description', 'Created At', 'Slug'])
            ->setRows([
                [ $this->csv()[1]['sku'], $this->isEnable()[0], $this->price()['0'].$this->csv()[1]['currency'],$this->description()[0],$this->date()[0], $this->slug()[0] ],
                [ $this->csv()[2]['sku'], $this->isEnable()[1], $this->price()['1'].$this->csv()[2]['currency'],$this->description()[1],$this->date()[1], $this->slug()[1] ],
            ])
        ;
        $table->render();

        return Command::SUCCESS;
    }

}