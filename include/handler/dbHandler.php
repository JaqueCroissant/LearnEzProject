<?php
    class DbHandler 
    {
        private $_dsn = 'mysql:dbname=mokhtar_project_learnez_;host=mysql7.gigahost.dk:3306';
        private $_username;
        private $_password;
        
        private $_conn = null;
        private $_prepare;
        private static $_instance;
        
        public $error;
        
        public function __construct ($username, $password) 
        {
            $this->_username = $username;
            $this->_password = $password;
            $this->connect();
        }
        
        public static function get_instance()
        {
            if ( is_null( self::$_instance ) )
            {
              self::$_instance = new self(db_info::$db_username, db_info::$db_password);
            }
            return self::$_instance;
        }
        
        private function connect() 
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
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
        }
        
        private function get_conn_instance() {
            if($this->_conn == null) {
                $this->connect();
            }
            return $this->_conn;
        }
        
        private function handle_arguments($query, $num_args, $args) {
            if($num_args > 0) {
                $queryArguments = $this->find_arguments($this->get_char_array($query));
                $arguments = $args;
                
                for($i = 1; $i < count($args); $i++) {
                    $this->add_argument($queryArguments[$i-1], $arguments[$i]);
                }
            }
        }
        
        private function add_argument($argName, $argValue) {
            $this->_prepare->bindParam($argName, $argValue, $this->get_argument_type($argValue));
        }
        
        private function find_arguments($charArray) {
            $argArray = array();
            $currentArg = "";
            $isValid = false;
            
            for($i = 0; $i < count($charArray); $i++) {
                if($isValid) {
                    if(!preg_match('/^[a-zA-Z_]+$/', $charArray[$i]) || $i == count($charArray)-1) {
                        if($i == count($charArray)-1 && preg_match('/^[a-zA-Z_]+$/', $charArray[$i])) {
                            $currentArg .= $charArray[$i];
                        }
                        
                        $isValid = false;
                        $argArray[] = $currentArg;
                        $test = $currentArg;
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
        
        private function get_char_array($string) {
            return str_split($string);
        }
        
        private function get_argument_type($arg) {
            switch(gettype($arg)) {
                case "integer":
                    return PDO::PARAM_INT;
                case "boolean":
                    return PDO::PARAM_BOOL;
                default:
                    return PDO::PARAM_STR;  
            }
        }
        
        public function query($query) 
        {
            try {
                $this->_prepare = $this->get_conn_instance()->prepare($query);
                $this->handle_arguments($query, func_num_args(), func_get_args());
                $this->_prepare->execute();
                return true;
            }
            catch (PDOException $ex) 
            {
                $this->error = ErrorHandler::return_error($ex->getMessage());
                echo $ex->getMessage();
            }
            return false;
        }
        
        public function return_query($query) 
        {
            try {
                $this->_prepare = $this->get_conn_instance()->prepare($query);
                $this->handle_arguments($query, func_num_args(), func_get_args());
                $this->_prepare->execute();
                
                if($this->_prepare->rowCount() > 0) {
                    return $this->_prepare->fetchall(PDO::FETCH_ASSOC);
                }
                return null;
            }
            catch (PDOException $ex) 
            {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
        }
        
        public function last_inserted_id() 
        {
            try {
                if(!isset($this->_conn) || empty($this->_conn)) {
                    return;
                }
                return $this->_conn->lastInsertId();
            }
            catch (PDOException $ex) 
            {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
        }
        
        public function count_query($query) 
        {
            try {
                $this->_prepare = $this->get_conn_instance()->prepare($query);
                $this->handle_arguments($query, func_num_args(), func_get_args());
                $this->_prepare->execute();
                
                return $this->_prepare->rowCount();
            }
            catch (PDOException $ex) 
            {
                $this->error = ErrorHandler::return_error($ex->getMessage());
            }
        }
    }
?>
