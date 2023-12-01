<?php
$sum = 0;
$f = fopen("input.txt", 'r');
while(($line = fgetcsv($f)) !== false){
    $s = implode($line);
    $a = ''; $b = '';
    $i = 0; $j = strlen($s)-1;
    while($i <= $j){
        if($a == ''){
            $val = ord($s[$i]) - ord('0');
            if(0 <= $val && $val <= 9){
                $a = $val;
            } else {
                $i++;
            }
        }
        if($b == '') {
            $val = ord($s[$j]) - ord('0');
            if(0 <= $val && $val <= 9){
                $b = $val;
            }else{
                $j--;
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