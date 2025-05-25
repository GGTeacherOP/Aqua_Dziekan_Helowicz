<?php
// admin/index.php
require_once __DIR__ . '/../config/init.php'; // Kluczowe dołączenie init.php

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$roleId = $_SESSION['user_role_id'] ?? null; // Pobierz z sesji, jeśli init.php to ustawia

// Jeśli rola nie jest w sesji, pobierz ją (fallback, init.php powinien to robić)
if ($roleId === null && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT role_id FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $userRow = $stmt->fetch();
        if ($userRow) {
            $roleId = (int)$userRow['role_id'];
            $_SESSION['user_role_id'] = $roleId; // Zapisz do sesji
        } else {
            session_destroy(); header("Location: " . BASE_URL . "login.php?error=user_not_found_admin"); exit;
        }
    } catch (\PDOException $e) { die("Krytyczny błąd: Nie można pobrać roli użytkownika (admin)."); }
}

// Podstawowe sprawdzenie dostępu do panelu admina
if ($roleId != 7 && $roleId != 6 && !user_has_permission($pdo, $userId, 'view_general_admin_dashboard')) {
    $_SESSION['flash_message'] = ['text' => "Nie masz uprawnień dostępu do panelu administratora.", 'type' => "error"];
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$canManageOpinions = user_has_permission($pdo, $userId, 'manage_opinions') || ($roleId == 7);
$canManageSalaries = user_has_permission($pdo, $userId, 'manage_salaries') || ($roleId == 7);
$canEditAndDeleteAll = ($roleId == 7);

$all_tables_from_db = [];
try {
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) $all_tables_from_db[] = $row[0];
} catch (\PDOException $e) { die("Krytyczny błąd: Nie można pobrać listy tabel."); }

$selectedTable = isset($_GET['table']) && in_array($_GET['table'], $all_tables_from_db) ? $_GET['table'] : null;

$tables_to_display = [];
$allowedTablesForAdminRole6 = ['roles', 'products', 'opinions', 'carts', 'users', 'categories', 'orders', 'orderitems', 'cartitems', 'permissions', 'rolepermissions', 'salaries'];

if ($roleId == 7) $tables_to_display = $all_tables_from_db;
elseif ($roleId == 6) $tables_to_display = array_intersect($all_tables_from_db, $allowedTablesForAdminRole6);
else {
    if (user_has_permission($pdo, $userId, 'manage_products_services')) $tables_to_display = array_merge($tables_to_display, array_intersect($all_tables_from_db, ['products', 'categories']));
    if (user_has_permission($pdo, $userId, 'manage_opinions')) $tables_to_display = array_merge($tables_to_display, array_intersect($all_tables_from_db, ['opinions']));
    if (user_has_permission($pdo, $userId, 'manage_salaries')) $tables_to_display = array_merge($tables_to_display, array_intersect($all_tables_from_db, ['salaries']));
}
$tables_to_display = array_unique($tables_to_display); $tables_to_display = array_values($tables_to_display);

if (empty($selectedTable) && count($tables_to_display) > 0) $selectedTable = $tables_to_display[0];
elseif ($selectedTable && !in_array($selectedTable, $tables_to_display)) $selectedTable = count($tables_to_display) > 0 ? $tables_to_display[0] : null;

