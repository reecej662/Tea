<?php
	class User{
		
		// database connection and table name
		private $conn;
		private $table_name = "users";

		// object properties
		public $id;
		public $username;
		public $email;
		public $firstName;
		public $lastName;
		public $created;

		// constructor with $db as database connection
		public function __construct($db) {
			$this->conn = $db;
		}

		// read events
		function read(){
		    // select all query
		    $query = "SELECT u.id, u.email, u.firstName, u.lastName, u.created FROM users u WHERE u.username = '" . $this->username . "' ORDER BY u.created DESC";
		 
		    // prepare query statement
		    $stmt = $this->conn->prepare($query);
		 
		    // execute query
		    $stmt->execute();
		 
		    return $stmt;
		}

		// create product
		function create(){
		 
		    // query to insert record
		    $query = "INSERT INTO
		                " . $this->table_name . "
		            SET
		                username=:username, email=:email, firstName=:firstName, lastName=:lastName";
		 
		    // prepare query
		    $stmt = $this->conn->prepare($query);
		 
		    // sanitize
		    $this->username=htmlspecialchars(strip_tags($this->username));
		    $this->email=htmlspecialchars(strip_tags($this->email));
		    $this->firstName=htmlspecialchars(strip_tags($this->firstName));
		    $this->lastName=htmlspecialchars(strip_tags($this->lastName));
		 
		    // bind values
		    $stmt->bindParam(":username", $this->username);
		    $stmt->bindParam(":email", $this->email);
		    $stmt->bindParam(":firstName", $this->firstName);
		    $stmt->bindParam(":lastName", $this->lastName);
		 
		    // execute query
		    if($stmt->execute()){
		        return true;
		    }else{
		        return false;
		    }
		}

		// used to fetch a specific user
		function readOne(){
		 
		    // query to read single record
		    $query = "SELECT u.id, u.username, u.email, u.firstName, u.lastName, u.created FROM users u WHERE u.username = ? LIMIT 0,1";
		 
		    // prepare query statement
		    $stmt = $this->conn->prepare( $query );
		 
		    // bind id of user to be updated
		    $stmt->bindParam(1, $this->username);
		 
		    // execute query
		    $stmt->execute();
		 
		    // get retrieved row
		    $row = $stmt->fetch(PDO::FETCH_ASSOC);
		 
		    // set values to object properties
		    $this->id = $row['id'];
		    $this->username = $row['username'];
		    $this->email = $row['email'];
		    $this->firstName = $row['firstName'];
		    $this->lastName = $row['lastName'];
		    $this->created = $row['created'];
		}

		// update the product
		function update(){
		 
		    // update query
		    $query = "UPDATE
		                " . $this->table_name . "
		            SET
		                username=:username, email=:email, firstName=:firstName, lastName=:lastName
		            WHERE
		                id = :id";
		 
		    // prepare query statement
		    $stmt = $this->conn->prepare($query);

		    // sanitize
		    $this->id=htmlspecialchars(strip_tags($this->id));
		    $this->username=htmlspecialchars(strip_tags($this->username));
		    $this->email=htmlspecialchars(strip_tags($this->email));
		    $this->firstName=htmlspecialchars(strip_tags($this->firstName));
		    $this->lastName=htmlspecialchars(strip_tags($this->lastName));
		 
		    // bind new values
		    $stmt->bindParam(":id", $this->id);
		    $stmt->bindParam(":username", $this->username);
		    $stmt->bindParam(":email", $this->email);
		    $stmt->bindParam(":firstName", $this->firstName);
		    $stmt->bindParam(":lastName", $this->lastName);
		 
		    // execute the query
		    if($stmt->execute()){
		        return true;
		    }else{
		        return false;
		    }
		}

		// delete the user
		function delete(){
		 
		    // delete query
		    $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
		 
		    // prepare query
		    $stmt = $this->conn->prepare($query);
		 
		    // sanitize
		    $this->id=htmlspecialchars(strip_tags($this->id));
		 
		    // bind id of record to delete
		    $stmt->bindParam(1, $this->id);
		 
		    // execute query
		    if($stmt->execute()){
		        return true;
		    }
		 
		    return false;
		}
	}
