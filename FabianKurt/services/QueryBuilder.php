<?php





namespace services;

class QueryBuilder
{
    private $statement = array();
    private $parameters = array();
    private $MODES = array('select', 'update', 'insert', 'delete');
    private $JOINMODES = array('left join', 'right join', 'inner join', 'outer join');
    private $CONDITIONOPERATOR = array('=', '!=', '<', '>', '>=', '<=', 'LIKE', 'IS');
    private $selectedMode = -1;
    private $dbConnection;
    private $limit = -1;
    private $offset = -1;

    public function setMode($index){
        $this->dbConnection = DBConnection::getDbConnection();
        if (!($index > count($this->MODES) || $index < 0 )){
            $this->selectedMode = $index;
        }
        return$this;
    }

    public function setTable($table) {
        $table = strtolower($table);
        if (!isset($this->statement['Tables'])){
            $this->statement['Tables'][] = $table;
        } else if(!in_array($table, $this->statement['Tables'])){
            $this->statement['Tables'][] = $table;
        }
        return$this;
    }

    public function setCols($table, array $cols = array()){
        $table = strtolower($table);
        if (count($cols) <= 0){
            $this->statement['Columns'][$table][] = '*';
        } else {
            foreach ($cols as $col)
                $this->statement['Columns'][$table][] = $col;
        }
        return$this;
    }

    public function setColsWithValues($table, array $cols, array $values){
        $this->statement['ColsWVals'] = array('Table' => $table, 'Columns' => $cols, 'Values' => $values);
        return$this;
    }

    public function joinTable($joinTable, $foreignTable, $index, $fkCol, bool $swap = false){
        if (!($index > count($this->JOINMODES) || $index < 0 )){
            $foreignTable = strtolower($foreignTable);
            $joinTable = strtolower($joinTable);
            $this->statement['Joins'][$this->JOINMODES[$index]][$joinTable][] =  $foreignTable."|".$fkCol."|".$swap;
        }
        return$this;
    }


    public function addCond($table, $col, $index, $cond, $andOr){
        if (!($index > count($this->CONDITIONOPERATOR) || $index < 0 )) {
            $table = strtolower($table);

            $this->statement['Conditions'][] = array('Table' => $table, 'Column' => $col, 'Operator' => (($index > 5) ? ' ' : '').$this->CONDITIONOPERATOR[$index], 'Condition' => $cond, 'ConnectionMode' => (($andOr) ? 'and' : 'or'));
        }
        return$this;
    }


    public function limitOffset(int $limit, int $offset = 0){
        $this->limit = $limit;
        $this->offset = $offset;

        return$this;
    }


    public function orderBy(array $cols){
        foreach($cols as $col){
            $this->statement['Order'][] = $col;
        }
        return$this;
    }


    public function groupBy(array $cols){
        foreach($cols as $col){
            $this->statement['Group'][] = $col;
        }
        return$this;
    }


    private function createSelectStatement(){
        $statement = "select ";

        if(isset($this->statement['Columns'])){
            $string = "";
            foreach($this->statement['Columns'] as  $key => $value){
                foreach($value as $cols){
                    if ($cols != "*")
                        $string .= $key.".".$cols.",";
                }
            }
            $statement .= substr($string, 0, -1);
        } else {
            $statement .= "*";
        }

        $statement .= " from ";

        if (isset($this->statement['Tables'])){
            $statement .= $this->statement['Tables'][0]." ";
        }

        if (isset($this->statement['Joins'])){
            $string = "";
            foreach($this->statement['Joins'] as  $key => $value) {
                foreach($value as $ptkey => $pt){
                    foreach($pt as $val){
                        $vals = explode('|', $val);
                        if (!$vals[2]){
                            $string .= $key." ".$ptkey." on ".$ptkey.".id=".$vals[0].".".$vals[1]." ";
                        } else {
                            $string .= $key." ".$ptkey." on ".$ptkey.".".$vals[1]."=".$vals[0].".id ";
                        }
                    }
                }
            }
            $statement .= $string;
        }

        if (isset($this->statement['Conditions'])){
            $string = "where ";
            $start = true;
            foreach($this->statement['Conditions'] as $conditions){
                $this->parameters[] = $conditions['Condition'];
                if ($start){
                    $string .= $conditions['Table'].".".$conditions['Column'].$conditions["Operator"]."?";
                    $start = false;
                    continue;
                }
                $string .= " ".$conditions['ConnectionMode']." ".$conditions['Table'].".".$conditions['Column'].$conditions["Operator"]."?";

            }
            $statement .= $string;
        }

        if (isset($this->statement['Group'])){
            $string = "";
            foreach($this->statement['Group'] as $order){
                $string .= $order.",";
            }
            $statement .= " group by ".substr($string, 0, -1);
        }

        if (isset($this->statement['Order'])){
            $string = "";
            foreach($this->statement['Order'] as $order){
                $string .= $order.",";
            }
            $statement .= " order by ".substr($string, 0, -1);
        }

        if ($this->limit > 0 ){
            $statement .= " limit ".$this->limit." offset ".$this->offset;
        }



        return$statement.";";
    }


