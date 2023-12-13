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
    $submat = array();
    $generator = gen_line($filename);    
    while ($generator->valid()) {
        $line = $generator->current();
        // Check for empty line
        if(strlen($line) == 0){
            array_push($mat, $submat);
            $submat = array();
            $generator->next();
            continue;
        }
        array_push($submat, str_split($line, 1));
        $generator->next();
    }
    array_push($mat, $submat);
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

function compare(array $arr1, array $arr2) : bool
{    
    if(count($arr1) == count($arr2)){        
        foreach(array_map(null, $arr1, $arr2) as [$a, $b]){
            if ($a != $b)
                return false;
        }
        return true;
    } else {
        return false;
    }
}

function solve(string $filename) : int
{
    $sum = 0;
    $mat = parse_file($filename);
    foreach($mat as $submat) {
        print_mat($submat);
        // Vertical
        $ncols_max = 0;
        $cols_left = 0;
        for($j = 0; $j < count($submat[0])-1; $j++){
            $ncols = 0;
            for($k = 0; $k < min($j+1, count($submat[0]) - ($j+1)); $k++){
                echo $j-$k, " ", $j + 1 + $k;
                if(compare(array_column($submat, $j - $k), array_column($submat, $j + 1 + $k))) {
                    $ncols++;
                    echo " *\n";
                } else {
                    echo "\n";
                    break;
                }
            }        
            if(!($j+1 - $ncols == 0 || $j + $ncols == count($submat[0])-1))
                $ncols = 0;
            if ($ncols_max < $ncols){
                $ncols_max = $ncols;
                $cols_left = $j+1;
            }  
        }
        echo "submat_cols: ", $cols_left." > ".$ncols_max, "\n";
        // Horizontal
        $nrows_max = 0;
        $rows_above = 0;
        for($j = 0; $j < count($submat)-1; $j++){
            $nrows = 0;
            for($k = 0; $k < min($j+1, count($submat) - ($j+1)); $k++){
                echo $j-$k, " ", $j + 1 + $k;
                if(compare($submat[$j-$k], $submat[$j+1+$k])) {
                    $nrows++;
                    echo " *\n";
                } else {
                    echo "\n";
                    break;
                }
            }
            if(!($j+1 - $nrows == 0 || $j + $nrows == count($submat)-1))
                $nrows = 0;
            if ($nrows_max < $nrows) {
                $nrows_max = $nrows;
                $rows_above = $j+1;
            }
        }
        echo "submat_rows: ", $rows_above." > ".$nrows_max, "\n";

        if($ncols_max >= $nrows_max)
            $sum += $cols_left;
        else
            $sum += 100 * $rows_above;
    }
    return $sum;
}


function main(): void
{   
    /**** PART 1 ****/       
    $res = solve("sample.txt");
    echo $res;    
    if (405 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true); 
    $res = solve("input.txt");
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);
}
main();

?>