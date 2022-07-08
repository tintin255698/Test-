<?php

namespace App\Manager;

use http\Env\Response;

class data
{
//Price
    public function getPrice(string $number): string
    {
        $price = number_format($number,2);
        return str_replace('.', ',', $price  );
    }

    //Status
    public function getStatus(string $number): string
    {
        if($number == 1){
            $status = 'Enable';
        } else {
            $status = 'Disable';
        }
        return $status;
    }

    //Slug
    public function getSlug($urlString): string
    {
        return preg_replace('/[^A-Za-z0-9-]+/', '-', $urlString);
    }

    //Description HTML
    public function getDescription($description): string
    {
        $changeDesc = str_replace('r/', PHP_EOL, $description  );
        $changeDesc2 = str_replace('<br/>', PHP_EOL, $description  );

        return nl2br($changeDesc2, $changeDesc);
    }

    //Date
    public function getCreatedAt($date): string
    {
        $timestamp = strtotime($date);
        return date("l, d-M-Y H:i:s T", $timestamp );
    }

    public function search($method, $search, $array)
    {
        $ar = [];
        foreach ($array as $value => $item) {
            $data = $this->$method($array[$value][$search]);
            $ar[] = $data;
        }
        return $ar;
    }



}