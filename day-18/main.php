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
    $arr = array();
    $generator = gen_line($filename);    
    while ($generator->valid()) {
        $line = $generator->current();
        [$cmd, $hash] = explode("(", $line);
        [$move, $steps] = explode(" ", $cmd);
        $hash = substr($hash, 1, -1);
        array_push($arr, array($move, $steps, $hash));
        $generator->next();
    }
    return $arr;
}

function print_arr($arr)
{
    foreach($arr as $a){
        echo $a, " ";
    }
    echo "\n";
}

class DirectedGraph
{    
    public function __construct()
    {
        $this->_adj = array();
        $this->_inside = array();
    }

    function from_file(string $filename) : string
    {
        // Build the graph
        $arr = parse_file($filename);
        $i = 0; $j = 0;
        $start = sprintf("%d,%d", $i, $j);
        foreach($arr as [$move, $steps, $hash]){
            for($k = 0; $k < intval($steps); $k++){
                $tail = sprintf("%d,%d", $i, $j);
                switch($move) {
                    case "U":
                        $i--;
                        break;
                    case "D":
                        $i++;
                        break;
                    case "L":
                        $j--;
                        break;
                    case "R":
                        $j++;
                        break;
                }                
                $head = sprintf("%d,%d", $i, $j);
                $this->add_edge($tail, $head);              
            }            
        }
        return $start;
    }

    public function add_edge($tail, $head) : void
    {
        if(!in_array($tail, array_keys($this->_adj)))
            $this->_adj[$tail] = array();
        array_push($this->_adj[$tail], $head);
    }
    
    public function get_size() : int
    {
        return count($this->_adj);
    }

    public function flood_fill($vertex) : void
    {
        if(in_array($vertex, array_keys($this->_adj))) {
            return;
        } else if (in_array($vertex, $this->_inside)) {
            return;
        } else {
            array_push($this->_inside, $vertex);

            [$i, $j] = explode(",", $vertex);
            $i = intval($i);
            $j = intval($j);
            // Up
            $this->flood_fill(sprintf("%s,%s", $i-1, $j));
            // Down
            $this->flood_fill(sprintf("%s,%s", $i+1, $j));
            // Left
            $this->flood_fill(sprintf("%s,%s", $i, $j-1));
            // Right
            $this->flood_fill(sprintf("%s,%s", $i, $j+1));            
        }
        return;
    }

    public function get_inside() : int
    {
        return count($this->_inside);
    }

    protected $_adj;
    protected $_inside;
}

function solve(string $filename) : int
{        
    $g = new DirectedGraph();
    $start = $g->from_file($filename);
    $contour = $g->get_size();
    $g->flood_fill("1,1");
    $fill = $g->get_inside();
    return $contour + $fill;
}


function main(): void
{    
    /**** PART 1 ****/
    $res = solve("sample.txt");
    echo $res;
    if (62 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true); 
    $res = solve("input.txt");
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);
}
main();

?>