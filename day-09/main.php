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

function print_arr($arr)
{
    foreach($arr as $a){
        echo $a, " ";
    }
    echo "\n";
}

function find_numbers(string $str) : array
{
    preg_match_all("/-?[\d]+/", $str, $matches);
    return $matches[0];
}

function parse_line(string $str) : array
{
    $num_arr = find_numbers($str);
    foreach($num_arr as $number)
        $number = intval($number);
    return $num_arr;
}

function predict(array $num_arr, bool $first) : int 
{
    $pyramid = array();
    array_push($pyramid, $num_arr);
    // Compute differences
    $flag = false;
    while(!$flag) {
        $diff_arr = array_fill(0, count(end($pyramid))-1, 0);
        for($i = 0; $i < count($diff_arr); $i++) {
            $diff_arr[$i] = end($pyramid)[$i+1] - end($pyramid)[$i];
        }
        array_push($pyramid, $diff_arr);
        $unique_arr = array_unique($diff_arr);
        if(count($unique_arr) == 1 && $unique_arr[0] == 0)
            $flag = true;
    }
    // Compute prediction
    for($i = count($pyramid) - 2; $i >= 0; $i--){
        if(!$first)
            array_push($pyramid[$i], end($pyramid[$i]) + end($pyramid[$i+1]));
        else
            array_unshift($pyramid[$i], $pyramid[$i][0] - $pyramid[$i+1][0]);          
    }
    if(!$first)
        return end($pyramid[0]);
    else
        return $pyramid[0][0];
}

function solve(string $filename, bool $first=false) : int
{
    $sum = 0;
    $generator = gen_line($filename);
    while ($generator->valid()) {
        $num_arr = parse_line($generator->current());
        $val = predict($num_arr, $first);
        $sum += $val;
        $generator->next();
    }
    return $sum;
}


function main(): void
{   
    /**** PART 1 ****/       
    $res = solve("sample.txt");
    echo $res;    
    if (114 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true); 
    $res = solve("input.txt");
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);

    /**** PART 2 ****/       
    $res = solve("sample.txt", $first=true);
    echo $res;    
    if (2 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true); 
    $res = solve("input.txt", $first=true);
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);
}
main();

?>