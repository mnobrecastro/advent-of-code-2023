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

function parse_file(string $filename, bool $is_hexa) : array
{   
    $arr = array();
    $generator = gen_line($filename);    
    while ($generator->valid()) {
        $line = $generator->current();
        [$cmd, $hash] = explode("(", $line);
        [$move, $steps] = explode(" ", $cmd);
        $hash = substr($hash, 1, -1);
        //echo $move." ".$steps." ".$hash."\n";
        if($is_hexa) {
            // The last hexadecimal digit encodes the direction to dig:
            // 0 means R, 1 means D, 2 means L, and 3 means U.
            switch(hexdec(substr($hash, -1))) {
                case 0:
                    $move = "R";
                    break;
                case 1:
                    $move = "D";
                    break;
                case 2:
                    $move = "L";
                    break;
                case 3:
                    $move = "U";
                    break;
            }
            $steps = hexdec(substr($hash, 0, -1));
        }
        //echo "* ".$move." ".$steps." ".$hash."\n";
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

    function from_file(string $filename, bool $is_hexa) : void
    {
        // Build the graph
        $arr = parse_file($filename, $is_hexa);
        $i = 0; $j = 0;
        $tail = sprintf("%d,%d", $i, $j);
        foreach($arr as [$move, $steps, $hash]){ 
            switch($move) {
                case "U":
                    $i -= intval($steps);
                    break;
                case "D":
                    $i += intval($steps);
                    break;
                case "L":
                    $j -= intval($steps);
                    break;
                case "R":
                    $j += intval($steps);
                    break;
            }
            $head = sprintf("%d,%d", $i, $j);
            //echo $tail." -> ".$head."\n";
            $this->add_edge($tail, $head);
            $tail = $head;
        }
        return;
    }

    public function add_edge($tail, $head) : void
    {
        // if(!in_array($tail, array_keys($this->_adj)))
        //     $this->_adj[$tail] = array();
        // array_push($this->_adj[$tail], $head);
        $this->_adj[$tail] = $head;
    }
    
    public function get_size() : int
    {
        $sum = 0;
        foreach($this->_adj as $tail => $head) {
            //echo "^".$tail." -> ".$head."\n";
            [$t_i, $t_j] = explode(",", $tail);
            $t_i = intval($t_i);
            $t_j = intval($t_j);
            [$h_i, $h_j] = explode(",", $head);
            $h_i = intval($h_i);
            $h_j = intval($h_j);
            $sum += abs($t_i - $h_i) + abs($t_j - $h_j);
        }
        return $sum;
        // return count($this->_adj);
    }

    public function flood_fill($vertex) : void
    {
        [$i, $j] = explode(",", $vertex);
        $i = intval($i);
        $j = intval($j);

        foreach($this->_adj as $tail => $head) {
            //echo "*".$tail." -> ".$head."\n";
            [$t_i, $t_j] = explode(",", $tail);
            $t_i = intval($t_i);
            $t_j = intval($t_j);
            [$h_i, $h_j] = explode(",", $head);
            $h_i = intval($h_i);
            $h_j = intval($h_j);
            if($t_i == $h_i && $t_i == $i){
                // Horizontal
                if($t_j > $h_j){
                    // Left
                    if($h_j <= $j && $j <= $t_j) {
                        return;
                    }
                } else if ($t_j < $h_j){
                    // Right
                    if($t_j <= $j && $j <= $h_j) {
                        return;
                    }
                }
            }else if($t_j == $h_j && $t_j == $j){
                // Vertical
                if($t_i > $h_i){
                    // Up
                    if($h_i <= $i && $i <= $t_i) {
                        return;
                    }
                } else if ($t_i < $h_i){
                    // Down
                    if($t_i <= $i && $i <= $h_i) {
                        return;
                    }
                }
            }
        }
        if (in_array($vertex, $this->_inside)) {
            return;
        } else {
            array_push($this->_inside, $vertex);
            if(count($this->_inside) % 10000 == 0){
                echo "Inside: ".count($this->_inside)."\n";
            }
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

    public function get_area() : int
    {
        $area = 0.0;
        $start = array(0.0, 0.0);
        $flag = true;
        foreach($this->_adj as $tail => $head) {            
            
            [$t_i, $t_j] = explode(",", $tail);
            $t_i = floatval($t_i);
            $t_j = floatval($t_j);
            [$h_i, $h_j] = explode(",", $head);
            $h_i = floatval($h_i);
            $h_j = floatval($h_j);

            if($flag) {
                $flag = false;
                continue;
            }

            $area += $this->_signed_area($start, array($t_i, $t_j), array($h_i, $h_j));
            //echo "area: ".$area."\n";
        }
        return $area;
    }

    protected $_adj;
    protected $_inside;

    protected function _signed_area(array $p1, array $p2, array $p3) : float
    {
        return 0.5 * (
            -$p1[0] * $p2[1] + $p1[1] * $p2[0] +
            $p1[0] * $p3[1] - $p2[0] * $p3[1] +
            -$p1[1] * $p3[0] + $p2[1] * $p3[0] );
    }
}

function solve(string $filename, bool $is_hexa=false) : int
{        
    $g = new DirectedGraph();
    $start = $g->from_file($filename, $is_hexa);
    $contour = $g->get_size();
    //$g->flood_fill("1,1");
    //$fill = $g->get_inside();
    $area = $g->get_area();    
    $fill = $area + 1 - $contour/2; // Pick's theorem   
    echo "contour: ".$contour." fill: ".$fill."\n";
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

    /**** PART 2 ****/
    $res = solve("sample.txt", $is_hexa=true);
    echo $res;
    if (952408144115 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true); 
    $res = solve("input.txt", $is_hexa=true);
    $toc = microtime(true);
    printf("Answer 2: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);
}
main();

?>