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

function apply_joker(array &$arr) : void{
    if (($idx = array_search("J", array_keys($arr))) !== false){
        //echo "a $idx\n";
        if(count($arr) > 1) {
            if($idx == 0 && count($arr) > 1) {
                $arr[array_keys($arr)[1]] += $arr["J"];            
            } else {
                $arr[array_keys($arr)[0]] += $arr["J"];
            }          
            unset($arr["J"]);
        }        
    }
}

function rank2($a, $b)
{
    $ca = $a[0];
    $cb = $b[0];
    // Sort cards by number of occurences
    $ar = array_count_values($ca);
    arsort($ar);
    $br = array_count_values($cb);    
    arsort($br);
    
    // Handle Joker "J" as wildcard
    $aj = $ar;
    apply_joker($aj);
    $bj = $br;
    apply_joker($bj);

    // Check for different type of hand
    if(count($aj) != count($bj))
        return -(count($aj) - count($bj));

    // Check for similar type of hand
    for ($i = 0; $i < count($aj); $i++) {
        $ka = $aj[array_keys($aj)[$i]];
        $kb = $bj[array_keys($bj)[$i]];
        if($ka != $kb)
            return $ka - $kb;
    }

    // Check for first card
    $camel = array("J", "2", "3", "4", "5", "6", "7", "8", "9", "T", "Q", "K", "A");
    for ($i = 0; $i < count($ca); $i++){
        $va = array_search($ca[$i], $camel);
        $vb = array_search($cb[$i], $camel);
        if($va != $vb)
            return $va - $vb;
    }
    return 0;
}

function solve(string $filename, string $rank_fn) : int
{
    $generator = gen_line($filename);
    $hands = array();
    foreach($generator as $line) {
        [$cards, $bet] = parse_line($line);
        $cards = str_split($cards,1);
        $bet = intval($bet);
        $hands[] = array($cards, $bet);
    }
    usort($hands, $rank_fn);

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
    $res = solve("sample.txt", "rank");
    echo $res;    
    if (6440 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true); 
    $res = solve("input.txt", "rank");
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);

    /**** PART 2 ****/       
    $res = solve("sample.txt", "rank2");
    echo $res;
    if (5905 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true); 
    $res = solve("input.txt", "rank2");
    $toc = microtime(true);
    printf("Answer 2: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);

}
main();

?>