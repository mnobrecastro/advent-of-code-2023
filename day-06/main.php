<?php

// function gen_line(string $filename) : generator
// {
//     $f = fopen($filename, 'r');
//     while(($line = fgets($f)) !== false){
//         yield str_replace(array("\n","\r"), '', $line);
//     }
//     fclose($f);
//     return;
// }

function find_numbers(string $str) : array
{
    preg_match_all("/[\d]+/", $str, $matches);
    return $matches[0];
}

function parse_line(string $str)
{
    $str_arr = explode(':', $str);
    $numbers = find_numbers($str_arr[1]);
    return $numbers;
}

function print_arr($arr)
{
    foreach($arr as $a){
        echo $a, " ";
    }
    echo "\n";
}

function parse_file($filename)
{
    $generator = gen_line($filename);
    // Retrieve header (first line)
    $time = parse_line($generator->current());
    $generator->next();
    $dist = parse_line($generator->current());
    $generator->next();
    return [$time, $dist];
}

function solve(string $filename) : int
{
    [$time, $dist] = parse_file($filename);
    //print_arr($time);
    //print_arr($dist);    
    $mul = 1;
    for($i = 0; $i < count($time); $i++) {
        $ways = 0;
        for($t = 1; $t < $time[$i]; $t++) {
            $v = $t;
            $d = $v * ($time[$i]-$t);
            if($d > $dist[$i])
                $ways++;
        }
        $mul *= $ways;
    }
    return $mul;
}


function main(): void
{
    /**** PART 1 ****/
    $res = solve("sample.txt");
    echo $res, "\n";
    if (288 !== $res)
        return;    
    $res = solve("input.txt");
    echo $res, "\n";
}
main();

?>