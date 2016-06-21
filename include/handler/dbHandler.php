<?php
    class DbHandler 
    {
        private $dsn = 'mysql:dbname=mokhtar_project_learnez_;host=mysql7.gigahost.dk:3306';
        private $username;
        private $password;
        
        private $conn = null;
        private $prepare;
        
        public function __construct ($username, $password) 
        {
            $this->username = $username;
            $this->password = $password;
            $this->Connect();
        }
        
        private function Connect() 
        {
            try {
                $this->conn = new PDO($this->dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } 
            catch (PDOException $ex) 
            {
                $errorMessage = ErrorHandler::ReturnError($ex->getCode());
                echo $errorMessage;
            }
        }
        
        private function GetConnInstance() {
            if($this->conn == null) {
                $this->Connect();
            }
            return $this->conn;
        }
        
        private function HandleArguments($query, $num_args, $args) {
            if($num_args > 0) {
                $queryArguments = $this->FindArguments($this->GetCharArray($query));
                $arguments = $args;
                
                for($i = 1; $i < count($args); $i++) {
                    $this->AddArgument($queryArguments[$i-1], $arguments[$i]);
                }
            }
        }
        
        private function AddArgument($argName, $argValue) {
            $this->prepare->bindParam($argName, $argValue, $this->GetArgumentType($argValue));
        }
        
        private function FindArguments($charArray) {
            $argArray = array();
            $currentArg = "";
            $isValid = false;
            
            for($i = 0; $i < count($charArray); $i++) {
                if($isValid) {
                    if(!preg_match('/^[a-zA-Z_]+$/', $charArray[$i]) || $i == count($charArray)-1) {
                        
                        if($i == count($charArray)-1) {
                            $currentArg .= $charArray[$i];
                        }
                        
                        $isValid = false;
                        $argArray[] = $currentArg;
                        $currentArg = "";
                    } else {
                        $currentArg .= $charArray[$i];
                    }   
                } else {
                    if($charArray[$i] == ":") {
                        $isValid = true;
                    }
                }
            }
            return $argArray;
        }
        
        private function GetCharArray($string) {
            return str_split($string);
        }
        
        private function GetArgumentType($arg) {
            switch(gettype($arg)) {
                case "integer":
                    return PDO::PARAM_INT;
                case "boolean":
                    return PDO::PARAM_BOOL;
                default:
                    return PDO::PARAM_STR;  
            }
        }
        
        public function Query($query) 
        {
            try {
                $this->prepare = $this->GetConnInstance()->prepare($query);
                $this->HandleArguments($query, func_num_args(), func_get_args());
                $this->prepare->execute();
            }
            catch (PDOException $ex) 
            {
                $errorMessage = ErrorHandler::ReturnError($ex->getCode());
                echo $ex->getMessage();
                echo $errorMessage;
            }
        }
        
        public function ReturnQuery($query) 
        {
            try {
                $this->prepare = $this->GetConnInstance()->prepare($query);
                $this->HandleArguments($query, func_num_args(), func_get_args());
                $this->prepare->execute();
                
                if($this->prepare->rowCount() > 0) {
                    return $this->prepare->fetchall(PDO::FETCH_ASSOC);
                }
            }
            catch (PDOException $ex) 
            {
                $errorMessage = ErrorHandler::ReturnError($ex->getCode());
                echo $ex->getMessage();
                echo $errorMessage;
            }
        }
        
        public function CountQuery($query) 
        {
            try {
                $this->prepare = $this->GetConnInstance()->prepare($query);
                $this->HandleArguments($query, func_num_args(), func_get_args());
                $this->prepare->execute();
                
                return $this->prepare->rowCount();
            }
            catch (PDOException $ex) 
            {
                $errorMessage = ErrorHandler::ReturnError($ex->getCode());
                echo $ex->getMessage();
                echo $errorMessage;
            }
        }
    }
?>
