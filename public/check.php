<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    $columns = Schema::getColumnListing('trip_addons');
    file_put_contents(__DIR__.'/check.txt', json_encode(['success' => true, 'columns' => $columns]));
    
    // Check if the migration was run
    $migrations = DB::table('migrations')->pluck('migration')->toArray();
    file_put_contents(__DIR__.'/check.txt', "\nMigrations: " . json_encode($migrations), FILE_APPEND);
} catch (\Exception $e) {
    file_put_contents(__DIR__.'/check.txt', json_encode(['success' => false, 'error' => $e->getMessage()]));
}