    private function createUpdateStatement(){
        $statement = "update ";

        if (isset($this->statement['ColsWVals']) && count($this->statement['ColsWVals']['Values']) == count($this->statement['ColsWVals']['Columns'])){
            $statement .= $this->statement['ColsWVals']['Table'];
            $statement .= " set ";
            for($i = 0;$i < count($this->statement['ColsWVals']['Columns']); $i++) {
                $this->parameters[] = $this->statement['ColsWVals']['Values'][$i];
                $statement .= $this->statement['ColsWVals']['Columns'][$i]."=?,";
            }
            $statement = substr($statement, 0, -1);
        }

        if (isset($this->statement['Conditions'])){
            $string = " where ";
            $start = true;
            foreach($this->statement['Conditions'] as $conditions){
                $this->parameters[] = $conditions['Condition'];
                if ($start){
                    $string .= $conditions['Table'].".".$conditions['Column'].$conditions["Operator"]."?";
                    $start = false;
                    continue;
                }
                $string .= " ".$conditions['ConnectionMode']." ".$conditions['Table'].".".$conditions['Column'].$conditions["Operator"]."?";

            }
            $statement .= $string;
        }
        return$statement.";";
    }


    public function createInsertStatement(){
        $statement = "insert into ";

        if (isset($this->statement['ColsWVals']) && count($this->statement['ColsWVals']['Values']) == count($this->statement['ColsWVals']['Columns'])){
            $statement .= $this->statement['ColsWVals']['Table']." (";
            foreach($this->statement['ColsWVals']['Columns'] as $cols){
                $statement .= $cols.",";
            }
            $statement = substr($statement, 0, -1).") values (";
            foreach($this->statement['ColsWVals']['Values'] as $vals){
                $this->parameters[] = $vals;
                $statement .= "?,";
            }
            $statement = substr($statement, 0, -1).")";
        }
        return$statement.";";
    }


    private function createDeleteStatement(){
        $statement = "delete from ";

        if (isset($this->statement['Tables'])){
            $statement .= $this->statement['Tables'][0]." ";
        }

        if (isset($this->statement['Conditions'])){
            $string = "where ";
            $start = true;
            foreach($this->statement['Conditions'] as $conditions){
                $this->parameters[] = $conditions['Condition'];
                if ($start){
                    $string .= $conditions['Table'].".".$conditions['Column'].$conditions["Operator"]."?";
                    $start = false;
                    continue;
                }
                $string .= " ".$conditions['ConnectionMode']." ".$conditions['Table'].".".$conditions['Column'].$conditions["Operator"]."?";

            }
            $statement .= $string;
        }
        return$statement.";";
    }


    public function executeStatement(){
        $stmt = $this->getStatement();
        $statement = $this->dbConnection->prepare($stmt);
        foreach($this->parameters as $key => $param){
            $statement->bindValue(($key+1), htmlspecialchars($param, ENT_QUOTES));
        }
        //var_dump($stmt);
        //var_dump($statement);
        $statement->execute();
        if ($this->selectedMode == 2){
            $result = $this->dbConnection->lastInsertId();
        } else {
            $result = $statement->fetchAll();
        }
        $this->reset();
        return$result;
    }


    public function getStatement(){
        $statement = "";
        switch($this->selectedMode) {
            case 0:
                $statement = $this->createSelectStatement();
                break;
            case 1:
                $statement = $this->createUpdateStatement();
                break;
            case 2:
                $statement = $this->createInsertStatement();
                break;
            case 3:
                $statement = $this->createDeleteStatement();
                break;
            default:

                break;
        }
        return$statement;
    }


    public function getStatementArray(){
        return$this->statement;
    }


    private function reset(){
        $this->selectedMode = -1;
        $this->limit = -1;
        $this->offset = -1;
        $this->statement = array();
        $this->parameters = array();
    }
}