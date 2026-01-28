<?php
/**
 * Verifikasi Model vs Database
 */

$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "simprom";

$conn = new mysqli($servername, $username, $password, $database, 3306);

if ($conn->connect_error) {
    die("❌ Koneksi gagal: " . $conn->connect_error . "\n");
}

echo "🔍 VERIFIKASI MODEL vs DATABASE\n";
echo "================================\n\n";

// Ambil semua tabel
$result = $conn->query("SHOW TABLES");
$tables = [];

while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

$modelsDir = __DIR__ . '/app/Models';
$allMatches = true;

foreach ($tables as $table) {
    $modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
    $filePath = $modelsDir . '/' . $modelName . '.php';
    
    echo "📋 Tabel: {$table}\n";
    echo "   Model: {$modelName}.php\n";
    
    if (!file_exists($filePath)) {
        echo "   ❌ Model TIDAK DITEMUKAN\n";
        $allMatches = false;
    } else {
        echo "   ✅ Model DITEMUKAN\n";
        
        // Baca struktur database
        $structureResult = $conn->query("DESCRIBE {$table}");
        $dbColumns = [];
        $primaryKey = null;
        
        while ($column = $structureResult->fetch_assoc()) {
            $dbColumns[] = $column['Field'];
            if ($column['Key'] === 'PRI') {
                $primaryKey = $column['Field'];
            }
        }
        
        // Baca file model
        $modelCode = file_get_contents($filePath);
        
        // Verifikasi table name
        if (strpos($modelCode, "protected \$table = '{$table}';")) {
            echo "   ✅ Nama tabel benar\n";
        } else {
            echo "   ❌ Nama tabel SALAH\n";
            $allMatches = false;
        }
        
        // Verifikasi primary key
        if (strpos($modelCode, "protected \$primaryKey = '{$primaryKey}';")) {
            echo "   ✅ Primary key benar ({$primaryKey})\n";
        } else {
            echo "   ❌ Primary key SALAH\n";
            $allMatches = false;
        }
        
        // Hitung jumlah baris
        $rowCount = $conn->query("SELECT COUNT(*) as count FROM {$table}")->fetch_assoc()['count'];
        echo "   📊 Data: {$rowCount} baris, Kolom: " . count($dbColumns) . "\n";
        
        // Tampilkan contoh data
        $sampleData = $conn->query("SELECT * FROM {$table} LIMIT 1");
        if ($sampleData->num_rows > 0) {
            $row = $sampleData->fetch_assoc();
            echo "   📝 Sample data: " . json_encode($row) . "\n";
        }
    }
    
    echo "\n";
}

$conn->close();

echo "\n================================\n";
if ($allMatches) {
    echo "✅ SEMUA MODEL SESUAI DENGAN DATABASE!\n";
} else {
    echo "⚠️  ADA BEBERAPA MODEL YANG TIDAK SESUAI\n";
}
echo "================================\n";
?>
