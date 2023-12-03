<?php

function gen_line(string $filename) : Generator
{
    $f = fopen($filename, 'r');
    while(($line = fgets($f)) !== false){
        yield str_replace(array("\n","\r"), '', $line);
    }
    fclose($f);
    return;
}

function find_number(string $str){
    preg_match_all("/[\d]+/", $str, $matches, PREG_OFFSET_CAPTURE);
    return $matches;
}

function find_symbol(string $str) {
    preg_match_all("/[^0-9A-Za-z.]+/", $str, $matches, PREG_OFFSET_CAPTURE);
    return $matches;
}

function parse_line(string $str)
{
    $numbers = find_number($str);
    if($numbers) {$numbers = $numbers[0];}
    $symbols = find_symbol($str);
    if($symbols) {$symbols = $symbols[0];}
    return [$numbers, $symbols];
}

function is_match($number, $symbol)
{
    if($number[1]-1 <= $symbol[1] && $symbol[1] <= $number[1] + strlen($number[0])) {
        return true;
    }
    return false;
}

function solve(string $filename) : int
{
    $sum = 0;
    $generator = gen_line($filename);
    $numbers_prev = array();
    $symbols_prev = array();
    foreach($generator as $line) {
        [$numbers, $symbols] = parse_line($line);
        if ($symbols_prev) {
            // Associate $symbols_prev to $numbers
            if ($numbers) {                
                foreach($symbols_prev as $symbol) {
                    foreach($numbers as $number) {
                        if (is_match($number, $symbol)) {
                            echo $number[0], " > ", $symbol[0], "\n";
                            $sum += intval($number[0]);
                            unset($number);
                        }
                    }
                }
            }
        }
        if ($symbols) {
            // Associate $symbols to $numbers_prev (previous line)
            if ($numbers_prev) {
                foreach($symbols as $symbol) {        
                    foreach($numbers_prev as $number) {
                        if (is_match($number, $symbol)) {
                            echo $number[0], " > ", $symbol[0], "\n";
                            $sum += intval($number[0]);
                            unset($number);
                        }
                    }
                }
            }
            // Associate $symbols to $numbers (same line)
            if ($numbers) {             
                foreach($symbols as $symbol) {
                    foreach($numbers as $number) {
                        if (is_match($number, $symbol)) {
                            echo $number[0], " > ", $symbol[0], "\n";
                            $sum += intval($number[0]);
                            unset($number);
                        }
                    }
                }
            }            
        }
        $symbols_prev = $symbols;
        $numbers_prev = $numbers;
    }
    return $sum;
}

function solve2(string $filename) : int
{
    $sum = 0;
    $generator = gen_line($filename);
    $numbers_prev = array();
    $symbols_prev = array();
    $pairs = array();
    foreach($generator as $line) {
        [$numbers, $symbols] = parse_line($line);
        if ($symbols_prev) {
            // Associate $symbols_prev to $numbers
            if ($numbers) {                
                foreach($symbols_prev as $symbol) {
                    if($symbol[0] == "*") {
                        foreach($numbers as $number) {
                            if (is_match($number, $symbol)) {                            
                                if ( isset($pairs[$symbol[1]]) ) {
                                    // Confirm the pairing
                                    $sum += $pairs[$symbol[1]] * intval($number[0]);
                                    echo $pairs[$symbol[1]], " * ", intval($number[0]), "\n";
                                    unset($pairs[$symbol[1]]);
                                } else {
                                    $pairs[$symbol[1]] = intval($number[0]);
                                }
                                unset($number);
                            }
                        }
                        unset($pairs[$symbol[1]]);
                    }
                }
            }
        }
        if ($symbols) {
            // Associate $symbols to $numbers_prev (previous line)
            if ($numbers_prev) {
                foreach($symbols as $symbol) {        
                    foreach($numbers_prev as $number) {
                        if (is_match($number, $symbol) && $symbol[0] == "*") {
                            if ( isset($pairs[$symbol[1]]) ) {
                                // Confirm the pairing
                                $sum += $pairs[$symbol[1]] * intval($number[0]);
                                echo $pairs[$symbol[1]], " * ", intval($number[0]), "\n";
                                unset($pairs[$symbol[1]]);
                            } else {
                                $pairs[$symbol[1]] = intval($number[0]);
                            }
                            unset($number);
                        }
                    }
                }
            }
            // Associate $symbols to $numbers (same line)
            if ($numbers) {             
                foreach($symbols as $symbol) {
                    foreach($numbers as $number) {
                        if (is_match($number, $symbol) && $symbol[0] == "*") {
                            if ( isset($pairs[$symbol[1]]) ) {
                                // Confirm the pairing
                                $sum += $pairs[$symbol[1]] * intval($number[0]);
                                echo $pairs[$symbol[1]], " * ", intval($number[0]), "\n";
                                unset($pairs[$symbol[1]]);
                            } else {
                                $pairs[$symbol[1]] = intval($number[0]);
                            }
                            unset($number);
                        }
                    }
                }
            }            
        }
        $symbols_prev = $symbols;
        $numbers_prev = $numbers;
    }
    return $sum;
}

function main(): void
{
    /* "ASSERTS" */
    [$numbers, $symbols] = parse_line("467@.114..");
    if (!is_match($numbers[0], $symbols[0]))
        return;
    [$numbers_new, $symbols_new] = parse_line("...*......");
    if (!is_match($numbers[0], $symbols_new[0]))
        return;

    /**** PART 1 ****/
    $res = solve("sample.txt");
    echo $res, "\n";
    if (4361 !== $res)
        return;    
    $res = solve("input.txt");
    echo $res, "\n";
    
    // /**** PART 2 ****/
    $res = solve2("sample.txt");
    echo $res, "\n";
    if (467835 !== $res)
        return;    
    $res = solve2("input.txt");
    echo $res, "\n";

}
main();

?>