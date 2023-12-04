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


function main(): void
{
    /**** PART 1 ****/
    $res = solve("sample.txt");
    echo $res, "\n";
    if (13 !== $res)
        return;    
    $res = solve("input.txt");
    echo $res, "\n";
}
main();

?>