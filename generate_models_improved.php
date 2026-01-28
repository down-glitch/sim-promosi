<?php
/**
 * Generator Model Laravel Otomatis dari Database (Versi Perbaikan)
 * Script ini membaca database dan langsung generate file model di app/Models/
 */

$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "simprom";

$conn = new mysqli($servername, $username, $password, $database, 3306);

if ($conn->connect_error) {
    die("❌ Koneksi gagal: " . $conn->connect_error . "\n");
}

echo "✅ Terhubung ke database 'simprom'\n\n";

// Ambil semua tabel
$result = $conn->query("SHOW TABLES");
$tables = [];

while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

echo "📊 Ditemukan " . count($tables) . " tabel\n";
echo "================================================\n\n";

$modelsDir = __DIR__ . '/app/Models';
if (!is_dir($modelsDir)) {
    mkdir($modelsDir, 0755, true);
}

$generatedCount = 0;

foreach ($tables as $table) {
    // Generate nama model (PascalCase)
    $modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));

    // Ambil struktur tabel
    $structureResult = $conn->query("DESCRIBE {$table}");
    $columns = [];
    $primaryKey = 'id';
    $timestamps = false;
    $softDeletes = false;

    while ($column = $structureResult->fetch_assoc()) {
        $columns[] = $column;

        if ($column['Key'] === 'PRI') {
            $primaryKey = $column['Field'];
        }

        if ($column['Field'] === 'created_at' || $column['Field'] === 'updated_at') {
            $timestamps = true;
        }

        if ($column['Field'] === 'deleted_at') {
            $softDeletes = true;
        }
    }

    // Generate fillable
    $fillable = [];
    foreach ($columns as $column) {
        if ($column['Field'] !== $primaryKey &&
            !in_array($column['Field'], ['created_at', 'updated_at', 'deleted_at'])) {
            $fillable[] = $column['Field'];
        }
    }

    // Generate casts
    $casts = [];
    foreach ($columns as $column) {
        $type = strtolower($column['Type']);

        if ($column['Field'] === 'created_at' || $column['Field'] === 'updated_at' || $column['Field'] === 'deleted_at') {
            $casts[$column['Field']] = 'datetime';
        } elseif (strpos($type, 'bigint') !== false) {
            $casts[$column['Field']] = 'integer';
        } elseif (strpos($type, 'int') !== false && strpos($type, 'unsigned') !== false) {
            $casts[$column['Field']] = 'integer';
        } elseif (strpos($type, 'int') !== false) {
            $casts[$column['Field']] = 'integer';
        } elseif (strpos($type, 'decimal') !== false || strpos($type, 'float') !== false || strpos($type, 'double') !== false) {
            $casts[$column['Field']] = 'float';
        } elseif (strpos($type, 'boolean') !== false || strpos($type, 'tinyint(1)') !== false) {
            $casts[$column['Field']] = 'boolean';
        } elseif (strpos($type, 'json') !== false) {
            $casts[$column['Field']] = 'array';
        } elseif (strpos($type, 'enum') !== false) {
            // Untuk enum, kita tetapkan sebagai string
            $casts[$column['Field']] = 'string';
        } elseif (strpos($type, 'set') !== false) {
            // Untuk set, kita tetapkan sebagai string
            $casts[$column['Field']] = 'string';
        }
    }

    // Buat file model
    $fillableStr = count($fillable) > 0 ? "[\n        '" . implode("',\n        '", $fillable) . "',\n    ]" : "[]";

    $castsCode = '';
    if (!empty($casts)) {
        $castLines = [];
        foreach ($casts as $field => $castType) {
            $castLines[] = "        '{$field}' => '{$castType}',";
        }
        $castsCode = "\n\n    /**\n     * The attributes that should be cast.\n     *\n     * @return array<string, string>\n     */\n    protected function casts(): array\n    {\n        return [\n" . implode("\n", $castLines) . "\n        ];\n    }";
    }

    $softDeletesUse = $softDeletes ? "use SoftDeletes;\n    " : "";
    $softDeletesNamespace = $softDeletes ? "use Illuminate\Database\Eloquent\SoftDeletes;\n" : "";

    // Tentukan apakah model harus menggunakan timestamps
    $timestampsProperty = $timestamps ? "" : "public \$timestamps = false;";

    $modelCode = "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
{$softDeletesNamespace}
class {$modelName} extends Model
{
    use HasFactory;
    {$softDeletesUse}

    protected \$table = '{$table}';
    protected \$primaryKey = '{$primaryKey}';
    " . ($primaryKey !== 'id' ? "protected \$keyType = 'string';\n    " : "") . "
    protected \$fillable = {$fillableStr};
    
    {$timestampsProperty}{$castsCode}
}
";

    $filePath = $modelsDir . '/' . $modelName . '.php';

    // Cek apakah file sudah ada
    if (file_exists($filePath)) {
        echo "⏭️  Model {$modelName} sudah ada (skip)\n";
    } else {
        if (file_put_contents($filePath, $modelCode)) {
            echo "✅ Model {$modelName} dibuat ✓\n";
            echo "   └─ Tabel: {$table}\n";
            echo "   └─ Primary Key: {$primaryKey}\n";
            echo "   └─ Fillable: " . count($fillable) . " kolom\n";
            $generatedCount++;
        } else {
            echo "❌ Error membuat {$modelName}\n";
        }
    }
}

$conn->close();

echo "\n";
echo "================================================\n";
echo "✅ Selesai! " . $generatedCount . " model baru dibuat\n";
echo "📁 Lokasi: app/Models/\n";
echo "\nModel siap digunakan dalam program Anda!\n";
?>