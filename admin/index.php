<?php
session_start(); // 1. Start session

// Check if user is logged in, otherwise redirect
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // Change to your login page path
    exit;
}

// Database connection settings
$host = 'localhost';
$db   = 'aquaparadise_db';
$user = 'root';
$pass = '';

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user's role_id from database
$userId = $_SESSION['user_id'];
$roleId = null;

$stmt = $conn->prepare("SELECT role_id FROM users WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($roleId);
$stmt->fetch();
$stmt->close();

// (Access restriction logic moved below, after fetching $tables and $selectedTable)

// Database connection settings
$host = 'localhost';
$db   = 'aquaparadise_db';
$user = 'root';
$pass = '';

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all tables
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

// Get selected table from GET parameter
$selectedTable = isset($_GET['table']) && in_array($_GET['table'], $tables) ? $_GET['table'] : (count($tables) ? $tables[0] : null);

// Worker type filter
$workerType = isset($_GET['worker_type']) ? $_GET['worker_type'] : '';

// If workers table is selected, get worker types
$workerTypes = [];
if ($selectedTable === 'workers') {
    $typeResult = $conn->query("SELECT DISTINCT `type` FROM `workers`");
    while ($typeRow = $typeResult->fetch_assoc()) {
        $workerTypes[] = $typeRow['type'];
    }
}

// Restrict access based on role_id
if ($roleId != 7) {
    // If role_id is 6, show only 'users' table
    if ($roleId == 6) {
        $tables = array_intersect($tables, ['roles','products','opinions','carts',]);
        $tables = array_values($tables); // reindex array
        if (!in_array($selectedTable, $tables)) {
            $selectedTable = count($tables) ? $tables[0] : null;
        }
    } else {
        $tables = array_intersect($tables, ['roles','products','opinions','carts',]);
        $tables = array_values($tables); // reindex array
        if (!in_array($selectedTable, $tables)) {
            $selectedTable = count($tables) ? $tables[0] : null;
        }
    }
}
?>
    <title>Admin Panel - Workers</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #0074D9;
            padding: 0 0 0 20px;
            height: 56px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.07);
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-right: 18px;
            font-weight: 500;
            padding: 12px 10px;
            border-radius: 4px 4px 0 0;
            transition: background 0.2s;
        }
        .navbar a.active, .navbar a:hover {
            background: #005fa3;
        }
        .navbar span {
            margin-left: auto;
            font-size: 15px;
            display: flex;
            align-items: center;
        }
        .navbar span a {
            color: #fff;
            background: #ff4136;
            margin-left: 18px;
            padding: 7px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 400;
            transition: background 0.2s;
        }
        .navbar span a:hover {
            background: #c12b1e;
        }
        .container {
            max-width: 1100px;
            margin: 36px auto 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 32px 36px 36px 36px;
        }
        h1 {
            margin-top: 0;
            font-size: 2.1em;
            color: #0074D9;
            letter-spacing: 1px;
        }
        h2 {
            margin-top: 18px;
            color: #222;
            font-size: 1.3em;
        }
        .worker-types {
            margin-bottom: 18px;
        }
        .worker-types a {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 6px;
            padding: 6px 18px;
            background: #e6f2fb;
            color: #0074D9;
            border-radius: 16px;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.2s, color 0.2s;
        }
        .worker-types a.active, .worker-types a:hover {
            background: #0074D9;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            background: #fafbfc;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        th, td {
            padding: 10px 14px;
            text-align: left;
        }
        th {
            background: #e6f2fb;
            color: #0074D9;
            font-weight: 600;
            border-bottom: 2px solid #cce4f7;
        }
        tr:nth-child(even) td {
            background: #f4f8fb;
        }
        tr:hover td {
            background: #e6f2fb;
        }
        .action-btn {
            padding: 4px 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            margin-right: 4px;
        }
        .delete-btn {
            background: #ff4136;
            color: #fff;
        }
        .edit-btn {
            background: #2ecc40;
            color: #fff;
        }
        .action-btn:hover {
            opacity: 0.85;
        }
        @media (max-width: 800px) {
            .container {
                padding: 16px 6px;
            }
            table, th, td {
                font-size: 13px;
            }
            .navbar {
                flex-direction: column;
                height: auto;
                padding: 10px 0 0 0;
            }
            .navbar span {
                margin: 10px 0 0 0;
            }
        }
    </style>
