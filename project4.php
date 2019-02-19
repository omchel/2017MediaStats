#!/usr/bin/php
<?php
/* 	Authors: Maria Paolini and Chelina Ortiz
	Project 4 PHP
	Purpose: write a very basic web application in PHP that will create a dynamic search form.
		 The PHP script will take user input from the form and perform basic searches on files
		 local to the web server. Searches will involve JSON objects stored in those files.
*/

if (isset($_GET['matchValue'])) { // check if $matchValue is set
	process_form(); //if it is, process the form
} else {
	display_form(); //if it isn't, display the form
}

/* process_form function:
	Function that processes the input from the user, validates it for use in
	other functions.
*/
function process_form() {
	$invalid = false;
	if (isset($_GET['categories'])) { // Check if $categories was set in the form
		$category = $_GET['categories'];
	} else {
		$invalid = true;
		echo $invalid . " category";
	}
	if (isset($_GET['keyToMatch'])) { // Check if $keyToMatch was set in the form
		$keyToMatch  = $_GET['keyToMatch'];
	} else {
		$invalid = true;
		echo $invalid . " keyToMatch";
	}
	if (isset($_GET['matchValue'])) { // Check if $matchValue was set in the form
		$matchValue = $_GET['matchValue'];
	} else {
		$invalid = true;
		echo $invalid . " matchValue";
	}
	if (isset($_GET['infoToProcess'])) { // Check if $infoToProcess was set in the form
		$info = $_GET['infoToProcess'];
	} else {
		$invalid = true;
		echo $invalid . " infoToProcess <br>";
	}
	if (isset($_GET['sumOrAvg'])) { // Check if $sumOrAvg was set in the form
		$sumOrAvg = $_GET['sumOrAvg'];
	} else {
		$invalid = true;
		echo $invalid . " sumOrAvg";
	}
	// call function read_file() and pass in the variables retrieved from the form
	read_file($category,$keyToMatch,$matchValue,$info,$sumOrAvg);
} // end process_form()
/* read_file function:
	Function that reads the file selected by user through the $category variable
	and figures out how to display it.
*/
function read_file($category,$keyToMatch,$matchValue,$infoToProcess,$sumOrAvg) {
	$file = file_get_contents($category); // built-in function that gets the proper file content.
	// call function finding_match() and pass in the value of $file and the variables
	// retrieved from the form.
	finding_match($file,$keyToMatch,$matchValue,$infoToProcess,$sumOrAvg);
} //end read_file()
/* sum_or_avg function:
	Function that takes user input on the way to process the value $infoToProcess,
	either by adding the matched values together or calculating the average of those
	matched values.
*/
function sum_or_avg($sum,$count,$sumOrAvg,$infoToProcess) {
	if ($sum == 0) { // if there were no matches found
		$total = 0; // set $total equal to 0
	} else { // if we found some values to match $infoToProcess requested
		if ($sumOrAvg == "Avg") {
			$total = $sum / $count; // divide the sum of values (previously added together) by the # of matches
		} else {
			$total = $sum; // display the sum of matched values (previously added together)
		}
	}
	echo "<b>" . $sumOrAvg, " of " . $infoToProcess . " that match = ", $total, "</b> <br> <br>";
} // end sum_or_avg()
/* finding_match function:
	Function that matches the $matchValue requested by the user with the $keyToMatch's
	found in the file selected inside the $category variable
*/
function finding_match($file,$keyToMatch,$matchValue,$infoToProcess,$sumOrAvg){
	$json = json_decode($file); // Decode the proper $category file and store in a JSON object
	$sum = 0; // create a variable to add values that match together
	$count = 0;  // create a variable to keep track of the matched values
	start_html();
	foreach ($json->works as $item) { // use the values inside the JSON object under the "works" variable
		if (isset($item->$keyToMatch) && $item->$keyToMatch == $matchValue) { //check if $keyToMatch matches $matchValue
			echo "<b>"; // Bold the matched values
			foreach ($item as $key=>$value) { // Print the matched key/value pairs
				echo $key, ": ", $value, "<br>";
				if (isset($infoToProcess) && $key == $infoToProcess) { // If the key we are looking at is equal to the selected $infoToProcess
					$sum = $sum + $value; // add up the matched value
					$count = $count + 1; // add one matched value to the count
				}
			}
			echo "</b> <br>"; // Close the bold HTML tag
		} else { // if there were no matches found
      foreach ($item as $key=>$value) {
				echo $key, ": ", $value, "<br>"; //print the key/value pairs
      }
			echo "<br>";
		}
	}
	sum_or_avg($sum,$count,$sumOrAvg,$infoToProcess); // call sum_or_avg function and pass in the data found when matches are found
	echo "Comments: <br>"; // print the "comments" section in the JSON Object
	foreach ($json->comments as $aComment) {
		foreach ($aComment as $name=>$comment) {
			echo $comment . " <br>";
		}
	}
	if (json_last_error()){
		echo json_last_error();
	} else {
		end_html();
	}
} // end finding_match()
/* start_html function:
	Function that sets the starting code for the HTML file
*/
function start_html() {
	?>
<HTML>
<HEAD> <TITLE> 2017 Media Stats </TITLE> </HEAD>
<BODY>
<h1> 2017 Media Stats </h1>
	<?php
} //end start_html()
/* end_html() function:
	Function that sets the closing code for the HTML file
*/
function end_html() {
	?>
</BODY>
</HTML>
	<?php
} // end end_html()
/* display_form function:
	Function that displays the code in HTML to retrieve user input through
	a dynamic form. The form uses a JSON file to fill up the form.
*/
function display_form() {
	start_html();
	?>
	<h2> Select one option of each category </h2>
	<form action="project4.php">
	<?php
	$optionsFile = file_get_contents("Media.json");
	$mediaJson = json_decode($optionsFile);
  echo "Categories: <br>";
  echo "<select name=", "categories", ">";
	foreach ($mediaJson->categories as $key=>$value) {
		echo	"<option value=", $value, ">", $key, "</option>";
	}
	echo "</select> <br>";
  echo "Key to Match: <br>";
  echo "<select name=", "keyToMatch", ">";
	foreach($mediaJson->find as $key=>$value){
  echo "<option value=", $value, ">", $value, "</option>";
	}
  echo "</select> <br>";
	?>
	Match Value: <br>
	<input name="matchValue" type= "text">
	<br>
	<?php
	echo "Info to Process: <br>";
	echo "<select name=", "infoToProcess", ">";
	foreach($mediaJson->info as $key=>$value){
		 echo    "<option value=", $value, ">", $value, "</option>";
  }
	echo "</select> <br>";
	?>
	Sum or Average: <br>
	<select name= "sumOrAvg">
		<option value="Sum"> Sum </option>
		<option value="Avg"> Avg </option>
	</select>
	<br>
	<br>
	<input type="submit" value="Submit">
	</form>
	<?php
	end_html();
} // end display_form()
?>
