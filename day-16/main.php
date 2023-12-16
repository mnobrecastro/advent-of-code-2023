
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

enum Move
{
    case Up;
    case Down;
    case Left;
    case Right;
}

function move(array $pos, Move $mv) : array
{
    switch($mv){
        case Move::Up:
            $pos[0] -= 1;
            break;

        case Move::Down:
            $pos[0] += 1;
            break;

        case Move::Left:
            $pos[1] -= 1;
            break;

        case Move::Right:
            $pos[1] += 1;
            break;
    }
    return $pos;
}

class Map
{ 
    public function __construct()
    {
        $this->_mat = array();
        $this->_rep = array();
        $this->_visited = array();
    }

    public function from_file(string $filename) : void
    {
        $this->_mat = array();
        $generator = gen_line($filename);
        while ($generator->valid()) {
            $line = $generator->current();
            array_push($this->_mat, str_split($line, 1));
            array_push($this->_rep, array_fill(0, count(str_split($line, 1)), 0)); //
            $generator->next();
        }        
        return;
    }

    public function run(array $pos, Move $mv)
    {
        //print_mat($this->_rep);
        if($pos[0] < 0 || $pos[1] < 0 || $pos[0] > count($this->_mat) - 1 || $pos[1] > count($this->_mat[0]) - 1){
            return;
        } else {
            $symbol = $this->_mat[$pos[0]][$pos[1]];
            //echo $symbol, "\n";

            // Add to visited or halt if repeated
            if(!in_array($pos[0].",".$pos[1], array_keys($this->_visited))) {
                $this->_visited[$pos[0].",".$pos[1]] = array();
                array_push($this->_visited[$pos[0].",".$pos[1]], $mv);
            }else {
                if(!in_array($mv, $this->_visited[$pos[0].",".$pos[1]])){
                    array_push($this->_visited[$pos[0].",".$pos[1]], $mv);
                } else {
                    return;
                }
            }

            if ($symbol == ".") {
                $this->run(move($pos, $mv), $mv);
            } else {
                switch($mv){
                    case Move::Up:
                        switch($symbol){
                            case "|":
                                $mv = Move::Up;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "-":
                                $mv = Move::Left;
                                $this->run(move($pos, $mv), $mv);
                                $mv = Move::Right;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "\\":
                                $mv = Move::Left;
                                $this->run(move($pos, $mv), $mv);                            
                                break;
                    
                            case "/":
                                $mv = Move::Right;
                                $this->run(move($pos, $mv), $mv);
                                break;    
                        }
                        break;
            
                    case Move::Down:
                        switch($symbol){
                            case "|":
                                $mv = Move::Down;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "-":
                                $mv = Move::Left;
                                $this->run(move($pos, $mv), $mv);
                                $mv = Move::Right;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "\\":
                                $mv = Move::Right;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "/":
                                $mv = Move::Left;
                                $this->run(move($pos, $mv), $mv); 
                                break;    
                        }
                        break;
            
                    case Move::Left:
                        switch($symbol){
                            case "|":
                                $mv = Move::Up;
                                $this->run(move($pos, $mv), $mv);
                                $mv = Move::Down;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "-":
                                $mv = Move::Left;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "\\":
                                $mv = Move::Up;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "/":
                                $mv = Move::Down;
                                $this->run(move($pos, $mv), $mv);
                                break;    
                        }
                        break;
            
                    case Move::Right:
                        switch($symbol){
                            case "|":
                                $mv = Move::Up;
                                $this->run(move($pos, $mv), $mv);
                                $mv = Move::Down;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "-":
                                $mv = Move::Right;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "\\":
                                $mv = Move::Down;
                                $this->run(move($pos, $mv), $mv);
                                break;
                    
                            case "/":
                                $mv = Move::Up;
                                $this->run(move($pos, $mv), $mv);
                                break;    
                        }
                        break;
                }
            }
        }
        return;
    }

    public function get_visited() : int
    {
        return count(array_keys($this->_visited));
    }

    protected $_mat;
    protected $_rep;
    protected $_visited;
}

function solve(string $filename) : int
{
    $map = new Map();
    $map->from_file($filename);
    $map->run(array(0,0), Move::Right, false);
    return $map->get_visited();
}


function main(): void
{   
    /**** PART 1 ****/       
    $res = solve("sample.txt");
    echo $res;
    if (46 !== $res)
        return;
    printf("... sample passed!\n");
    $tic = microtime(true);
    $res = solve("input.txt");
    $toc = microtime(true);
    printf("Answer 1: %d in %.3f ms.\n", $res, ($toc-$tic)*1e3);
}
main();

?>