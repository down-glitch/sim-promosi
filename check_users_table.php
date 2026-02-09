<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Column;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check if columns exist
echo "Checking if columns exist in users table:\n";
echo "Phone column exists: " . (Schema::hasColumn('users', 'phone') ? 'YES' : 'NO') . "\n";
echo "Department column exists: " . (Schema::hasColumn('users', 'department') ? 'YES' : 'NO') . "\n";
echo "Bio column exists: " . (Schema::hasColumn('users', 'bio') ? 'YES' : 'NO') . "\n";
echo "Email notifications column exists: " . (Schema::hasColumn('users', 'email_notifications') ? 'YES' : 'NO') . "\n";
echo "SMS notifications column exists: " . (Schema::hasColumn('users', 'sms_notifications') ? 'YES' : 'NO') . "\n";
echo "Push notifications column exists: " . (Schema::hasColumn('users', 'push_notifications') ? 'YES' : 'NO') . "\n";

// Show all columns
echo "\nAll columns in users table:\n";
$columns = Schema::getColumnListing('users');
foreach ($columns as $column) {
    echo "- $column\n";
}