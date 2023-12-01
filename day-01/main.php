<?php
function getNumber($str, $end)
{
    $number = array("zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine");
    for($i=0; $i < 10; $i++) {
        if($end) {
            if(str_ends_with($str, $number[$i])){
                return $i;
            }
        }else {
            if(str_starts_with($str, $number[$i])){
                return $i;
            }
        }
    }
    return -1;
}

$sum = 0;
$f = fopen("input.txt", 'r');
while(($line = fgetcsv($f)) !== false){
    $s = implode($line);
    $a = ''; $b = '';
    $lbuf = ''; $rbuf = '';
    $i = 0; $j = strlen($s)-1;
    while($i <= $j){
        if($a == ''){
            $val = ord($s[$i]) - ord('0');
            if(0 <= $val && $val <= 9){
                $a = $val;
            } else {
                $lbuf .= $s[$i];
                $val = getNumber($lbuf, true);
                if ($val >= 0) {
                    $a = $val;
                } else {
                    $i++;
                }
            }
        }
        if($b == '') {
            $val = ord($s[$j]) - ord('0');
            if(0 <= $val && $val <= 9){
                $b = $val;
            } else {
                $rbuf = $s[$j] . $rbuf;
                $val = getNumber($rbuf, false);
                if ($val >= 0) {
                    $b = $val;
                } else {
                    $j--;
                }
            }
        }
        if($a !== '' && $b !== '') break;
    }
    if($a == '') $a = $b;
    if($b == '') $b = $a;
    $sum += $a*10 + $b;
    echo "$s -> $a$b\n";
}
echo $sum;
fclose($f);
?>