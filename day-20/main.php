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

class Queue
{    
    public function __construct()
    {
        $this->_q = array();
    }

    public function from_file(string $filename) : void
    {
        [$this->_broadcaster, $this->_flipflop, $this->_conjunction] = $this->_parse_file($filename);
        return;
    }

    public function run(string $outmod="", $outstate=false) : array
    {
        // Reset queue
        $this->_q = array();
        $flag = false;

        // Push button
        $pulse = false;
        array_push($this->_q, $pulse);
        //echo "button"." -> ".+$pulse." -> "."broadcaster"."\n";
        $subq = array();
        foreach($this->_broadcaster as $module){
            array_push($this->_q, $pulse);
            //echo "broadcaster"." -> ".+$pulse." -> ".$module."\n";
            if(in_array($module, array_keys($this->_flipflop))){
                // Flip-flop module                
                if(!$pulse){
                    if($this->_flipflop[$module][0]){
                        $this->_flipflop[$module][0] = false;                        
                    } else {
                        $this->_flipflop[$module][0] = true;
                    }
                    array_push($subq, $module);                    
                }                
            }
            
            // Check for output module (outmod) and state (outstate)
            if(strlen($outmod) > 0){                            
                if($module == $outmod && $pulse == $outstate){
                    $flag = true;
                }
            }
        }

        $subq2 = array();
        while(true) {
            while(count($subq) > 0) {
                $module = array_shift($subq);
                $pulse = NULL;
                if(in_array($module, array_keys($this->_flipflop))){
                    // Flip-flop module                                
                    if($this->_flipflop[$module][0]){
                        $pulse = true;
                    } else {
                        $pulse = false;
                    }
                    if(count($this->_flipflop[$module][1]) > 0) {
                        foreach($this->_flipflop[$module][1] as $target){
                            array_push($this->_q, $pulse);
                            //echo $module." -> ".+$pulse." -> ".$target."\n";                            
                            if(in_array($target, array_keys($this->_flipflop))) {
                                // Flip-flop module
                                if(!$pulse){                            
                                    if($this->_flipflop[$target][0]){
                                        $this->_flipflop[$target][0] = false;                                        
                                    } else {
                                        $this->_flipflop[$target][0] = true;
                                    }
                                    array_push($subq2, $target);
                                }                                
                            } else {
                                // Conjunction module
                                if(in_array($module, array_keys($this->_conjunction[$target]["inputs"]))){
                                    $this->_conjunction[$target]["inputs"][$module] = $pulse;
                                    array_push($subq2, $target);
                                }
                            }

                            // Check for output module (outmod) and state (outstate)
                            if(strlen($outmod) > 0) {                            
                                if($target == $outmod && $pulse == $outstate){
                                    $flag = true;
                                }
                            }
                        }
                    }
                } else {
                    // Conjunction module
                    $output = true;
                    foreach($this->_conjunction[$module]["inputs"] as $in => $rem){
                        //echo $in." ~ ".+$rem."\n";
                        if(!$rem){
                            $output = false;
                            break;
                        }                        
                    }
                    if($output){
                        $pulse = false;
                    } else {
                        $pulse = true;
                    }
                    foreach($this->_conjunction[$module]["outputs"] as $out){
                        array_push($this->_q, $pulse);
                        //echo $module." -> ".+$pulse." -> ".$out."\n";
                        
                        if(in_array($out, array_keys($this->_flipflop))){
                            // Flip-flop module          
                            if(!$pulse){
                                if($this->_flipflop[$out][0]){
                                    $this->_flipflop[$out][0] = false;
                                    
                                } else {
                                    $this->_flipflop[$out][0] = true;
                                }
                                array_push($subq2, $out);
                            }                            
                        } else {
                            // Conjunction module                            
                            if(in_array($out, array_keys($this->_conjunction))) {
                                $this->_conjunction[$out]["inputs"][$module] = $pulse;
                                array_push($subq2, $out);
                            }
                        }

                        // Check for output module (outmod) and state (outstate)
                        if(strlen($outmod) > 0) {                            
                            if($out == $outmod && $pulse == $outstate){
                                $flag = true;
                            }
                        }
                    }
                }
            }
            
            if(count($subq2) == 0){
                break;
            } else {
                $subq = $subq2;
                $subq2 = array();
            }
        }

        $q_out = array();
        foreach($this->_q as $e){
            array_push($q_out, (int)$e);
        }

        $counts = array_count_values($q_out);
        if(strlen($outmod) == 0){
            return [$counts[1], $counts[0]];
        }else {            
            return [$counts[1], $counts[0], $flag];
        }        
    }

