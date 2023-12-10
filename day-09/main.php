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

function predict(array $num_arr) : int 
{
    $pyramid = array();
    array_push($pyramid, $num_arr);
    print_arr(end($pyramid));
    // Compute differences
    $flag = false;    
    while(!$flag) {
        $diff_arr = array_fill(0, count(end($pyramid))-1, 0);
        for($i = 0; $i < count($diff_arr); $i++) {
            $diff_arr[$i] = end($pyramid)[$i+1] - end($pyramid)[$i];
        }
        array_push($pyramid, $diff_arr);
        print_arr($diff_arr);
        $unique_arr = array_unique($diff_arr);
        if(count($unique_arr) == 1 && $unique_arr[0] == 0)
            $flag = true;
        // else if(count($diff_arr) == 1) {
        //     print_arr(array(0));
        //     array_push($pyramid, array(0));
        //     $flag = true;
        // }

    }
    // Compute prediction
    // if(count(end($pyramid)) != 1)
    //     array_push($pyramid[count($pyramid)-1], 0);
    print_arr(end($pyramid));
    for($i = count($pyramid) - 2; $i >= 0; $i--){
        array_push($pyramid[$i], end($pyramid[$i]) + end($pyramid[$i+1]));
        print_arr($pyramid[$i]);
    }
    echo "*\n";
    return end($pyramid[0]);
}

function solve(string $filename) : int
{
    $sum = 0;
    $generator = gen_line($filename);
    while ($generator->valid()) {
        $num_arr = parse_line($generator->current());
        $val = predict($num_arr);
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
}
main();

?>