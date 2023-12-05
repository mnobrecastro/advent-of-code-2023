<?php

function gen_line(string $filename) : generator
{
    $f = fopen($filename, 'r');
    while(($line = fgets($f)) !== false){
        yield str_replace(array("\n","\r"), '', $line);
    }
    fclose($f);
    return;
}

function find_numbers(string $str) : array
{
    preg_match_all("/[\d]+/", $str, $matches);
    return $matches[0];
}

function parse_line(string $str, bool $is_header=false)
{
    $numbers = array();
    $flag = false;
    if($is_header){
        $str_arr = explode(':', $str);
        $numbers = find_numbers($str_arr[1]);
    }
    else{
        if(str_ends_with($str, ':'))
            $flag = true;
        else {
            $numbers = find_numbers($str);
        }
    }
    return [$flag, $numbers];
}

function print_arr($arr)
{
    foreach($arr as $a){
        echo $a, " ";
    }
    echo "\n";
}

function solve(string $filename) : int
{
    $generator = gen_line($filename);
    // Retrieve header (first line)
    [$flag, $seeds] = parse_line($generator->current(), $is_header=true);
    print_arr($seeds);
    $generator->next();
    //Read the rest of the file
    while ($generator->valid()) {
        [$flag, $map] = parse_line($generator->current());        
        if($flag) {
            $change = array_fill(0, count($seeds), true);
        }
        if ($map){          
            for($j = 0; $j < count($seeds); $j++) {
                if($change[$j] && $map[1] <= $seeds[$j] && $seeds[$j] <= $map[1] + $map[2] - 1){
                    $seeds[$j] += $map[0] - $map[1];
                    $change[$j] = false;
                }
            }
            print_arr($seeds);
        }
        $generator->next();
    }
    return min($seeds);
}


function main(): void
{
    /**** PART 1 ****/
    $res = solve("sample.txt");
    echo $res, "\n";
    if (35 !== $res)
        return;    
    $res = solve("input.txt");
    echo $res, "\n";
}
main();

?>