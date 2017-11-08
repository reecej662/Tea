<?php
	class Event{
		
		// database connection and table name
		private $conn;
		private $table_name = "events";

		// object properties
		// CHAGNE THIS
		public $id;
		public $userId;
		public $eventId;
		public $title;
		public $allDay;
		public $start;
		public $end;
		public $url;
		public $className;
		public $editable;
		public $startEditable;
		public $durationEditable;
		public $overlap;
		public $constraint;
		public $source;
		public $color;
		public $backgroundColor;
		public $borderColor;
		public $textColor;
		public $created;

		// constructor with $db as database connection
		public function __construct($db) {
			$this->conn = $db;
		}

		// read events
		function read(){
		    // select all query
			$query = "";
			
			if(empty($this->userId)) {
			    $query = "SELECT
			                e.id, e.userId, e.eventId, e.title, e.start, e.end, e.created
			            FROM
			                " . $this->table_name . " e
			            ORDER BY
			                e.start";

			    // prepare query statement
			    $stmt = $this->conn->prepare($query);
			} else {
			    $query = "SELECT
			                e.id, e.userId, e.eventId, e.title, e.start, e.end, e.created
			            FROM
			                " . $this->table_name . " e
			            WHERE
					e.userId = ?
				    ORDER BY
					e.start";

			    // prepare query statement
			    $stmt = $this->conn->prepare($query);
			 
			    $stmt->bindParam(1, $this->userId);

            		}	

		    // execute query
		    $stmt->execute();
		 
		    return $stmt;
		}

		// create product
		function create(){
		 
		    // query to insert record
		    $query = "INSERT INTO events SET userId=:userId, eventId=:eventId, title=:title, start=:start, end=:end";
		 
		    // prepare query
		    $stmt = $this->conn->prepare($query);
		 
		    // sanitize
		    $this->userId=htmlspecialchars(strip_tags($this->userId));
		    $this->eventId=htmlspecialchars(strip_tags($this->eventId));
		    $this->title=htmlspecialchars(strip_tags($this->title));
		    $this->start=htmlspecialchars(strip_tags($this->start));
		    $this->end=htmlspecialchars(strip_tags($this->end));
		 
		    // bind values
		    $stmt->bindParam(":userId", $this->userId);
		    $stmt->bindParam(":eventId", $this->eventId);
		    $stmt->bindParam(":title", $this->title);
		    $stmt->bindParam(":start", $this->start);
		    $stmt->bindParam(":end", $this->end);
		 
		    // execute query
		    if($stmt->execute()){
		        return true;
		    }else{
		        return false;
		    }
		}

		// update the product
		function update(){
		 
		    // update query
		    $query = "UPDATE
		                " . $this->table_name . "
		            SET
		                userId = :userId,
		                eventId = :eventId,
		                title = :title,
		                start = :start,
		                end = :end
		            WHERE
		                id = :id";
		 
		    // prepare query statement
		    $stmt = $this->conn->prepare($query);
		 
		    // sanitize
		    $this->userId=htmlspecialchars(strip_tags($this->userId));
		    $this->eventId=htmlspecialchars(strip_tags($this->eventId));
		    $this->title=htmlspecialchars(strip_tags($this->title));
		    $this->start=htmlspecialchars(strip_tags($this->start));
		    $this->end=htmlspecialchars(strip_tags($this->end));
		 
		    // bind values
		    $stmt->bindParam(":userId", $this->userId);
		    $stmt->bindParam(":eventId", $this->eventId);
		    $stmt->bindParam(":title", $this->title);
		    $stmt->bindParam(":start", $this->start);
		    $stmt->bindParam(":end", $this->end);
		    $stmt->bindParam(":id", $this->id);

		    // execute the query
		    if($stmt->execute()){
		        return true;
		    }else{
		        return false;
		    }
		}

		// delete the product
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

		// delete repeating events
		function deleteRepeating(){
		 
		    // delete query
		    $query = "DELETE FROM " . $this->table_name . " WHERE eventId = ?";
		 
		    // prepare query
		    $stmt = $this->conn->prepare($query);
		 
		    // sanitize
		    $this->eventId=htmlspecialchars(strip_tags($this->eventId));
		 
		    // bind id of record to delete
		    $stmt->bindParam(1, $this->eventId);
		 
		    // execute query
		    if($stmt->execute()){
		        return true;
		    }
		 
		    return false;
		     
		}
	}
