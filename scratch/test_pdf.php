<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$policy = \App\Models\InsurancePolicy::first();
$service = app(\App\Services\InvoiceService::class);
$pdfPath = $service->generateInsurancePolicyPdf($policy);

echo "Generated PDF Path: " . $pdfPath . "\n";
echo "File Exists on Disk: " . (file_exists(storage_path('app/public/' . $pdfPath)) ? 'YES' : 'NO') . "\n";
if ($pdfPath && file_exists(storage_path('app/public/' . $pdfPath))) {
    echo "File Size: " . filesize(storage_path('app/public/' . $pdfPath)) . " bytes\n";
}
