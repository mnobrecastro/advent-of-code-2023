<?php

function gen_line(string $filename) : Generator
{
    $f = fopen($filename, 'r');
    while(($line = fgetcsv($f)) !== false){
        yield $line;
    }
    fclose($f);
    return;
}

function get_number(string $str, bool $end) : int
{
    $number = array("zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine");
    for($i=0; $i < 10; $i++) {
        if($end) {
            if(str_ends_with($str, $number[$i])){
                return $i;
            }
        } else {
            if(str_starts_with($str, $number[$i])){
                return $i;
            }
        }
    }
    return -1;
}

function solve(string $filename, bool $is_spelled=false) : int
{
    $sum = 0;
    $generator = gen_line($filename);
    foreach($generator as $line) {
        $s = implode($line);
        $a = ''; $b = '';
        $lbuf = ''; $rbuf = '';
        $i = 0; $j = strlen($s) - 1;
        while($i <= $j){
            if($a == '') {
                $val = ord($s[$i]) - ord('0');
                if(0 <= $val && $val <= 9){
                    $a = $val;
                } else if($is_spelled){
                    $lbuf .= $s[$i];
                    $val = get_number($lbuf, true);
                    if ($val >= 0) {
                        $a = $val;
                    }else{
                        $i++;
                    }
                } else {
                    $i++;
                }
            }
            if($b == '') {
                $val = ord($s[$j]) - ord('0');
                if(0 <= $val && $val <= 9){
                    $b = $val;
                } else if($is_spelled) {
                    $rbuf = $s[$j] . $rbuf;
                    $val = get_number($rbuf, false);
                    if ($val >= 0) {
                        $b = $val;
                    } else {
                        $j--;
                    }
                } else {
                    $j--;
                }
            }
            if($a !== '' && $b !== '')
                break;
        }
        if($a == '') $a = $b;
        if($b == '') $b = $a;
        $sum += $a*10 + $b;
        echo "$s -> $a$b\n";
    }
    return $sum;
}



function main(): void
{
    /**** PART 1 ****/
    $filename = "sample.txt";
    $sum = solve($filename);
    if (142 != $sum)
        return;
    $filename= "input.txt";
    $sum = solve($filename);
    echo $sum, "\n";
    
    /**** PART 2 ****/
    $filename = "sample2.txt";
    $sum = solve($filename, $is_spelled=true);
    if (281 != $sum)
        return;
    $filename= "input.txt";
    $sum = solve($filename, $is_spelled=true);
    echo $sum;
    return;
}
main();

?>