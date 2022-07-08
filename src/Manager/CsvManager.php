<?php

namespace App\Manager;

class CsvManager
{
    //Open Csv file
    public function openCsv($file):String
    {
        return file_get_contents($file, 'r+');
    }

    //Detect the delimeters
    public function detectDelimiter(string $csv_string): string
    {
        // List of delimiters that we will check for
        $delimiters = array(';' => 0,',' => 0,"\t" => 0,"|" => 0);

        // For every delimiter, we count the number of time it can be found within the csvManager string
        foreach ($delimiters as $delimiter => &$count) {
            $count = substr_count($csv_string,$delimiter);
        }

        // The delimiter used is probably the one that has the more occurrence in the file
        return array_search(max($delimiters), $delimiters);
    }

    //Fix the line for Csv
    public function lineCsv($csv):array
    {
       return explode("\n", $csv);
    }

    //Determine the header Csv
    public function headerCsv($lines, $delimiter, $enclosure  ):array
    {
        return str_getcsv(array_shift($lines),$delimiter,$enclosure);
    }

    //Combine the Csv in array
    public function combineCsv($lines, $delimiter, $enclosure, $head):array
    {
        $array = array();

        foreach ($lines as $line) {

            // Sometimes CSV files have an empty line at the end, we try not to add it in the array
            if (empty($line)) {
                continue;
            }

            // Get the CSV dataManager of the line
            $csv = str_getcsv($line, $delimiter, $enclosure);

            // Combine the header and the lines dataManager
            $array[] = array_combine($head, $csv);
        }
        return $array;
    }}


