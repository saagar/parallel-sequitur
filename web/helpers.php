<?

		//generate navbar for each page in backend
	function navBar($page){

		if($page == "home")
			echo "<li class='active'><a href='index.php'>Home</a></li>";
		else
			echo "<li><a href='index.php'>Home</a></li>";

		if($page == "problem")
			echo "<li class='active'><a href='problem.php'>Problem</a></li>";
		else
			echo "<li><a href='problem.php'>Problem</a></li>";

		if($page == "program")
			echo "<li class='active'><a href='program.php'>Program</a></li>";
		else
			echo "<li><a href='program.php'>Program</a></li>";
	
		if($page == "performance")
			echo "<li class='active'><a href='performance.php'>Performance</a></li>";
		else
			echo "<li><a href='performance.php'>Performance</a></li>";

		if($page == "results")
			echo "<li class='active'><a href='results.php'>Results</a></li>";
		else
			echo "<li><a href='results.php'>Results</a></li>";

		if($page == "remarks")
			echo "<li class='active'><a href='remarks.php'>Remarks</a></li>";
		else
			echo "<li><a href='remarks.php'>Remarks</a></li>";

		if($page == "extensions")
			echo "<li class='active'><a href='extensions.php'>Extensions</a></li>";
		else
			echo "<li><a href='extensions.php'>Extensions</a></li>";

		if($page == "resources")
			echo "<li class='active'><a href='resources.php'>Resources</a></li>";
		else
			echo "<li><a href='resources.php'>Resources</a></li>";
			
	}




?>