</head>
<body>
    <?php
    // Handle delete
    if (
        isset($_POST['delete'], $_POST['table'], $_POST['pk'], $_POST['pk_val'])
        && in_array($_POST['table'], $tables)
    ) {
        $delTable = $_POST['table'];
        $delPk = $_POST['pk'];
        $delPkVal = $_POST['pk_val'];
        // Only allow deletion if user is admin (role_id == 7)
        if ($roleId == 7) {
            $stmt = $conn->prepare("DELETE FROM `$delTable` WHERE `$delPk` = ?");
            $stmt->bind_param('s', $delPkVal);
            $stmt->execute();
            $stmt->close();
            // Redirect to avoid resubmission
            header("Location: ?table=" . urlencode($delTable));
            exit;
        }
    }

    // Handle update
    if (
        isset($_POST['edit'], $_POST['table'], $_POST['pk'], $_POST['pk_val'])
        && in_array($_POST['table'], $tables)
        && isset($_POST['fields']) && is_array($_POST['fields'])
    ) {
        $editTable = $_POST['table'];
        $editPk = $_POST['pk'];
        $editPkVal = $_POST['pk_val'];
        $fields = $_POST['fields'];
        // Only allow update if user is admin (role_id == 7)
        if ($roleId == 7) {
            $set = [];
            $types = '';
            $values = [];
            foreach ($fields as $col => $val) {
                $set[] = "`$col` = ?";
                $types .= 's';
                $values[] = $val;
            }
            $types .= 's';
            $values[] = $editPkVal;
            $sql = "UPDATE `$editTable` SET " . implode(', ', $set) . " WHERE `$editPk` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$values);
            $stmt->execute();
            $stmt->close();
            header("Location: ?table=" . urlencode($editTable));
            exit;
        }
    }

    // For edit form
    $editRow = null;
    if (
        isset($_GET['edit'], $_GET['table'], $_GET['pk'], $_GET['pk_val'])
        && in_array($_GET['table'], $tables)
    ) {
        $editTable = $_GET['table'];
        $editPk = $_GET['pk'];
        $editPkVal = $_GET['pk_val'];
        $stmt = $conn->prepare("SELECT * FROM `$editTable` WHERE `$editPk` = ? LIMIT 1");
        $stmt->bind_param('s', $editPkVal);
        $stmt->execute();
        $result = $stmt->get_result();
        $editRow = $result->fetch_assoc();
        $stmt->close();
    }
    ?>
    <div class="navbar">
        <?php foreach ($tables as $table): ?>
            <a href="?table=<?php echo urlencode($table); ?>"<?php if ($selectedTable === $table) echo ' class="active"'; ?>>
                <?php echo htmlspecialchars($table); ?>
            </a>
        <?php endforeach; ?>
        <a href="../index.php" style="background: white; color: black;">&#8592; Powrót</a>
    </div>
    <div class="container">
        <?php if ($selectedTable): ?>
            <h2><?php echo htmlspecialchars($selectedTable); ?></h2>
            <?php if ($selectedTable === 'workers' && count($workerTypes)): ?>
                <div class="worker-types">
                    <a href="?table=workers"<?php if ($workerType == '') echo ' class="active"'; ?>>All</a>
                    <?php foreach ($workerTypes as $type): ?>
                        <a href="?table=workers&amp;worker_type=<?php echo urlencode($type); ?>"<?php if ($workerType == $type) echo ' class="active"'; ?>>
                            <?php echo htmlspecialchars($type); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($editRow && $roleId == 7): ?>
                <form method="post" style="margin-bottom:24px;">
                    <input type="hidden" name="edit" value="1">
                    <input type="hidden" name="table" value="<?php echo htmlspecialchars($editTable); ?>">
                    <input type="hidden" name="pk" value="<?php echo htmlspecialchars($editPk); ?>">
                    <input type="hidden" name="pk_val" value="<?php echo htmlspecialchars($editPkVal); ?>">
                    <table>
                        <tr>
                            <?php foreach (array_keys($editRow) as $col): ?>
                                <th><?php echo htmlspecialchars($col); ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <?php foreach ($editRow as $col => $val): ?>
                                <td>
                                    <?php if ($col === $editPk): ?>
                                        <?php echo htmlspecialchars($val); ?>
                                    <?php else: ?>
                                        <input type="text" name="fields[<?php echo htmlspecialchars($col); ?>]" value="<?php echo htmlspecialchars($val); ?>">
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </table>
                    <button type="submit" class="action-btn edit-btn">Save</button>
                    <a href="?table=<?php echo urlencode($editTable); ?>" class="action-btn" style="background:#888;color:#fff;">Cancel</a>
                </form>
            <?php endif; ?>

            <table>
                <tr>
                    <?php
                    // Get columns
                    $columns = [];
                    $colResult = $conn->query("SHOW COLUMNS FROM `$selectedTable`");
                    while ($col = $colResult->fetch_assoc()) {
                        $columns[] = $col['Field'];
                        echo '<th>' . htmlspecialchars($col['Field']) . '</th>';
                    }
                    // Find primary key
                    $pk = $columns[0];
                    $pkResult = $conn->query("SHOW KEYS FROM `$selectedTable` WHERE Key_name = 'PRIMARY'");
                    if ($pkRow = $pkResult->fetch_assoc()) {
                        $pk = $pkRow['Column_name'];
                    }
                    ?>
                    <?php if ($roleId == 7): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
                <?php
                // Get rows
                if ($selectedTable === 'workers' && $workerType !== '') {
                    $stmt = $conn->prepare("SELECT * FROM `workers` WHERE `type` = ? LIMIT 100");
                    $stmt->bind_param('s', $workerType);
                    $stmt->execute();
                    $rowResult = $stmt->get_result();
                } else {
                    $rowResult = $conn->query("SELECT * FROM `$selectedTable` LIMIT 100");
                }
                while ($row = $rowResult->fetch_assoc()):
                ?>
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <td><?php echo htmlspecialchars($row[$col]); ?></td>
                    <?php endforeach; ?>
                    <?php if ($roleId == 7): ?>
                        <td>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this row?');">
                                <input type="hidden" name="delete" value="1">
                                <input type="hidden" name="table" value="<?php echo htmlspecialchars($selectedTable); ?>">
                                <input type="hidden" name="pk" value="<?php echo htmlspecialchars($pk); ?>">
                                <input type="hidden" name="pk_val" value="<?php echo htmlspecialchars($row[$pk]); ?>">
                                <button type="submit" class="action-btn delete-btn">Delete</button>
                            </form>
                            <a href="?table=<?php echo urlencode($selectedTable); ?>&edit=1&pk=<?php echo urlencode($pk); ?>&pk_val=<?php echo urlencode($row[$pk]); ?>" class="action-btn edit-btn">Edit</a>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No tables found in the database.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$conn->close();
?>
