
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

function print_arr($arr)
{
    foreach($arr as $a){
        echo $a, " ";
    }
    echo "\n";
}

function print_mat($mat)
{
    foreach($mat as $m){
        print_arr($m);
    }    
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
}
main();

?>