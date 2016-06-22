<?php
    class DbHandler 
    {
        private $_dsn = 'mysql:dbname=mokhtar_project_learnez_;host=mysql7.gigahost.dk:3306';
        private $_username;
        private $_password;
        
        private $_conn = null;
        private $_prepare;
        private static $_instance;
        
        public function __construct ($username, $password) 
        {
            $this->_username = $username;
            $this->_password = $password;
            $this->Connect();
        }
        
        public static function getInstance()
        {
            if ( is_null( self::$_instance ) )
            {
              self::$_instance = new self(db_info::$db_username, db_info::$db_password);
            }
            return self::$_instance;
        }
        
        private function Connect() 
        {
            try {
                if($this->_conn != null) {
                    return;
                }
                
                $this->_conn = new PDO($this->_dsn, $this->_username, $this->_password);
                $this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } 
            catch (PDOException $ex) 
            {
                $errorMessage = ErrorHandler::ReturnError("DATABASE_COULD_NOT_CONNECT");
                echo $errorMessage;
            }
        }
        
        private function GetConnInstance() {
            if($this->_conn == null) {
                $this->Connect();
            }
            return $this->_conn;
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
            $this->_prepare->bindParam($argName, $argValue, $this->GetArgumentType($argValue));
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
                $this->_prepare = $this->GetConnInstance()->prepare($query);
                $this->HandleArguments($query, func_num_args(), func_get_args());
                $this->_prepare->execute();
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
                $this->_prepare = $this->GetConnInstance()->prepare($query);
                $this->HandleArguments($query, func_num_args(), func_get_args());
                $this->_prepare->execute();
                
                if($this->_prepare->rowCount() > 0) {
                    return $this->_prepare->fetchall(PDO::FETCH_ASSOC);
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
                $this->_prepare = $this->GetConnInstance()->prepare($query);
                $this->HandleArguments($query, func_num_args(), func_get_args());
                $this->_prepare->execute();
                
                return $this->_prepare->rowCount();
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
