
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
        array_push($mat, str_split($line, 1));
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

function move_rocks(array &$mat) : int
{
    $load = 0;
    for($i = 0; $i < count($mat); $i++) {
        for($j = 0; $j < count($mat[0]); $j++) {
            if($mat[$i][$j] == "O"){
                $cur_load = count($mat) - $i;
                //print($i.",".$j." ".$cur_load."\n");
                $k = $i;
                while($k > 0) {
                    if($mat[$k-1][$j] == "."){
                        $mat[$k-1][$j] = "O";            
                        $cur_load = count($mat) - $k+1;
                        $mat[$k][$j] = ".";
                        //print($i.",".$j." > ".$cur_load."\n");
                    }
                    else
                        break;                    
                    $k--;
                }
                //print($i.",".$j." ".$cur_load."\n");
                //print_mat($mat);
                $load += $cur_load;                
            }
        }
    }
    return $load;
}

function solve(string $filename) : int
{
    $sum = 0;
    $mat = parse_file($filename);
    print_mat($mat);
    echo "\n";
    $sum = move_rocks($mat);
    print_mat($mat);    
    return $sum;
}


function main(): void
{   
    /**** PART 1 ****/       
    $res = solve("sample.txt");
    echo $res;    
    if (136 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true);
    $res = solve("input.txt");
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);
}
main();

?>