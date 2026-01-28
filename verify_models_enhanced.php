<?php
/**
 * Verifikasi Model vs Database (Versi Perbaikan)
 * Memeriksa kesesuaian antara model Laravel dan struktur database
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
$issuesFound = [];

foreach ($tables as $table) {
    $modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
    $filePath = $modelsDir . '/' . $modelName . '.php';

    echo "📋 Tabel: {$table}\n";
    echo "   Model: {$modelName}.php\n";

    if (!file_exists($filePath)) {
        echo "   ❌ Model TIDAK DITEMUKAN\n";
        $allMatches = false;
        $issuesFound[] = "Model {$modelName} tidak ditemukan untuk tabel {$table}";
    } else {
        echo "   ✅ Model DITEMUKAN\n";

        // Baca struktur database
        $structureResult = $conn->query("DESCRIBE {$table}");
        $dbColumns = [];
        $primaryKey = null;

        while ($column = $structureResult->fetch_assoc()) {
            $dbColumns[] = $column;
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
            $issuesFound[] = "Nama tabel salah pada model {$modelName}, seharusnya '{$table}'";
        }

        // Verifikasi primary key
        if (strpos($modelCode, "protected \$primaryKey = '{$primaryKey}';")) {
            echo "   ✅ Primary key benar ({$primaryKey})\n";
        } else {
            echo "   ❌ Primary key SALAH\n";
            $allMatches = false;
            $issuesFound[] = "Primary key salah pada model {$modelName}, seharusnya '{$primaryKey}'";
        }

        // Verifikasi fillable
        $missingFillables = [];
        $extraFillables = [];
        
        // Ekstrak fillable dari model
        preg_match("/protected \\\$fillable = \[(.*?)\];/s", $modelCode, $matches);
        if (isset($matches[1])) {
            $fillableContent = trim($matches[1]);
            $modelFillables = [];
            
            // Ekstrak nama-nama kolom dari fillable array
            preg_match_all("/'(.*?)'/", $fillableContent, $fillableMatches);
            $modelFillables = $fillableMatches[1];
            
            // Bandingkan dengan kolom-kolom di database (kecuali primary key dan timestamp)
            $dbFillables = [];
            foreach ($dbColumns as $col) {
                if ($col['Field'] !== $primaryKey && 
                    !in_array($col['Field'], ['created_at', 'updated_at', 'deleted_at'])) {
                    $dbFillables[] = $col['Field'];
                }
            }
            
            // Cari fillable yang hilang
            foreach ($dbFillables as $dbCol) {
                if (!in_array($dbCol, $modelFillables)) {
                    $missingFillables[] = $dbCol;
                }
            }
            
            // Cari fillable yang tidak ada di database
            foreach ($modelFillables as $modelCol) {
                if (!in_array($modelCol, $dbFillables)) {
                    $extraFillables[] = $modelCol;
                }
            }
        }
        
        if (empty($missingFillables) && empty($extraFillables)) {
            echo "   ✅ Fillable properties benar\n";
        } else {
            echo "   ⚠️  Fillable properties TIDAK SESUAI\n";
            if (!empty($missingFillables)) {
                echo "      - Kolom yang hilang dari fillable: " . implode(', ', $missingFillables) . "\n";
                $issuesFound[] = "Kolom hilang dari fillable pada model {$modelName}: " . implode(', ', $missingFillables);
            }
            if (!empty($extraFillables)) {
                echo "      - Kolom tambahan di fillable: " . implode(', ', $extraFillables) . "\n";
                $issuesFound[] = "Kolom tambahan di fillable pada model {$modelName}: " . implode(', ', $extraFillables);
            }
            $allMatches = false;
        }

        // Verifikasi casts
        $dbTypes = [];
        foreach ($dbColumns as $col) {
            $dbTypes[$col['Field']] = $col['Type'];
        }
        
        $castIssues = [];
        preg_match("/protected function casts\(\): array \{(.*?)\}/s", $modelCode, $castMatches);
        if (isset($castMatches[1])) {
            $castsContent = trim($castMatches[1]);
            $modelCasts = [];
            
            // Ekstrak casting dari model
            preg_match_all("/'([^']*)' => '([^']*)'/", $castsContent, $castMatches);
            for ($i = 0; $i < count($castMatches[1]); $i++) {
                $modelCasts[$castMatches[1][$i]] = $castMatches[2][$i];
            }
            
            // Bandingkan dengan tipe data di database
            foreach ($modelCasts as $field => $castType) {
                if (isset($dbTypes[$field])) {
                    $dbType = strtolower($dbTypes[$field]);
                    
                    // Periksa apakah casting cocok dengan tipe database
                    $expectedCast = null;
                    if (strpos($dbType, 'int') !== false) {
                        $expectedCast = 'integer';
                    } elseif (strpos($dbType, 'decimal') !== false || strpos($dbType, 'float') !== false) {
                        $expectedCast = 'float';
                    } elseif (strpos($dbType, 'boolean') !== false || strpos($dbType, 'tinyint(1)') !== false) {
                        $expectedCast = 'boolean';
                    } elseif (strpos($dbType, 'json') !== false) {
                        $expectedCast = 'array';
                    } elseif ($field === 'created_at' || $field === 'updated_at' || $field === 'deleted_at') {
                        $expectedCast = 'datetime';
                    }
                    
                    if ($expectedCast && $expectedCast !== $castType) {
                        $castIssues[] = "Field {$field}: seharusnya '{$expectedCast}' tapi di-set ke '{$castType}'";
                    }
                }
            }
        }
        
        if (empty($castIssues)) {
            echo "   ✅ Cast properties benar\n";
        } else {
            echo "   ⚠️  Cast properties TIDAK SESUAI\n";
            foreach ($castIssues as $issue) {
                echo "      - {$issue}\n";
            }
            $issuesFound = array_merge($issuesFound, $castIssues);
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
    echo "\nDetail masalah yang ditemukan:\n";
    foreach ($issuesFound as $issue) {
        echo "- {$issue}\n";
    }
}
echo "================================\n";

// Analisis tambahan: Cek apakah ada model tanpa tabel yang sesuai
echo "\n🔍 ANALISIS TAMBAHAN\n";
echo "===================\n";

$modelFiles = scandir($modelsDir);
$existingTables = array_flip($tables);

foreach ($modelFiles as $file) {
    if ($file !== '.' && $file !== '..' && $file !== '.DS_Store' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $expectedTableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $fileName)); // PascalCase to snake_case
        
        if (!isset($existingTables[$expectedTableName]) && $fileName !== 'User') {
            echo "⚠️  Model {$fileName} tidak memiliki tabel database yang sesuai (seharusnya: {$expectedTableName})\n";
        }
    }
}
?>