<?php

error_reporting(0);

if (!isset($_REQUEST['search'])) {
	header("Location: /search.html");
	exit(0);
}

$dom = new DOMDocument;
$dom->load('books.xml');

$xpath = new DOMXPath($dom);

$search_input = $_GET['search'];  // Get user input

$lower_search_input = strtolower($search_input);  // Convert search input to lowercase

// Check if "j. k. levenson" contains the lowercase search input
if (strpos("j. k. levenson", $lower_search_input) !== false) {
    echo "Not that easy";
    return;
}

// Input sanitization to prevent basic injection attempts
// $search_input = str_replace("1=1", "", $search_input);
// $search_input = str_replace("or", "", $search_input);
// $search_input = str_replace("and", "", $search_input);

// Use contains() in XPath query to return books with author names that contain the search string
$query = "//book[contains(author,'$search_input')]";

// $test_q1 = "//book[contains(author,'11') or contains(genre,'Mystery')]";
// $results_q1 = $xpath->query($test_q1);
// echo "ISBN: " . $results_q1[0]->getElementsByTagName('ISBN')->item(0)->nodeValue;

$entries = $xpath->query($query);

echo "Search results:<br>";
if ($entries->length > 0) {
    foreach ($entries as $entry) {
        echo "Title: " . $entry->getElementsByTagName('title')->item(0)->nodeValue . "<br>";
        echo "Author: " . $entry->getElementsByTagName('author')->item(0)->nodeValue . "<br>";
        echo "Year: " . $entry->getElementsByTagName('year')->item(0)->nodeValue . "<br>";
        echo "Genre: " . $entry->getElementsByTagName('genre')->item(0)->nodeValue . "<br>";
        echo "ISBN: " . $entry->getElementsByTagName('ISBN')->item(0)->nodeValue . "<br>";
        echo "--------------------------------<br>";
    }
} else {
    echo "Your search for " . htmlentities($search_input) . " returned no results";
}
?>

