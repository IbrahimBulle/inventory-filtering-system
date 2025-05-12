<?php
// Path to your JSON file
$file = 'inventory.json';

// Check if the file exists
if (!file_exists($file)) {
    die("File not found.");
}

// Read and decode the JSON data
$data = file_get_contents($file); // Get the content of the JSON file
$items = json_decode($data, true); // Decode JSON into an associative array

// Check if json_decode was successful
if ($items === null) {
    die("Error decoding JSON.");
}

// Get the search query from the URL
$query = strtolower(trim($_GET['query'] ?? ''));

// Filter items based on the query
$matches = [];
foreach ($items as $item => $prices) {
    $name = $prices['name'] ?? '';
    $code = $prices['code'] ?? '';

    // Combine all fields to search
    $searchable = strtolower($item . ' ' . $name . ' ' . $code);

    // Match words starting with the query
    if (preg_match('/\b' . preg_quote($query, '/') . '/i', $searchable)) {
        $item_name = htmlspecialchars($item);
        $item_code = htmlspecialchars($code);
        $item_display_name = htmlspecialchars($name);

        $matches[] = "
            <div class='match-item'>
                <div class='list-item'>
                    <div class='item-code'><b>Code:</b> $item_code</div>
                    <div class='item-name'><b>Item:</b> $item_display_name</div>
                    <div class='item-prices'>
                        <span><b>Selling Price:</b> KES " . number_format($prices['selling_price'], 2) . "</span>
                    </div>
                    <div class='item-prices'>
                        <span class='standard-cost' style='display: none;'><b>Standard Cost:</b> KES " . number_format($prices['standard_cost'], 2) . "</span>
                    </div>
                </div>
            </div>";
    }
}

// If no matches are found, display "No item found"
if (empty($matches)) {
    echo "<p class='no-results'>No item found.</p>";
} else {
    echo "<p>Search results:</p>";
    echo "<div class='matches-list'>";

    foreach ($matches as $match) {
        echo $match;
    }
    echo "</div>"; // Output matching items
}
?>
