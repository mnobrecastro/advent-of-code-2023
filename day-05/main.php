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

function expand_seeds($arr)
{
    $new = array();
    for($i = 0; $i < count($arr); $i+=2){
        for($j = $arr[$i]; $j < $arr[$i] + $arr[$i+1]; $j++)
            array_push($new, $j);
    }
    return $new;
}

/*function solve2(string $filename) : int
{
    $generator = gen_line($filename);
    // Retrieve header (first line)
    [$flag, $seeds] = parse_line($generator->current(), $is_header=true);
    $seeds = expand_seeds($seeds);
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
}*/


function gen_expand_seeds($arr) : generator
{
    for($i = 0; $i < count($arr); $i+=2){
        for($j = $arr[$i]; $j < $arr[$i] + $arr[$i+1]; $j++)
            yield $j;
    }
}

function parse_file($filename)
{
    $generator = gen_line($filename);
    // Retrieve header (first line)
    [$flag, $seeds] = parse_line($generator->current(), $is_header=true);
    $generator->next();
    $mapping = array();
    while ($generator->valid()) {
        [$flag, $map] = parse_line($generator->current());
        if ($flag) {
            array_push($mapping, array());
        } else if ($map) {
            array_push($mapping[count($mapping)-1], $map);
        }   
        $generator->next();
    }
    return [$seeds, $mapping];
}

function echo_arr($arr)
{
    foreach($arr as $a){
        echo $a, " ";
    }
    echo "\n";
}

function solve2(string $filename) : int
{
    [$seeds, $mapping] = parse_file($filename);
    // foreach($mapping as $maps) {
    //     echo count($maps), "\n";
    // }
    $seeds = gen_expand_seeds($seeds);
    $min = -log(0); // INF
    $i = 0;
    foreach($seeds as $seed) {
        if ($i % 100000 == 0) {
            echo "Seed: {$seed}, Min: {$min}\n";
            $i = 0;
        }

        foreach($mapping as $maps) {
            foreach($maps as $map) {
                if($map[1] <= $seed && $seed <= $map[1] + $map[2] - 1){
                    $seed += $map[0] - $map[1];
                    break;
                }
            }
        }
        if($seed < $min)
            $min = $seed;

        $i++;
    }
    return $min;
}


function main(): void
{
    /**** PART 1 ****/
    // $res = solve("sample.txt");
    // echo $res, "\n";
    // if (35 !== $res)
    //     return;    
    // $res = solve("input.txt");
    // echo $res, "\n";

    /**** PART 2 ****/
    //ini_set('memory_limit', '32G'); //-1
    $res = solve2("sample.txt");
    echo $res, "\n";
    if (46 !== $res)
        return;
    $res = solve2("input.txt");
    echo $res, "\n";
}
main();

?>