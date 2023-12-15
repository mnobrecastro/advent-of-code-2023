
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

function parse_file($filename) : array
{   
    $mat = array();
    $generator = gen_line($filename);
    while ($generator->valid()) {
        $line = $generator->current();
        array_push($mat, explode(",", $line));
        $generator->next();
    }
    return $mat;
}

function apply_hash(string $str) : int
{
    $val = 0;
    foreach(str_split($str) as $chr){
        $val += ord($chr);
        $val *= 17;
        $val %= 256;
    }
    return $val;
}

function solve(string $filename) : int
{
    $sum = 0;
    $mat = parse_file($filename);
    foreach($mat[0] as $str)
        $sum += apply_hash($str);
    return $sum;
}

function solve2(string $filename) : int
{
    $boxes = array_fill(0, 256, array());
    $mat = parse_file($filename);
    // Add the lenses
    foreach($mat[0] as $str) {
        if($pos = strpos($str, "=") !== false){
            [$label, $focal] = explode("=", $str);            
            //echo $label." ".$focal."\n";
            $boxes[apply_hash($label)][$label] = intval($focal);            
        } else {
            $label = substr($str, 0, -1);
            //echo $label."-\n";
            if(in_array($label, array_keys($boxes[apply_hash($label)]))){
                unset($boxes[apply_hash($label)][$label]);
            }
        }
    }
    // Compute focusing power
    $sum = 0;
    for($i = 0; $i < count($boxes); $i++) {
        if(count($boxes[$i]) >= 0) {
            $j = 0;
            foreach($boxes[$i] as $lens){
                $sum += ($i + 1) * ($j+1) * $lens;;
                $j++;
            }
        }
    }
    return $sum;
}


function main(): void
{   
    $str = "HASH";
    echo $str.": ".apply_hash($str).".\n";

    /**** PART 1 ****/       
    $res = solve("sample.txt");
    echo $res;    
    if (1320 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true);
    $res = solve("input.txt");
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);

    /**** PART 2 ****/       
    $res = solve2("sample.txt");
    echo $res;
    if (145 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true);
    $res = solve2("input.txt");
    $toc = microtime(true);
    printf("Answer 2: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);
}
main();

?>