<?php

namespace App\Helper;

use App\Entity\Costanti;
use App\Entity\Fatture;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\AsciiSlugger;
use function Sodium\add;


trait Helper{

    // ####### CONVERTE LA DATA USATA NEL FRONT CON QUELLA USATA NEL DATABASE
    // ####### ESEMPIO:  01/10/2018  ---->  2018-10-01
    function frontDateToDatabaseDate($date) {
        return date( "Y-m-d H:i:s", strtotime(str_replace('/', '-', $date)));
    }

    function randomPassword($len = 12) {
        //enforce min length 8
        if($len < 12)
            $len = 12;

        //define character libraries - remove ambiguous characters like iIl|1 0oO
        $sets = array();
        $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        $sets[] = '123456789';
        //$sets[]  = '~!@#$%^&*(){}[],./?';

        $password = '';

        //append a character from each set - gets first 4 characters
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
        }

        //use all characters to fill up to $len
        while(strlen($password) < $len) {
            //get a random set
            $randomSet = $sets[array_rand($sets)];

            //add a random char from the random set
            $password .= $randomSet[array_rand(str_split($randomSet))];
        }

        //shuffle the password string before returning!
        return str_shuffle($password);
    }


}