// --- Obsługa DELETE i UPDATE (logika skrócona dla czytelności, pełna w poprzednich odpowiedziach) ---
// Upewnij się, że ta logika jest poprawna i bezpieczna
if (isset($_POST['delete'], $_POST['table'], $_POST['pk'], $_POST['pk_val']) && in_array($_POST['table'], $tables_to_display)) {
    // ... (logika usuwania z poprzednich odpowiedzi, upewnij się, że $canEditAndDeleteAll jest sprawdzane)
    // Przykładowo:
    if ($canEditAndDeleteAll) { /* ... kod usuwania ... */ } else { /* błąd uprawnień */ }
    // header("Location: ?table=" . urlencode($_POST['table'])); exit();
}
if (isset($_POST['edit_submit'], $_POST['table'], $_POST['pk'], $_POST['pk_val']) && in_array($_POST['table'], $tables_to_display) && $_POST['table'] !== 'opinions') {
    // ... (logika aktualizacji z poprzednich odpowiedzi, sprawdzanie $canEditAndDeleteAll lub specyficznych uprawnień)
    // Przykładowo:
    if ($canEditAndDeleteAll || ($_POST['table'] === 'salaries' && $canManageSalaries) ) { /* ... kod aktualizacji ... */ } else { /* błąd uprawnień */ }
    // header("Location: ?table=" . urlencode($_POST['table'])); exit();
}
// --- Koniec obsługi DELETE i UPDATE ---


$editRowData = null; $pkColumnName = null;
if ($selectedTable) {
    try {
        $pkResult = $pdo->query("SHOW KEYS FROM `$selectedTable` WHERE Key_name = 'PRIMARY'");
        if ($pkRow = $pkResult->fetch()) $pkColumnName = $pkRow['Column_name'];
        else { $colResultForPk = $pdo->query("SHOW COLUMNS FROM `$selectedTable`"); $firstCol = $colResultForPk->fetch(); $pkColumnName = $firstCol ? $firstCol['Field'] : null; }
    } catch (\PDOException $e) { error_log("Admin Panel Error fetching PK for $selectedTable: " . $e->getMessage());}
}

if (isset($_GET['edit'], $_GET['table'], $_GET['pk_val']) && $pkColumnName && $_GET['table'] === $selectedTable && $selectedTable !== 'opinions') {
    $editTableGet = $_GET['table']; $editPkValGet = $_GET['pk_val'];
    $canDisplayEditForm = $canEditAndDeleteAll;
    if ($editTableGet === 'salaries' && $canManageSalaries) $canDisplayEditForm = true;

    if ($canDisplayEditForm) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM `$editTableGet` WHERE `$pkColumnName` = ? LIMIT 1");
            $stmt->execute([$editPkValGet]);
            $editRowData = $stmt->fetch();
        } catch (\PDOException $e) { $editRowData = null; }
    }
}

