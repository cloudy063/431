<?php
// Database connection
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get all lists
function getLists() {
    global $conn;
    $sql = "SELECT * FROM lists ORDER BY created ASC";
    $result = $conn->query($sql);
    $lists = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $list = array(
                "idx" => $row["idx"],
                "name" => $row["name"],
                "created" => $row["created"],
                "items" => array()
            );
            $listItems = getListItems($row["idx"]);
            $list["items"] = $listItems;
            array_push($lists, $list);
        }
    }
    return $lists;
}

// Function to get list items
function getListItems($listIdx) {
    global $conn;
    $sql = "SELECT * FROM list_items WHERE list_idx = $listIdx ORDER BY checked DESC, created ASC";
    $result = $conn->query($sql);
    $items = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $item = array(
                "idx" => $row["idx"],
                "text" => $row["text"],
                "checked" => $row["checked"]
            );
            array_push($items, $item);
        }
    }
    return $items;
}

// Function to create a new list
function createList($name) {
    global $conn;
    $name = $conn->real_escape_string($name);
    $sql = "INSERT INTO lists (name, created) VALUES ('$name', NOW())";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Function to delete a list and its items
function deleteList($listIdx) {
    global $conn;
    $sql = "DELETE FROM lists WHERE idx = $listIdx";
    if ($conn->query($sql) === TRUE) {
        $sql = "DELETE FROM list_items WHERE list_idx = $listIdx";
        if ($conn->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Function to add an item to a list
function addItem($listIdx, $text) {
    global $conn;
    $text = $conn->real_escape_string($text);
    $sql = "INSERT INTO list_items (text, list_idx, created) VALUES ('$text', $listIdx, NOW())";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Function to toggle item status
function toggleItem($itemIdx) {
    global $conn;
    $sql = "UPDATE list_items SET checked = NOT checked WHERE idx = $itemIdx";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Main code
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create new list
    if ($_POST["action"] == "createList") {
        $name = $_POST["name"];
        echo createList($name);
    }
    // Delete list
    elseif ($_POST["action"] == "deleteList") {
        $listIdx = $_POST["listIdx"];
        echo deleteList($listIdx);
    }
    // Add item to list
    elseif ($_POST["action"] == "addItem") {
        $listIdx = $_POST["listIdx"];
        $text = $_POST["text"];
        echo addItem($listIdx, $text);
    }
    // Toggle item status
    elseif ($_POST["action"] == "toggleItem") {
        $itemIdx = $_POST["itemIdx"];
        echo toggleItem($itemIdx);
    }
}

$conn->close();
?>