    protected function _parse_file(string $filename) : array
    {   
        $roadcaster = array();
        $flipflop = array();
        $conjunction = array();
    
        $generator = gen_line($filename);    
        while ($generator->valid()) {
            $line = $generator->current();
            [$source, $targets] = explode(" -> ", $line);
            switch($source[0]){
                case "b":
                    $broadcaster = explode(", ", $targets);
                    break;
                case "%":
                    $flipflop[substr($source, 1)] = array(false, explode(", ", $targets));
                    break;
                case "&":
                    $outputs = explode(", ", $targets);
                    $conjunction[substr($source, 1)]["outputs"] = $outputs;
                    break;
            }
            $generator->next();
        }
        // Set the conjunction inputs
        foreach(array_keys($flipflop) as $source){
            $targets = $flipflop[$source][1];
            foreach($targets as $target) {
                if(in_array($target, array_keys($conjunction))){
                    $conjunction[$target]["inputs"][$source] = false;
                } else if(!in_array($target, array_keys($flipflop))) {
                    // Output module
                    $flipflop[$target] = array(false, array());
                }
            }
        }
        foreach(array_keys($conjunction) as $source){
            $targets = $conjunction[$source]["outputs"];
            foreach($targets as $target) {
                if(in_array($target, array_keys($conjunction))){
                    $conjunction[$target]["inputs"][$source] = false;
                } else if(!in_array($target, array_keys($flipflop))) {
                    // Output module
                    $flipflop[$target] = array(false, array());
                } 
            }
        }
        return [$broadcaster, $flipflop, $conjunction];
    }

    protected $_q;
    protected $_broadcaster;
    protected $_flipflop;
    protected $_conjunction;
}

function solve(string $filename, int $cycles) : int
{        
    $q = new Queue();
    $q->from_file($filename);
    $sum_high = 0;
    $sum_low = 0;
    for($i = 0; $i < $cycles; $i++){
        [$high, $low] = $q->run();      
        $sum_high += $high;
        $sum_low += $low;        
    }
    return $sum_high * $sum_low;
}

function gcd($a, $b)
{
    if ($b == 0)
        return $a;
    return gcd($b, $a % $b);
}

function least_common_multiple(array $arr) : int
{
    $ans = $arr[0];
    for ($i = 1; $i < count($arr); $i++)
        $ans = ((($arr[$i] * $ans)) / (gcd($arr[$i], $ans)));
    return $ans;
}

function solve2(string $filename, string $outmod="") : int
{        
    $q = new Queue();
    $q->from_file($filename);
    $flag = false;
    $i = 0;
    while(!$flag){
        [$high, $low, $flag] = $q->run($outmod);
        $i++;
    }
    return $i;
}

function main(): void
{    
    /**** PART 1 ****/
    $res = solve("sample.txt", 1000);
    echo $res;
    if (32000000 !== $res)
        return;
    printf("... sample passed!\n");
    $res = solve("sample2.txt", 1000);
    echo $res;
    if (11687500 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true);
    $res = solve("input.txt", 1000);
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);

    /**** PART 2 ****/
    $res = solve2("sample2.txt", $outmod="output", $outstate=false);
    echo $res;
    if (1 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true);    
    // $res = solve2("input.txt", $outmod="rx");
    // vm, lm, jd, fv > zg > rx
    $arr = array();
    array_push($arr, solve2("input.txt", $outmod="vm", $outstate=true));
    array_push($arr, solve2("input.txt", $outmod="lm", $outstate=true));
    array_push($arr, solve2("input.txt", $outmod="jd", $outstate=true));
    array_push($arr, solve2("input.txt", $outmod="fv", $outstate=true));
    $res = least_common_multiple(array_unique($arr)); 
    $toc = microtime(true);
    printf("Answer 2: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);
}
main();

?>