$filterOpinionStatus = 'Oczekująca';
if ($selectedTable === 'opinions' && isset($_GET['opinion_status_filter']) && in_array($_GET['opinion_status_filter'], ['Oczekująca', 'Zaakceptowana', 'Odrzucona', 'all'])) {
    $filterOpinionStatus = $_GET['opinion_status_filter'];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - AquaParadise</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 0; color: #333; }
        .navbar { background: #005f73; padding: 0 20px; height: 60px; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex-wrap: wrap; }
        .navbar a { color: #fff; text-decoration: none; margin-right: 15px; padding: 8px 12px; border-radius: 4px; transition: background-color 0.2s ease-in-out; font-size: 0.9em; }
        .navbar a.active, .navbar a:hover { background-color: #00778f; }
        .navbar .nav-right { margin-left: auto; display: flex; align-items: center;}
        .navbar .nav-right a { background-color: #ca6702; margin-left:15px; }
        .navbar .nav-right a:hover { background-color: #ae2012; }
        .container { max-width: 95%; margin: 25px auto; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24); padding: 25px 30px; }
        h1, h2 { color: #005f73; margin-top: 0; }
        h1 { font-size: 1.8em; margin-bottom: 20px; border-bottom: 2px solid #005f73; padding-bottom:10px;}
        h2 { font-size: 1.4em; margin-bottom: 15px; }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; font-size:0.9em; table-layout: auto; }
        th, td { padding: 10px 12px; text-align: left; border: 1px solid #dee2e6; vertical-align: top; white-space: normal; word-wrap: break-word; }
        th { background-color: #e9ecef; color: #495057; font-weight: 600; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #e9ecef; }
        th.col-short, td.col-short { width: 80px; min-width:60px; max-width: 100px; }
        th.col-medium, td.col-medium { width: 180px; min-width:120px; max-width: 250px; }
        th.col-long-text, td.col-long-text { width: auto; min-width:200px; max-width: 400px;}
        th.col-actions, td.col-actions { width: 280px; min-width:250px; text-align: center;}
        .action-btn { padding: 6px 10px; border-radius: 4px; border: none; cursor: pointer; font-size: 0.85em; margin-right: 5px; text-decoration:none; display:inline-block; color: white !important; line-height: 1.5; margin-bottom: 3px;}
        .delete-btn { background-color: #d9534f; } .delete-btn:hover { background-color: #c9302c; }
        .edit-btn { background-color: #5cb85c; } .edit-btn:hover { background-color: #4cae4c; }
        .approve-btn { background-color: #5cb85c; } .approve-btn:hover { background-color: #4cae4c; }
        .reject-btn { background-color: #f0ad4e; } .reject-btn:hover { background-color: #ec971f; }
        .cancel-btn { background-color: #777; } .cancel-btn:hover { background-color: #555; }
        .save-btn { background-color: #0275d8; } .save-btn:hover { background-color: #025aa5; }
        form { margin-bottom: 20px; padding:15px; background-color:#f8f9fa; border:1px solid #dee2e6; border-radius:5px;}
        form table {margin-top:0;}
        form input[type="text"], form input[type="number"], form input[type="date"], form textarea, form select { width: 98%; padding: 8px; margin: 2px 0 8px 0; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; font-size:0.95em; }
        form textarea { min-height: 60px; resize: vertical;}
        .opinion-action-form textarea { margin-top: 5px; width: calc(100% - 16px); }
        .opinion-action-form button { margin-top: 5px; }
        .status-message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .status-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .opinion-filters { margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; }
        .opinion-filters label { margin-right: 5px; font-weight: bold;}
        .opinion-filters select, .opinion-filters button { padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;}
        .opinion-filters button { background-color: #007bff; color: white; cursor: pointer; }
        .opinion-filters button:hover { background-color: #0056b3; }
        @media (max-width: 768px) {
            .navbar { height: auto; padding: 10px; }
            .navbar a { display: block; margin: 5px 0; width:calc(100% - 24px); }
            .navbar .nav-right { margin-left:0; width:100%; justify-content: space-between; margin-top:10px;}
            .container { padding: 15px; }
            h1 { font-size: 1.5em; }
            h2 { font-size: 1.2em; }
            th, td { padding: 8px; font-size:0.85em; }
            th.col-short, td.col-short { width: 60px; max-width: 80px; }
            th.col-medium, td.col-medium { width: 120px; max-width: 180px; }
            th.col-actions, td.col-actions { width: 120px;}
            .action-btn { font-size: 0.8em; padding: 4px 6px; display: block; margin-bottom: 5px; text-align:center;}
            form input[type="text"], form input[type="number"], form input[type="date"], form textarea, form select { width: 95%;}
            .opinion-filters select, .opinion-filters button { display: block; width: 100%; margin-bottom: 10px;}
        }
    </style>
</head>
<body>
    <div class="navbar">
        <?php foreach ($tables_to_display as $tableLink): ?>
            <a href="?table=<?php echo urlencode($tableLink); ?>"<?php if ($selectedTable === $tableLink) echo ' class="active"'; ?>>
                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $tableLink))); ?>
            </a>
        <?php endforeach; ?>
        <div class="nav-right">
            <a href="<?php echo BASE_URL; ?>index.php" title="Przejdź do strony głównej AquaParadise"><i class="fas fa-home"></i> Strona główna</a>
            <a href="<?php echo BASE_URL; ?>logout.php" title="Wyloguj się z panelu"><i class="fas fa-sign-out-alt"></i> Wyloguj</a>
        </div>
    </div>

    <div class="container">
        <h1>Panel Administratora AquaParadise</h1>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <p class="status-message <?php echo htmlspecialchars($_SESSION['flash_message']['type'] ?? 'info') === 'error' ? 'status-error' : 'status-success'; ?>">
                <?php echo htmlspecialchars($_SESSION['flash_message']['text'] ?? $_SESSION['flash_message']); ?>
            </p>
            <?php unset($_SESSION['flash_message']); unset($_SESSION['flash_message_type']); // Usuń też stary typ, jeśli istnieje ?>
        <?php endif; ?>


        <?php if (!$selectedTable && count($tables_to_display) == 0): ?>
            <p>Nie masz dostępu do żadnych tabel lub brak tabel w bazie.</p>
        <?php elseif (!$selectedTable && count($tables_to_display) > 0): ?>
            <p>Wybierz tabelę z menu powyżej, aby wyświetlić dane.</p>
        <?php endif; ?>

        <?php if ($selectedTable): ?>
            <h2>Tabela: <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $selectedTable))); ?></h2>

            <?php if ($selectedTable === 'opinions' && $canManageOpinions): ?>
                <div class="opinion-filters">
                    <form method="GET" action="">
                        <input type="hidden" name="table" value="opinions">
                        <label for="opinion_status_filter">Filtruj status:</label>
                        <select name="opinion_status_filter" id="opinion_status_filter" onchange="this.form.submit()">
                            <option value="Oczekująca" <?php if ($filterOpinionStatus === 'Oczekująca') echo 'selected'; ?>>Oczekujące</option>
                            <option value="Zaakceptowana" <?php if ($filterOpinionStatus === 'Zaakceptowana') echo 'selected'; ?>>Zaakceptowane</option>
                            <option value="Odrzucona" <?php if ($filterOpinionStatus === 'Odrzucona') echo 'selected'; ?>>Odrzucone</option>
                            <option value="all" <?php if ($filterOpinionStatus === 'all') echo 'selected'; ?>>Wszystkie</option>
                        </select>
                    </form>
                </div>
            <?php endif; ?>

            <?php
            $canDisplayEditFormForCurrentTable = $canEditAndDeleteAll;
            if ($selectedTable === 'salaries' && $canManageSalaries) $canDisplayEditFormForCurrentTable = true;

            if ($editRowData && $canDisplayEditFormForCurrentTable && $pkColumnName && $selectedTable !== 'opinions'):
            ?>
                <h3>Edytowanie rekordu (ID: <?php echo htmlspecialchars(is_array($editRowData[$pkColumnName] ?? null) ? json_encode($editRowData[$pkColumnName]) : (string)($editRowData[$pkColumnName] ?? '')); ?>) w tabeli <?php echo htmlspecialchars($selectedTable); ?></h3>
                <form method="post" action="index.php?table=<?php echo urlencode($selectedTable); ?>">
                    <input type="hidden" name="edit_submit" value="1">
                    <input type="hidden" name="table" value="<?php echo htmlspecialchars($selectedTable); ?>">
                    <input type="hidden" name="pk" value="<?php echo htmlspecialchars($pkColumnName); ?>">
                    <input type="hidden" name="pk_val" value="<?php echo htmlspecialchars(is_array($editRowData[$pkColumnName] ?? null) ? json_encode($editRowData[$pkColumnName]) : (string)($editRowData[$pkColumnName] ?? '')); ?>">
                    <table>
                        <?php
                        $column_types = [];
                        if ($selectedTable) {
                            $stmt_cols = $pdo->query("SHOW COLUMNS FROM `$selectedTable`");
                            while ($col_info = $stmt_cols->fetch()) $column_types[$col_info['Field']] = $col_info['Type'];
                        }

                        foreach ($editRowData as $col => $val):
                        ?>
                            <tr>
                                <th style="width:20%;"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $col))); ?></th>
                                <td>
                                    <?php
                                    if ($col === $pkColumnName) {
                                        $pk_display_val = is_array($val) ? (count($val) > 0 ? (string)reset($val) : '') : (string)$val;
                                        if (is_array($val)) error_log("Admin Panel Edit Form (PK): Column '$col' for table '$selectedTable' is an array: " . print_r($val, true));
                                        echo htmlspecialchars($pk_display_val);
                                    } else {
                                        $field_display_val = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string)$val;
                                        $col_type = $column_types[$col] ?? '';

                                        if (strpos(strtolower($col_type), 'text') !== false || (is_string($field_display_val) && strlen($field_display_val) > 70) ): ?>
                                            <textarea name="fields[<?php echo htmlspecialchars($col); ?>]" rows="3"><?php echo htmlspecialchars($field_display_val); ?></textarea>
                                        <?php elseif (strpos(strtolower($col_type), 'date') !== false): ?>
                                            <input type="date" name="fields[<?php echo htmlspecialchars($col); ?>]" value="<?php echo htmlspecialchars($field_display_val); ?>">
                                        <?php elseif (strpos(strtolower($col_type), 'enum') !== false && $selectedTable === 'salaries' && $col === 'pay_frequency'): ?>
                                            <select name="fields[<?php echo htmlspecialchars($col); ?>]">
                                                <option value="miesięcznie" <?php if ($field_display_val === 'miesięcznie') echo 'selected'; ?>>Miesięcznie</option>
                                                <option value="godzinowo" <?php if ($field_display_val === 'godzinowo') echo 'selected'; ?>>Godzinowo</option>
                                                <option value="projektowo" <?php if ($field_display_val === 'projektowo') echo 'selected'; ?>>Projektowo</option>
                                                <option value="jednorazowo" <?php if ($field_display_val === 'jednorazowo') echo 'selected'; ?>>Jednorazowo</option>
                                            </select>
                                        <?php elseif (strpos(strtolower($col_type), 'decimal') !== false || strpos(strtolower($col_type), 'int') !== false): ?>
                                             <input type="number" name="fields[<?php echo htmlspecialchars($col); ?>]" value="<?php echo htmlspecialchars($field_display_val); ?>" <?php if(strpos(strtolower($col_type), 'decimal') !== false) echo 'step="0.01"'; ?> >
                                        <?php else: ?>
                                            <input type="text" name="fields[<?php echo htmlspecialchars($col); ?>]" value="<?php echo htmlspecialchars($field_display_val); ?>">
                                        <?php endif; ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <button type="submit" class="action-btn save-btn"><i class="fas fa-save"></i> Zapisz zmiany</button>
                    <a href="?table=<?php echo urlencode($selectedTable); ?>" class="action-btn cancel-btn"><i class="fas fa-times"></i> Anuluj</a>
                </form>
            <?php endif; ?>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <?php
                            $columns = [];
                            if ($selectedTable) {
                                try {
                                    $colResult = $pdo->query("SHOW COLUMNS FROM `$selectedTable`");
                                    while ($colData = $colResult->fetch()) $columns[] = $colData['Field'];
                                } catch (\PDOException $e) { error_log("Admin Panel Error fetching columns for $selectedTable: " . $e->getMessage()); }
                            }
                            foreach ($columns as $colName):
                                $colNameLower = strtolower($colName); $thClass = 'col-medium';
                                if (in_array($colNameLower, ['opinion_id', 'user_id', 'product_id', 'salary_id', 'rating', 'status', 'role_id', 'permission_id', 'cart_item_id', 'order_item_id', 'quantity', 'is_active', 'is_vip_customer', 'category_id']) || preg_match('/_id$/', $colNameLower) || $colNameLower === 'id') $thClass = 'col-short';
                                else if (in_array($colNameLower, ['comment', 'admin_comment', 'description', 'item_details', 'availability_details', 'notes', 'password_hash', 'image_url', 'guest_email', 'guest_name', 'payment_transaction_id', 'promo_code_used', 'contract_type'])) $thClass = 'col-long-text';
                                echo '<th class="' . $thClass . '">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $colName))) . '</th>';
                            endforeach;
                            if ($selectedTable) echo '<th class="col-actions">Akcje</th>';
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($selectedTable) {
                            try {
                                $queryStr = "SELECT * FROM `$selectedTable`"; $queryParams = [];
                                if ($selectedTable === 'opinions' && $filterOpinionStatus !== 'all') { $queryStr .= " WHERE `status` = :status_filter"; $queryParams[':status_filter'] = $filterOpinionStatus; }
                                $queryStr .= " ORDER BY ";
                                if ($selectedTable === 'opinions') $queryStr .= "`created_at` DESC";
                                elseif ($selectedTable === 'salaries') $queryStr .= "`effective_date` DESC, `user_id` ASC";
                                elseif ($pkColumnName) $queryStr .= "`$pkColumnName` DESC";
                                else $queryStr .= ($columns[0] ?? '1') . " ASC";
                                $queryStr .= " LIMIT 100";
                                
                                $rowStmt = $pdo->prepare($queryStr); $rowStmt->execute($queryParams);
                                while ($row = $rowStmt->fetch()):
                                ?>
                                <tr>
                                    <?php foreach ($columns as $col): 
                                        $colNameLower = strtolower($col); $tdClass = 'col-medium';
                                        if (in_array($colNameLower, ['opinion_id', 'user_id', 'product_id', 'salary_id', 'rating', 'status', 'role_id', 'permission_id', 'cart_item_id', 'order_item_id', 'quantity', 'is_active', 'is_vip_customer', 'category_id']) || preg_match('/_id$/', $colNameLower) || $colNameLower === 'id') $tdClass = 'col-short';
                                        else if (in_array($colNameLower, ['comment', 'admin_comment', 'description', 'item_details', 'availability_details', 'notes', 'password_hash', 'image_url', 'guest_email', 'guest_name', 'payment_transaction_id', 'promo_code_used', 'contract_type'])) $tdClass = 'col-long-text';
                                    ?>
                                        <td class="<?php echo $tdClass; ?>">
                                            <?php
                                            $cellValue = $row[$col] ?? null;
                                            $finalOutputForCell = '';

                                            if (is_array($cellValue)) {
                                                $jsonEncodedCell = json_encode($cellValue, JSON_UNESCAPED_UNICODE);
                                                $finalOutputForCell = $jsonEncodedCell !== false ? $jsonEncodedCell : '[Błąd kodowania tablicy]';
                                            } elseif (is_object($cellValue)) {
                                                $jsonEncodedCell = json_encode($cellValue, JSON_UNESCAPED_UNICODE);
                                                if ($jsonEncodedCell !== false) {
                                                    $finalOutputForCell = $jsonEncodedCell;
                                                } elseif (method_exists($cellValue, '__toString')) {
                                                    $finalOutputForCell = (string)$cellValue;
                                                } else {
                                                    $finalOutputForCell = '[Obiekt]';
                                                }
                                            } elseif (is_null($cellValue)) {
                                                $finalOutputForCell = '';
                                            } else {
                                                $finalOutputForCell = (string)$cellValue;
                                            }
                                            
                                            $isLongTextCol = in_array(strtolower($col), ['comment', 'admin_comment', 'description', 'item_details', 'availability_details', 'notes']);
                                            if ($isLongTextCol && function_exists('mb_strlen') && mb_strlen($finalOutputForCell, 'UTF-8') > 100) {
                                                echo nl2br(htmlspecialchars(mb_substr($finalOutputForCell, 0, 100, 'UTF-8'))) . '...';
                                            } else {
                                                echo nl2br(htmlspecialchars($finalOutputForCell));
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="col-actions">
                                        <?php if ($selectedTable === 'opinions' && $canManageOpinions && isset($row['opinion_id'])): ?>
                                            <form action="process_opinion.php" method="POST" class="opinion-action-form" style="margin-bottom: 5px;">
                                                <input type="hidden" name="opinion_id" value="<?php echo $row['opinion_id']; ?>">
                                                <input type="hidden" name="current_filter_status" value="<?php echo htmlspecialchars($filterOpinionStatus); ?>">
                                                <textarea name="admin_comment" placeholder="Komentarz Administratora..."><?php echo htmlspecialchars((string)($row['admin_comment'] ?? '')); ?></textarea>
                                                <?php if ($row['status'] === 'Oczekująca'): ?>
                                                    <button type="submit" name="action" value="approve" class="action-btn approve-btn"><i class="fas fa-check"></i> Akceptuj</button>
                                                    <button type="submit" name="action" value="reject" class="action-btn reject-btn"><i class="fas fa-times"></i> Odrzuć</button>
                                                <?php elseif ($row['status'] === 'Zaakceptowana'): ?>
                                                    <button type="submit" name="action" value="update_comment" class="action-btn save-btn"><i class="fas fa-comment-dots"></i> Zakt. Komentarz</button>
                                                    <button type="submit" name="action" value="reject" class="action-btn reject-btn" onclick="return confirm('Czy na pewno chcesz odrzucić tę zaakceptowaną opinię?');"><i class="fas fa-times"></i> Odrzuć</button>
                                                <?php elseif ($row['status'] === 'Odrzucona'): ?>
                                                     <button type="submit" name="action" value="approve" class="action-btn approve-btn" onclick="return confirm('Czy na pewno chcesz zaakceptować tę odrzuconą opinię?');"><i class="fas fa-check"></i> Akceptuj</button>
                                                <?php endif; ?>
                                                <?php if ($canEditAndDeleteAll): ?>
                                                    <button type="submit" name="action" value="delete_opinion" class="action-btn delete-btn" onclick="return confirm('Czy na pewno chcesz trwale USUNĄĆ tę opinię? Tej akcji nie można cofnąć.');"><i class="fas fa-trash"></i> Usuń</button>
                                                <?php endif; ?>
                                            </form>
                                        <?php elseif ( ($canEditAndDeleteAll || ($selectedTable === 'salaries' && $canManageSalaries) ) && $pkColumnName && isset($row[$pkColumnName])): ?>
                                            <a href="?table=<?php echo urlencode($selectedTable); ?>&edit=1&pk_val=<?php echo urlencode((string)$row[$pkColumnName]); ?>&pk_col=<?php echo urlencode($pkColumnName); ?>" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edytuj</a>
                                            <?php if ($canEditAndDeleteAll): ?>
                                            <form method="post" style="display:inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć ten rekord?');">
                                                <input type="hidden" name="delete" value="1">
                                                <input type="hidden" name="table" value="<?php echo htmlspecialchars($selectedTable); ?>">
                                                <input type="hidden" name="pk" value="<?php echo htmlspecialchars($pkColumnName); ?>">
                                                <input type="hidden" name="pk_val" value="<?php echo htmlspecialchars((string)$row[$pkColumnName]); ?>">
                                                <button type="submit" class="action-btn delete-btn"><i class="fas fa-trash"></i> Usuń</button>
                                            </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Brak dostępnych akcji
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                                endwhile;
                            } catch (\PDOException $e) {
                                echo "<tr><td colspan=\"" . (count($columns) + 1) . "\">Błąd ładowania danych dla tabeli $selectedTable: " . $e->getMessage() . "</td></tr>";
                                error_log("Admin Panel Error fetching data for $selectedTable: " . $e->getMessage());
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
             <?php if (count($tables_to_display) > 0): ?>
                <p>Wybierz tabelę z menu, aby wyświetlić jej zawartość.</p>
             <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>