<?php

function gen_line(string $filename) : Generator {
    $f = fopen($filename, 'r');
    while(($line = fgets($f)) !== false){
        yield str_replace(array("\n","\r"), '', $line);
    }
    fclose($f);
    return;
}

function parse_line($str) {
    $cubes = array("red" => 0, "green" => 0, "blue" => 0);
    $str_arr = explode(':', $str);
    $game = intval(explode(' ', $str_arr[0])[1]);
    //echo "'$str'", "\n";
    $set_arr = explode(';', $str_arr[1]);
    foreach($set_arr as $set) {
        echo "'$set' > ";
        $sub_arr = explode(',', $set);
        if (!$sub_arr)
            $sub_arr = array($set);
        foreach($sub_arr as $subset) {
            $subset = substr($subset, 1); // Drop the leading space
            $sub_arr = explode(' ', $subset);
            $cubes[$sub_arr[1]] = max($cubes[$sub_arr[1]], intval($sub_arr[0]));
        }
        echo $cubes["red"] ." ". $cubes["green"] ." ". $cubes["blue"], "\n";
    }
    return [$game, $cubes];
}

function solve($filename, array $target) : int {
    $sum = 0;
    $generator = gen_line($filename);
    foreach($generator as $line) {
        [$game, $cubes] = parse_line($line);
        echo "Game $game: " . $cubes["red"] ." ". $cubes["green"] ." ". $cubes["blue"], "\n";
        $flag = false;
        foreach(["red", "green", "blue"] as $color) {
            if($target[$color] < $cubes[$color]) {
                $flag = true;
                break;
            }
        }
        if(!$flag)
            $sum += $game;
    }
    return $sum;
}


function main(): void
{
    $filename = "sample.txt";
    $bag = array("red" => 12, "green" => 13, "blue" => 14);
    $sum = solve($filename, $bag);
    echo $sum;
    if (8 !== $sum)
        return;
    $filename= "input.txt";
    $sum = solve($filename, $bag);
    echo $sum;
    return;
}
main();

?>