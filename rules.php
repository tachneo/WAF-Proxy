<?php
include('includes/header.php');
include('includes/db.php');
include('includes/functions.php');

// Fetch WAF rules from the database
$wafRules = getWAFRules($conn);

// Handle adding a new rule
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rule_name = trim($_POST['rule_name']);
    $pattern = trim($_POST['pattern']);
    $action = $_POST['action'];

    // Validate input
    if (!empty($rule_name) && !empty($pattern)) {
        // Insert new rule into the database
        addWAFRule($conn, $rule_name, $pattern, $action);

        // Refresh the page after adding a rule
        header('Location: rules.php');
        exit;
    } else {
        $error_message = "Please fill in all required fields and ensure the pattern is valid.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage WAF Rules</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Custom CSS for making the table wider and more responsive */
        .table-container {
            width: 100%;
            overflow-x: auto; /* Allows horizontal scrolling for wide content */
        }

        table {
            width: 100%; /* Full width table */
            border-collapse: collapse; /* Remove gaps between table cells */
        }

        th, td {
            padding: 12px 20px;
            border: 1px solid #ccc; /* Add border to table cells */
            text-align: left;
            white-space: nowrap; /* Prevent text from wrapping */
        }

        th {
            background-color: #f4f4f4; /* Background color for table headers */
        }

        .form-group {
            margin-bottom: 20px;
        }

        .action-btn {
            background-color: #4CAF50; /* Green button */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .action-btn:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage WAF Rules</h1>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>

        <!-- Form to Add New WAF Rule -->
        <form method="POST" action="rules.php">
            <div class="form-group">
                <label for="rule_name">Rule Name</label>
                <input type="text" name="rule_name" id="rule_name" placeholder="Enter rule name (e.g., SQL Injection)" required>
            </div>
            <div class="form-group">
                <label for="pattern">Pattern (Regular Expression)</label>
                <input type="text" name="pattern" id="pattern" placeholder="Enter regex pattern (e.g., /(select|insert|delete)/i)" required>
            </div>
            <div class="form-group">
                <label for="action">Action</label>
                <select name="action" id="action">
                    <option value="block">Block</option>
                    <option value="log">Log</option>
                </select>
            </div>
            <button type="submit" class="action-btn">Add Rule</button>
        </form>

        <h2>Existing WAF Rules</h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Rule Name</th>
                        <th>Pattern</th>
                        <th>Action</th>
                        <th>Created At</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wafRules as $rule): ?>
                        <tr>
                            <td><?php echo $rule['id']; ?></td>
                            <td><?php echo htmlspecialchars($rule['rule_name']); ?></td>
                            <td><?php echo htmlspecialchars($rule['pattern']); ?></td>
                            <td><?php echo ucfirst($rule['action']); ?></td>
                            <td><?php echo $rule['created_at']; ?></td>
                            <td>
                                <a href="delete_rule.php?id=<?php echo $rule['id']; ?>" class="action-btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
