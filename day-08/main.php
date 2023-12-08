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

function find_letters(string $str) : array
{
    preg_match_all("/[0-9A-Z]+/", $str, $matches);
    return $matches[0];
}

function parse_line(string $str) : array
{
    $str_arr = find_letters($str);    
    return $str_arr;
}

function parse_file($filename) : array
{
    $generator = gen_line($filename);
    $instructions = $generator->current();
    $generator->next();
    $generator->next(); // Skip the empty line
    $adj = array();
    while ($generator->valid()) {
        $str_arr = parse_line($generator->current());
        //print_arr($str_arr);
        $adj[$str_arr[0]] = array($str_arr[1], $str_arr[2]);
        $generator->next();
    }
    return [$instructions, $adj];
}

function solve(string $filename) : int
{
    [$instructions, $adj] = parse_file($filename);
    //print_r($adj);
    $node = "AAA";
    $moves = 0;
    $i = 0;
    while($node !== "ZZZ") {
        if($instructions[$i] === "L")
            $node = $adj[$node][0];
        else
            $node = $adj[$node][1];
        $i++;
        $moves++;
        if ($i == strlen($instructions))
            $i = 0;
    }
    return $moves;
}

function gcd($a, $b)
{
    if ($b == 0)
        return $a;
    return gcd($b, $a % $b);
}

function least_common_multiple(array $arr) : int
{
    $ans = $arr[0];
    for ($i = 1; $i < count($arr); $i++)
        $ans = ((($arr[$i] * $ans)) / (gcd($arr[$i], $ans)));
    return $ans;
}

function solve2(string $filename) : int
{
    [$instructions, $adj] = parse_file($filename);
    $sources = array_keys($adj);
    for($s = count($sources)-1; $s >= 0; $s--) {
        if($sources[$s][2] != "A"){
            unset($sources[$s]);       
        }
    }
    $sources = array_values($sources);
    //print_r($sources);
    $dists = array_fill(0, count($sources), 0);
    for($s = 0; $s < count($sources); $s++) {
        $node = $sources[$s];
        $moves = 0;
        $i = 0;
        while($node[2] !== "Z") {
            if($instructions[$i] === "L")
                $node = $adj[$node][0];
            else
                $node = $adj[$node][1];
            $i++;
            $moves++;
            if ($i == strlen($instructions))
                $i = 0;
        }
        $dists[$s] = $moves;
    }
    $dists = array_unique($dists);
    return least_common_multiple($dists);
}

function main(): void
{   
    /**** PART 1 ****/       
    $res = solve("sample2.txt");
    echo $res;    
    if (6 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true); 
    $res = solve("input.txt");
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);

    /**** PART 2 ****/       
    $res = solve2("sample3.txt");
    echo $res;    
    if (6 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true); 
    $res = solve2("input.txt");
    $toc = microtime(true);
    printf("Answer 2: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);
}
main();

?>