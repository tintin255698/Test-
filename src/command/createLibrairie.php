<?php

// /src/command/createLibrairie.php

namespace App\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class createLibrairie extends Command
{
    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Imports the products CSV data file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output, $enclosure = '"'): int
    {
        // Let's get the content of the file and store it in the string
        $csv_string = file_get_contents('https://recrutement.dnd.fr/products.csv', 'r+');

        // Let's detect what is the delimiter of the CSV file
        $delimiter = $this->detect_delimiter($csv_string);

        // Get all the lines of the CSV string
        $lines = explode("\n", $csv_string);

        // The first line of the CSV file is the headers that we will use as the keys
        $head = str_getcsv(array_shift($lines),$delimiter,$enclosure);

        $array = array();

        // For all the lines within the CSV
        foreach ($lines as $line) {

            // Sometimes CSV files have an empty line at the end, we try not to add it in the array
            if(empty($line)) {
                continue;
            }

            // Get the CSV data of the line
            $csv = str_getcsv($line,$delimiter,$enclosure);

            // Combine the header and the lines data
            $array[] = array_combine( $head, $csv );

        }

    $json = json_encode($array);

    $parsed_json = json_decode($json);

        // Returning the array

         //Prix
        $price1 = $this->price($parsed_json[0]->{'price'});
        $price2 = $this->price($parsed_json[1]->{'price'});

        //Status
        $status1 = $this->status($parsed_json[0]->{'is_enabled'});
        $status2 = $this->status($parsed_json[1]->{'is_enabled'});

        //Slug
       $slug1 = strtolower($this->slug($parsed_json[0]->{'title'}));
       $slug2 = strtolower($this->slug($parsed_json[1]->{'title'}));

       //Description
        define('CR',"\n");
        $description1 = $this->description($parsed_json[0]->{'description'});
        $description2 = $this->description($parsed_json[1]->{'description'});


        //CreatedAt
        $createdAt1 = $this->createdAt($parsed_json[0]->{'created_at'});
        $createdAt2 = $this->createdAt($parsed_json[1]->{'created_at'});

        $table = new Table($output);
        $table
            ->setHeaders(['Sku', 'Status', 'Price', 'Description', 'Created At', 'Slug'])
            ->setRows([
                [ $parsed_json[0]->{'sku'}, $status1, $price1['0'].$array[0]['currency'],$description1[0].CR.$description1[1],$createdAt1, $slug1 ],
                [ $parsed_json[1]->{'sku'}, $status2, $price2['0'].$array[1]['currency'],$description2[0].CR.$description2[1],$createdAt2, $slug2 ],
            ])
        ;
        $table->render();


        //Command for CRON frequency between 7h to 19h all the day with cron-tab
        // */60 7-19 * * * php bin/console csv:import >/dev/null 2>&1


        return Command::SUCCESS;
    }

    /**
     *
     * This function detects the delimiter inside the CSV file.
     *
     * It allows the function to work with different types of delimiters, ";", "," "\t", or "|"
     *
     *
     *
     * @param string $csv_string    The content of the CSV file
     * @return string               The delimiter used in the CSV file
     */
   public function detect_delimiter(string $csv_string): string
    {

        // List of delimiters that we will check for
        $delimiters = array(';' => 0,',' => 0,"\t" => 0,"|" => 0);

        // For every delimiter, we count the number of time it can be found within the csv string
        foreach ($delimiters as $delimiter => &$count) {
            $count = substr_count($csv_string,$delimiter);
        }

        // The delimiter used is probably the one that has the more occurrence in the file
        return array_search(max($delimiters), $delimiters);

    }

    public function price(string $number): array
    {
        $price = number_format($number,2);
        return [str_replace('.', ',', $price  )];
    }

    public function status(string $number): string
    {
        if($number == 1){
           $status = 'Enable';
        } else {
            $status = 'Disable';
        }
        return $status;
    }

    public function slug($urlString){
        return preg_replace('/[^A-Za-z0-9-]+/', '-', $urlString);
    }

    public function description($description){
        $changeDesc = str_replace('\r', '<br/>', $description  );
        return explode("<br/>", $changeDesc);
    }

    public function createdAt($date){
        $timestamp = strtotime($date);
        return date("l, d-M-Y H:i:s T", $timestamp );
    }


}