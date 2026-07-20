<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

try {
    // Run the migration programmatically
    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();
    
    $columns = Schema::getColumnListing('trip_addons');
    
    echo "<h1>Migration Run Successfully!</h1>";
    echo "<pre>Migration Output:\n$output</pre>";
    echo "<pre>trip_addons columns: " . implode(', ', $columns) . "</pre>";
    echo "<br><a href='/Trip/My-Trip/public/admin/trips'>Back to Admin</a>";

} catch (\Exception $e) {
    echo "<h1>Error running migration:</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
