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

class DirectedGraph
{    
    public function __construct()
    {
        $this->_adj = array();
    }

    public function add_edge($tail, $head) : void
    {
        if(!in_array($tail, array_keys($this->_adj)))
            $this->_adj[$tail] = array();
        array_push($this->_adj[$tail], $head);
    }    

    public function depth_first_search($source) : int
    {
        if(count($this->_adj) == 0 ||
            ! in_array($source, array_keys($this->_adj)))
            return 0;
        else {
            $this->_parent = array();
            $this->_dfs_visit($source);
        }
        $k = 0;
        $vertex = $source;
        while($this->_parent[$vertex] != $source){
            $k++;
            $vertex = $this->_parent[$vertex];
        }
        return $k;
    }

    protected $_adj;
    protected $_parent;

    private function _dfs_visit($tail)
    {
        foreach($this->_adj[$tail] as $head) {
            if(!in_array($head, array_keys($this->_parent))) {
                $this->_parent[$head] = $tail;
                $this->_dfs_visit($head);
            }
        }
    }
}

function solve(string $filename) : int
{        
    $pipes = ["|", "-", "L", "J", "7", "F", "S"];   
    $start = "";
    // Build the graph
    $mat = parse_file($filename);
    $g = new DirectedGraph();
    for($i = 0; $i < count($mat); $i++) {
        for($j = 0; $j < count($mat[0]); $j++) {
            if(in_array($mat[$i][$j], $pipes)) {
                switch ($mat[$i][$j]) {
                    case "|":
                        // Up
                        if ($i-1 >= 0){
                            if(in_array($mat[$i-1][$j], ["|", "7", "F"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i-1, $j);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        // Down
                        if ($i+1 <= count($mat)){
                            if(in_array($mat[$i+1][$j], ["|", "L", "J"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i+1, $j);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        break;

                    case "-":
                        // Left
                        if ($j-1 >= 0){
                            if(in_array($mat[$i][$j-1], ["-", "L", "F"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i, $j-1);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        // Right
                        if ($j+1 <= count($mat[0])){
                            if(in_array($mat[$i][$j+1], ["-", "J", "7"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i, $j+1);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        break;

                    case "L":
                        // Up
                        if ($i-1 >= 0){
                            if(in_array($mat[$i-1][$j], ["|", "7", "F"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i-1, $j);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        // Right
                        if ($j+1 <= count($mat[0])){
                            if(in_array($mat[$i][$j+1], ["-", "J", "7"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i, $j+1);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        break;

                    case "J":
                        // Up
                        if ($i-1 >= 0){
                            if(in_array($mat[$i-1][$j], ["|", "7", "F"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i-1, $j);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        // Left
                        if ($j-1 >= 0){
                            if(in_array($mat[$i][$j-1], ["-", "L", "F"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i, $j-1);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        break;

                    case "7":
                        // Left
                        if ($j-1 >= 0){
                            if(in_array($mat[$i][$j-1], ["-", "L", "F"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i, $j-1);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        // Down
                        if ($i+1 <= count($mat)){
                            if(in_array($mat[$i+1][$j], ["|", "L", "J"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i+1, $j);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        break;

                    case "F":
                        // Right
                        if ($j+1 <= count($mat[0])){
                            if(in_array($mat[$i][$j+1], ["-", "J", "7"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i, $j+1);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        // Down
                        if ($i+1 <= count($mat)){
                            if(in_array($mat[$i+1][$j], ["|", "L", "J"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i+1, $j);
                                $g->add_edge($tail, $head);
                            }
                        }                        
                        break;

                    case "S":
                        $start = sprintf("%d,%d", $i, $j);
                        // Up *
                        if ($i-1 >= 0){
                            if(in_array($mat[$i-1][$j], ["|", "7", "F"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i-1, $j);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        // Down
                        if ($i+1 <= count($mat)){
                            if(in_array($mat[$i+1][$j], ["|", "L", "J"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i+1, $j);
                                $g->add_edge($head, $tail);
                            }                            
                        }
                        // Left
                        if ($j-1 >= 0){
                            if(in_array($mat[$i][$j-1], ["-", "L", "F"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i, $j-1);
                                $g->add_edge($head, $tail);
                            }                            
                        }
                        // Right *
                        if ($j+1 <= count($mat[0])){
                            if(in_array($mat[$i][$j+1], ["-", "J", "7"])){
                                $tail = sprintf("%d,%d", $i, $j);
                                $head = sprintf("%d,%d", $i, $j+1);
                                $g->add_edge($tail, $head);
                            }                            
                        }
                        break;
                }
            }
        }
    }
    
    return intdiv($g->depth_first_search($start) + 1, 2);
}


function main(): void
{   
    $g = new DirectedGraph();
    $g->add_edge("a", "b");
    $g->add_edge("b", "c");
    $g->add_edge("c", "d");
    $g->add_edge("d", "b");
    $g->add_edge("d", "a");
    echo $g->depth_first_search("a"), "\n";
    
    /**** PART 1 ****/       
    $res = solve("sample.txt");
    echo $res;    
    if (8 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true); 
    $res = solve("input.txt");
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);
}
main();

?>