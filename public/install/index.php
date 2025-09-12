<?php
/**
 * Laravel Installer - WordPress Style
 * Simple installation system untuk kemudahan deployment
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if already installed
if (file_exists(__DIR__ . '/../../.env') && !file_exists(__DIR__ . '/../../storage/installer.lock')) {
    // Check if database connection works
    try {
        $env = parse_ini_file(__DIR__ . '/../../.env');
        if (isset($env['DB_CONNECTION']) && $env['DB_CONNECTION'] === 'sqlite') {
            $dbPath = __DIR__ . '/../../database/database.sqlite';
            if (file_exists($dbPath)) {
                $pdo = new PDO('sqlite:' . $dbPath);
                $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users' LIMIT 1");
                if ($result && $result->fetch()) {
                    header('Location: /');
                    exit('Installation sudah selesai. <a href="/">Akses aplikasi</a>');
                }
            }
        }
    } catch (Exception $e) {
        // Continue with installation if database check fails
    }
}

// Handle installation process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? 1;
    
    switch ($step) {
        case 2:
            handleEnvironmentSetup();
            break;
        case 3:
            handleDatabaseSetup();
            break;
        case 4:
            handleFinalSetup();
            break;
    }
}

function handleEnvironmentSetup() {
    $appName = $_POST['app_name'] ?? 'WSCRM';
    $appUrl = $_POST['app_url'] ?? 'http://localhost';
    $dbConnection = $_POST['db_connection'] ?? 'sqlite';
    
    $envTemplate = file_get_contents(__DIR__ . '/../../.env.example');
    
    $envContent = str_replace([
        'APP_NAME=Laravel',
        'APP_URL=http://localhost',
        'DB_CONNECTION=sqlite',
    ], [
        "APP_NAME=\"$appName\"",
        "APP_URL=$appUrl",
        "DB_CONNECTION=$dbConnection",
    ], $envTemplate);
    
    // Generate app key
    $appKey = 'base64:' . base64_encode(random_bytes(32));
    $envContent = str_replace('APP_KEY=', "APP_KEY=$appKey", $envContent);
    
    file_put_contents(__DIR__ . '/../../.env', $envContent);
    
    // Create SQLite database file
    if ($dbConnection === 'sqlite') {
        $dbPath = __DIR__ . '/../../database/database.sqlite';
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }
    }
    
    showStep(3, 'Environment berhasil dikonfigurasi');
}

function handleDatabaseSetup() {
    // Run migrations
    $output = [];
    $return = 0;
    
    chdir(__DIR__ . '/../..');
    exec('php artisan migrate --force 2>&1', $output, $return);
    
    if ($return !== 0) {
        showStep(3, 'Error saat migrasi database: ' . implode('\n', $output), true);
        return;
    }
    
    // Run seeders if exists
    exec('php artisan db:seed --force 2>&1', $output, $return);
    
    showStep(4, 'Database berhasil disetup');
}

function handleFinalSetup() {
    // Clear caches
    chdir(__DIR__ . '/../..');
    exec('php artisan config:clear 2>&1');
    exec('php artisan route:clear 2>&1');
    exec('php artisan view:clear 2>&1');
    
    // Create lock file
    file_put_contents(__DIR__ . '/../../storage/installer.lock', date('Y-m-d H:i:s'));
    
    // Create admin user if requested
    $adminEmail = $_POST['admin_email'] ?? '';
    $adminPassword = $_POST['admin_password'] ?? '';
    
    if ($adminEmail && $adminPassword) {
        // Create admin user via artisan command
        $command = "php artisan tinker --execute=\"
            \\App\\Models\\User::create([
                'name' => 'Administrator',
                'email' => '$adminEmail',
                'password' => bcrypt('$adminPassword'),
                'email_verified_at' => now(),
            ]);
        \"";
        exec($command . ' 2>&1');
    }
    
    showSuccess();
}

function showStep($step, $message = '', $error = false) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Instalasi WSCRM</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .logo { text-align: center; margin-bottom: 30px; }
            .logo h1 { color: #3b82f6; margin: 0; font-size: 2em; }
            .step-indicator { display: flex; justify-content: center; margin-bottom: 30px; }
            .step { width: 30px; height: 30px; border-radius: 50%; background: #e5e5e5; display: flex; align-items: center; justify-content: center; margin: 0 10px; color: #666; }
            .step.active { background: #3b82f6; color: white; }
            .step.completed { background: #10b981; color: white; }
            .form-group { margin-bottom: 20px; }
            .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
            .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 16px; }
            .btn { background: #3b82f6; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; }
            .btn:hover { background: #2563eb; }
            .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
            .message.success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
            .message.error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
            .requirements { background: #f9fafb; padding: 20px; border-radius: 4px; margin-bottom: 20px; }
            .req-item { display: flex; align-items: center; margin-bottom: 10px; }
            .req-ok { color: #10b981; margin-right: 10px; }
            .req-fail { color: #ef4444; margin-right: 10px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">
                <h1>🚀 WSCRM Installer</h1>
            </div>
            
            <div class="step-indicator">
                <div class="step <?php echo $step >= 1 ? ($step > 1 ? 'completed' : 'active') : ''; ?>">1</div>
                <div class="step <?php echo $step >= 2 ? ($step > 2 ? 'completed' : 'active') : ''; ?>">2</div>
                <div class="step <?php echo $step >= 3 ? ($step > 3 ? 'completed' : 'active') : ''; ?>">3</div>
                <div class="step <?php echo $step >= 4 ? 'active' : ''; ?>">4</div>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $error ? 'error' : 'success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php
            switch ($step) {
                case 1:
                    showRequirements();
                    break;
                case 2:
                    showEnvironmentForm();
                    break;
                case 3:
                    showDatabaseForm();
                    break;
                case 4:
                    showFinalForm();
                    break;
            }
            ?>
        </div>
    </body>
    </html>
    <?php
}

function showRequirements() {
    $requirements = [
        'PHP >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
        'Extension: PDO' => extension_loaded('pdo'),
        'Extension: SQLite' => extension_loaded('pdo_sqlite'),
        'Extension: OpenSSL' => extension_loaded('openssl'),
        'Extension: Mbstring' => extension_loaded('mbstring'),
        'Extension: Tokenizer' => extension_loaded('tokenizer'),
        'Extension: XML' => extension_loaded('xml'),
        'Extension: Ctype' => extension_loaded('ctype'),
        'Extension: JSON' => extension_loaded('json'),
        'Directory Writable: storage' => is_writable(__DIR__ . '/../../storage'),
        'Directory Writable: bootstrap/cache' => is_writable(__DIR__ . '/../../bootstrap/cache'),
    ];
    
    $allOk = true;
    foreach ($requirements as $req => $status) {
        if (!$status) $allOk = false;
    }
    ?>
    
    <h2>Pemeriksaan Sistem</h2>
    
    <div class="requirements">
        <?php foreach ($requirements as $req => $status): ?>
            <div class="req-item">
                <span class="<?php echo $status ? 'req-ok' : 'req-fail'; ?>">
                    <?php echo $status ? '✓' : '✗'; ?>
                </span>
                <?php echo $req; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($allOk): ?>
        <form method="POST">
            <input type="hidden" name="step" value="2">
            <button type="submit" class="btn">Lanjutkan Instalasi</button>
        </form>
    <?php else: ?>
        <p style="color: #ef4444;">Harap perbaiki error di atas sebelum melanjutkan instalasi.</p>
    <?php endif;
}

function showEnvironmentForm() {
    ?>
    <h2>Konfigurasi Aplikasi</h2>
    
    <form method="POST">
        <input type="hidden" name="step" value="2">
        
        <div class="form-group">
            <label for="app_name">Nama Aplikasi</label>
            <input type="text" id="app_name" name="app_name" value="WSCRM" required>
        </div>
        
        <div class="form-group">
            <label for="app_url">URL Aplikasi</label>
            <input type="url" id="app_url" name="app_url" value="<?php echo 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['REQUEST_URI'])); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="db_connection">Database</label>
            <select id="db_connection" name="db_connection">
                <option value="sqlite">SQLite (Direkomendasikan)</option>
                <option value="mysql">MySQL</option>
            </select>
        </div>
        
        <button type="submit" class="btn">Lanjutkan</button>
    </form>
    <?php
}

function showDatabaseForm() {
    ?>
    <h2>Setup Database</h2>
    
    <p>Aplikasi akan melakukan migrasi database dan setup data awal.</p>
    
    <form method="POST">
        <input type="hidden" name="step" value="3">
        <button type="submit" class="btn">Setup Database</button>
    </form>
    <?php
}

function showFinalForm() {
    ?>
    <h2>Finalisasi</h2>
    
    <p>Buat akun administrator untuk login pertama kali (opsional):</p>
    
    <form method="POST">
        <input type="hidden" name="step" value="4">
        
        <div class="form-group">
            <label for="admin_email">Email Admin (Opsional)</label>
            <input type="email" id="admin_email" name="admin_email" placeholder="admin@example.com">
        </div>
        
        <div class="form-group">
            <label for="admin_password">Password Admin (Opsional)</label>
            <input type="password" id="admin_password" name="admin_password" placeholder="Minimal 8 karakter">
        </div>
        
        <button type="submit" class="btn">Selesaikan Instalasi</button>
    </form>
    <?php
}

function showSuccess() {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Instalasi Berhasil - WSCRM</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
            .success-icon { font-size: 4em; color: #10b981; margin-bottom: 20px; }
            h1 { color: #065f46; margin-bottom: 20px; }
            .btn { background: #3b82f6; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; margin: 10px; }
            .btn:hover { background: #2563eb; }
            .btn-secondary { background: #6b7280; }
            .btn-secondary:hover { background: #4b5563; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="success-icon">🎉</div>
            <h1>Instalasi Berhasil!</h1>
            <p>WSCRM telah berhasil diinstall dan siap digunakan.</p>
            
            <p>
                <a href="/" class="btn">Akses Aplikasi</a>
                <a href="#" onclick="deleteInstaller()" class="btn btn-secondary">Hapus Installer</a>
            </p>
            
            <p><small>Untuk keamanan, disarankan untuk menghapus folder installer setelah instalasi selesai.</small></p>
        </div>
        
        <script>
        function deleteInstaller() {
            if (confirm('Hapus folder installer? Tindakan ini tidak dapat dibatalkan.')) {
                fetch('/install/cleanup.php', {method: 'POST'})
                .then(() => {
                    alert('Installer berhasil dihapus.');
                    window.location.href = '/';
                });
            }
        }
        </script>
    </body>
    </html>
    <?php
}

// Show initial step if no POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    showStep(1);
}
?>