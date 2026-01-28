<?php
/**
 * Script untuk membaca database dan generate model Laravel
 */

$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "simprom";

$conn = new mysqli($servername, $username, $password, $database, 3306);

if ($conn->connect_error) {
    die("❌ Koneksi gagal: " . $conn->connect_error);
}

echo "✅ Terhubung ke database 'simprom'\n\n";

// Ambil semua tabel
$result = $conn->query("SHOW TABLES");
$tables = [];

while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

echo "📊 TABEL YANG DITEMUKAN: " . count($tables) . "\n";
echo "=====================================\n\n";

// Simpan informasi untuk generate model
$modelData = [];

foreach ($tables as $table) {
    echo "📋 TABEL: {$table}\n";
    echo str_repeat("-", 80) . "\n";
    
    // Ambil struktur tabel
    $structureResult = $conn->query("DESCRIBE {$table}");
    $columns = [];
    $primaryKey = null;
    
    echo "Kolom:\n";
    while ($column = $structureResult->fetch_assoc()) {
        $columns[] = $column;
        $isPrimary = $column['Key'] === 'PRI' ? '🔑' : '  ';
        echo "{$isPrimary} {$column['Field']} | {$column['Type']} | " . 
             ($column['Null'] == 'NO' ? 'NOT NULL' : 'NULLABLE') . 
             ($column['Default'] ? " | Default: {$column['Default']}" : "") . "\n";
        
        if ($column['Key'] === 'PRI') {
            $primaryKey = $column['Field'];
        }
    }
    
    // Ambil contoh data
    $dataResult = $conn->query("SELECT * FROM {$table} LIMIT 3");
    $rowCount = $conn->query("SELECT COUNT(*) as count FROM {$table}")->fetch_assoc()['count'];
    
    echo "\nData (Total: {$rowCount} baris, Menampilkan max 3):\n";
    
    if ($dataResult->num_rows === 0) {
        echo "  (Tabel kosong)\n";
    } else {
        while ($row = $dataResult->fetch_assoc()) {
            echo "  " . json_encode($row) . "\n";
        }
    }
    
    echo "\n";
    
    // Simpan data untuk model generation
    $modelData[$table] = [
        'columns' => $columns,
        'primaryKey' => $primaryKey,
        'rowCount' => $rowCount
    ];
}

$conn->close();

// Generate model code
echo "\n\n";
echo "🔧 GENERATING MODEL LARAVEL\n";
echo "============================\n\n";

foreach ($modelData as $tableName => $data) {
    $modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName)));
    
    // Generate fillable properties
    $fillable = [];
    foreach ($data['columns'] as $column) {
        if ($column['Field'] !== $data['primaryKey'] && 
            !in_array($column['Field'], ['created_at', 'updated_at', 'deleted_at'])) {
            $fillable[] = $column['Field'];
        }
    }
    
    // Generate casts
    $casts = [];
    foreach ($data['columns'] as $column) {
        if ($column['Field'] === 'created_at' || $column['Field'] === 'updated_at' || $column['Field'] === 'deleted_at') {
            $casts[$column['Field']] = 'datetime';
        } elseif (strpos($column['Type'], 'int') !== false) {
            $casts[$column['Field']] = 'integer';
        } elseif (strpos($column['Type'], 'boolean') !== false || strpos($column['Type'], 'bool') !== false) {
            $casts[$column['Field']] = 'boolean';
        } elseif (strpos($column['Type'], 'decimal') !== false || strpos($column['Type'], 'float') !== false) {
            $casts[$column['Field']] = 'float';
        } elseif (strpos($column['Type'], 'json') !== false) {
            $casts[$column['Field']] = 'array';
        }
    }
    
    $fillableStr = "'" . implode("', '", $fillable) . "'";
    
    $castsStr = '';
    if (!empty($casts)) {
        $castLines = [];
        foreach ($casts as $field => $type) {
            $castLines[] = "            '$field' => '$type',";
        }
        $castsStr = "\n\n    /**\n     * Get the attributes that should be cast.\n     *\n     * @return array<string, string>\n     */\n    protected function casts(): array\n    {\n        return [\n" . implode("\n", $castLines) . "\n        ];\n    }";
    }
    
    echo "✅ Model: {$modelName}\n";
    echo "   Tabel: {$tableName}\n";
    echo "   Primary Key: {$data['primaryKey']}\n";
    echo "   Kolom yang bisa diisi: " . count($fillable) . "\n";
    echo "   Total baris: {$data['rowCount']}\n\n";
}
?>
