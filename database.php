
<?php

	function getConnection()
	{
		$conn = new mysqli("host", "user", "passwd", "db", "3306");
		return $conn;
	}
	
?>
