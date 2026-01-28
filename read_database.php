<?php
/**
 * Script untuk membaca isi database simprom
 * Ubah kredensial sesuai konfigurasi phpMyAdmin Anda
 */

// Konfigurasi database
$servername = "127.0.0.1";      // localhost atau IP server
$username = "root";             // username phpMyAdmin
$password = "";                 // password phpMyAdmin (kosong jika tidak ada)
$database = "simprom";          // nama database
$port = 3306;                   // port MySQL (default 3306)

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $database, $port);

// Periksa koneksi
if ($conn->connect_error) {
    die("❌ Koneksi gagal: " . $conn->connect_error);
}

echo "✅ Koneksi ke database 'simprom' berhasil!\n\n";

// Ambil semua nama tabel
$result = $conn->query("SHOW TABLES");

if ($result->num_rows === 0) {
    echo "Database ini kosong (tidak ada tabel)\n";
} else {
    echo "📊 DAFTAR TABEL:\n";
    echo "================\n";
    
    $tables = [];
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
        echo "- " . $row[0] . "\n";
    }
    
    echo "\n";
    
    // Baca isi setiap tabel
    foreach ($tables as $table) {
        echo "\n📋 TABEL: {$table}\n";
        echo str_repeat("=", 50) . "\n";
        
        // Ambil struktur tabel
        $structureResult = $conn->query("DESCRIBE {$table}");
        echo "Struktur:\n";
        while ($column = $structureResult->fetch_assoc()) {
            echo "  - {$column['Field']} ({$column['Type']}) " . 
                 ($column['Null'] == 'NO' ? '[NOT NULL]' : '') .
                 ($column['Key'] ? " [{$column['Key']}]" : '') . "\n";
        }
        
        echo "\nData:\n";
        
        // Ambil data dari tabel
        $dataResult = $conn->query("SELECT * FROM {$table}");
        
        if ($dataResult->num_rows === 0) {
            echo "  (Tabel kosong - tidak ada data)\n";
        } else {
            // Tampilkan header kolom
            $fields = $dataResult->fetch_fields();
            $headers = [];
            foreach ($fields as $field) {
                $headers[] = $field->name;
            }
            echo "  " . implode(" | ", $headers) . "\n";
            echo "  " . str_repeat("-", 80) . "\n";
            
            // Tampilkan data baris per baris
            while ($row = $dataResult->fetch_assoc()) {
                $values = [];
                foreach ($row as $value) {
                    $values[] = $value === null ? 'NULL' : (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value);
                }
                echo "  " . implode(" | ", $values) . "\n";
            }
        }
        echo "\n";
    }
}

// Tutup koneksi
$conn->close();

echo "\n✅ Selesai!\n";
?>
