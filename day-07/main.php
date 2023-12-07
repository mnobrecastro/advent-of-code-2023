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

function parse_line(string $str) : array
{
    $str_arr = explode(' ', $str);
    $cards = $str_arr[0];
    $bet = $str_arr[1];
    return [$cards, $bet];
}

function rank($a, $b)
{
    $ca = $a[0];
    $cb = $b[0];
    // Sort cards by number of occurences
    $ar = array_count_values($ca); 
    arsort($ar);
    $br = array_count_values($cb); 
    arsort($br);

    // Check for different type of hand
    if(count($ar) != count($br))
        return -(count($ar) - count($br));
    // Check for similar type of hand
    for ($i = 0; $i < count($ar); $i++) {
        $ka = $ar[array_keys($ar)[$i]];
        $kb = $br[array_keys($br)[$i]];
        if($ka != $kb)
            return $ka - $kb;
    }
    // Check for first card
    $camel = array("2", "3", "4", "5", "6", "7", "8", "9", "T", "J", "Q", "K", "A");
    for ($i = 0; $i < count($ca); $i++){
        $va = array_search($ca[$i], $camel);
        $vb = array_search($cb[$i], $camel);
        if($va != $vb)
            return $va - $vb;
    }
    return 0;
}

function solve(string $filename) : int
{
    $generator = gen_line($filename);
    $hands = array();
    foreach($generator as $line) {
        [$cards, $bet] = parse_line($line);
        $cards = str_split($cards,1);
        $bet = intval($bet);
        $hands[] = array($cards, $bet);
    }
    usort($hands, "rank");

    $sum = 0;
    for($i = 0; $i < count($hands); $i++){
        $sum += $hands[$i][1] * ($i+1);
        //print_arr($hands[$i][0]);
    }

    return $sum;
}

function main(): void
{   
    /**** PART 1 ****/       
    $res = solve("sample.txt");
    echo "$res\n";
    if (6440 !== $res)
        return;
    $tic = microtime(true); 
    $res = solve("input.txt");
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f s.\n", $res, $toc-$tic);
    //echo "Answer1: $res in $t s.\n";
}
main();

?>