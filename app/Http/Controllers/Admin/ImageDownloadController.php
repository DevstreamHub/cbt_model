<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ImageDownloadController extends Controller
{
    public function download()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $passports = \DB::table('students')->pluck('passport');
        $downloaded = 0;
        $failed = [];

        $basePath = public_path('downloaded_images');
        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }

        foreach ($passports as $filename) {
            if (!$filename) continue;

            $url = "https://assascollegeofhealth.com.ng/storage/" . $filename;
            $filePath = $basePath . '/' . $filename;
            $directory = dirname($filePath);

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            try {
                $response = Http::timeout(30)
                    ->retry(3, 1000)
                    ->sink($filePath)
                    ->get($url);

                if ($response->successful()) {
                    $downloaded++;
                } else {
                    \Log::warning("Failed HTTP for $filename: status " . $response->status());
                    $failed[] = $filename;
                }
            } catch (\Exception $e) {
                \Log::error("Download failed for $filename: " . $e->getMessage());
                $failed[] = $filename;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "$downloaded image(s) downloaded successfully." .
                         (count($failed) ? " " . count($failed) . " failed." : ''),
            'downloaded' => $downloaded,
            'failed' => $failed,
        ]);
    }
}
