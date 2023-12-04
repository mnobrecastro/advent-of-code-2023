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

function parse_line(string $str)
{
    $str_arr = explode(':', $str);
    $card = find_numbers($str_arr[0]);
    $str_arr = explode('|', $str_arr[1]);
    $target = find_numbers($str_arr[0]);
    $source = find_numbers($str_arr[1]);
    return [$card, $target, $source];
}

function solve(string $filename) : int
{
    $sum = 0;
    $generator = gen_line($filename);
    foreach($generator as $line) {
        $score = 0;
        [$card, $target, $source] = parse_line($line);
        echo "Card {$card[0]}: ";
        $matches = 0; 
        foreach($source as $number) {
            if(array_search($number, $target) !== false){
                $matches += 1;
                echo "$number ";
            }
        }
        if ($matches > 0)
            $score = pow(2, $matches-1);
        echo "({$score} points). \n";
        $sum += $score;
    }
    return $sum;
}

function find_matches($target, $source)
{
    $matches = 0;
    $numbers = array();
    sort($target);
    sort($source);
    $i = 0;
    $j = 0;
    while(true) {
        if ($target[$i] == $source[$j]) {
            array_push($numbers, $target[$i]);
            $matches += 1;
        }
        if (($i == count($target)-1 && $j == count($source)-1))
            break;
        if ($target[$i] <= $source[$j]){
            if ($i < count($target)-1)
                $i += 1;
            else if ($j < count($source)-1)
                $j += 1;
        } else {
            if ($j < count($source)-1)
                $j += 1;
            else if ($i < count($target)-1)
                $i += 1;
        }
    }
    return [$matches, $numbers];
}


function solve2(string $filename, int $max_winning) : int
{
    $sum = 0;
    $generator = gen_line($filename);
    $copies = array_fill(0, $max_winning+1, 1); // queue
    foreach($generator as $line) {        
        for($c = 0; $c < $copies[0]; $c++) {
            [$card, $target, $source] = parse_line($line);
            if ($c == 0)
                echo "Card {$card[0]}: ";
            [$matches, $numbers] = find_matches($target, $source);
            if ($c == 0) {
                foreach($numbers as $number)
                    echo "$number ";
            }
            if ($matches) {
                for($i=1; $i <= $matches; $i++){
                    $copies[$i] += 1;
                }
            }
        }
        echo "({$copies[0]} copies). \n";
        $sum += $copies[0];
        // Update de $copies queue
        array_shift($copies); // dequeue
        array_push($copies, 1); // enqueue
    }
    return $sum;
}


function main(): void
{
    /**** PART 1 ****/
    $res = solve("sample.txt");
    echo $res, "\n";
    if (13 !== $res)
        return;    
    $res = solve("input.txt");
    echo $res, "\n";
    
    /**** PART 2 ****/
    $res = solve2("sample.txt", $max_winning=5);
    echo $res, "\n";
    if (30 !== $res)
        return;
    $res = solve2("input.txt", $max_winning=10);
    echo $res, "\n";
}
main();

?>