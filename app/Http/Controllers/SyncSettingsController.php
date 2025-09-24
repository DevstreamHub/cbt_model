<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CbtApiSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncSettingsController extends Controller
{
    public function index()
    {
        $setting = CbtApiSetting::first();
        return view('cbt.sync.settings', compact('setting'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'api_key' => 'required',
            'base_url' => 'required|url',
        ]);

        CbtApiSetting::updateOrCreate(
            ['id' => 1],
            ['api_key' => $request->api_key, 'base_url' => $request->base_url]
        );

        return back()->with('success', 'Settings saved successfully!');
    }

    protected function formatTimestamps(array $record): array
{
    foreach ($record as $key => $value) {
        if (is_string($value) && str_contains($value, 'T')) {
            try {
                // Convert to DATETIME for created_at / updated_at
                if (in_array($key, ['created_at', 'updated_at'])) {
                    $record[$key] = Carbon::parse($value)->format('Y-m-d H:i:s');
                }
                // Convert to DATE for dob, date_of_birth, etc.
                elseif (in_array($key, ['dob', 'date_of_birth']) || str_ends_with($key, '_date')) {
                    $record[$key] = Carbon::parse($value)->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // If parsing fails, skip formatting for that value
                continue;
            }
        }
    }

    return $record;
}


    public function syncAll()
    {
        $setting = \App\Models\CbtApiSetting::first();

        if (!$setting) {
            return back()->with('error', 'API settings not configured.');
        }

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $setting->api_key,
            ])->timeout(10)->get($setting->base_url);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()->with('error', 'No internet connection. Please check your network.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to connect: ' . $e->getMessage());
        }

        if (!$response->successful()) {
            return back()->with('error', 'Failed to sync: ' . $response->status());
        }

        if (!$response->successful()) {
            return back()->with('error', 'Failed to sync: ' . $response->status());
        }

        $data = $response->json();

        try {
            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Departments
            if (isset($data['departments'])) {
                foreach ($data['departments'] as $record) {
                    $record = $this->formatTimestamps($record);
                    \App\Models\Department::updateOrInsert(['id' => $record['id']], $record);
                }
            }

            // Levels
            if (isset($data['levels'])) {
                foreach ($data['levels'] as $record) {
                    $record = $this->formatTimestamps($record);
                    \App\Models\Level::updateOrInsert(['id' => $record['id']], $record);
                }
            }
            

            // Semesters
            if (isset($data['semesters'])) {
                foreach ($data['semesters'] as $record) {
                    $record = $this->formatTimestamps($record);
                    \App\Models\Semester::updateOrInsert(['id' => $record['id']], $record);
                }
            }

            // Academic Sessions
            if (isset($data['academic_sessions'])) {
                foreach ($data['academic_sessions'] as $record) {
                    $record = $this->formatTimestamps($record);
                    \App\Models\AcademicSession::updateOrInsert(['id' => $record['id']], $record);
                }
            }

            // Settings
            if (isset($data['settings'])) {
                foreach ($data['settings'] as $record) {
                    $record = $this->formatTimestamps($record);

                    if (isset($record['image'])) {
                        $record['image'] = $this->downloadImage($record['image'], 'settings');
                    }

                    \App\Models\Setting::updateOrInsert(['id' => $record['id']], $record);
                }
            }            
           // Courses (only active ones)
            if (isset($data['courses'])) {
                foreach ($data['courses'] as $record) {
                    $record = $this->formatTimestamps($record);

                    // Only import if course is marked active
                    if (isset($record['is_active']) && $record['is_active']) {
                        \App\Models\Course::updateOrInsert(['id' => $record['id']], $record);
                    }
                }
            }

            // Add static Pre-National Exam course
            // Add static courses
$staticCourses = [
    [
        'id' => 1,
        'course_code' => 'Paper I',
        'name' => 'Pre-National Exam',
        'credit_unit' => Null,
        'core_or_elective' => 'core',
        'department_id' => Null,
        'semester_id' => 2,
        'level_id' => 3,
        'is_active' => true,
    ],
    [
        'id' => 2,
        'course_code' => 'Paper II',
        'name' => 'Pre-National Exam',
        'credit_unit' => Null,
        'core_or_elective' => 'core',
        'department_id' => Null,
        'semester_id' => 2,
        'level_id' => 3,
        'is_active' => true,
    ],
    [
        'id' => 3,
        'course_code' => 'Paper III',
        'name' => 'Pre-National Exam',
        'credit_unit' => Null,
        'core_or_elective' => 'core',
        'department_id' => Null,
        'semester_id' => 2,
        'level_id' => 3,
        'is_active' => true,
    ],
    // Add more static courses here as needed...
];

foreach ($staticCourses as $course) {
    \App\Models\Course::updateOrInsert(['id' => $course['id']], $course);
}


           // Students
            if (isset($data['students'])) {
                foreach ($data['students'] as $record) {
                    $record = $this->formatTimestamps($record);

                    // Handle image
                    if (isset($record['image'])) {
                        $record['passport'] = $this->downloadImage($record['image'], 'passports');
                    }

                    // Only keep allowed fields
                    $allowed = [
                        'id',
                        'application_id',
                        'matric_no',
                        'surname',
                        'other_names',
                        'email',
                        'department_id',
                        'level_id',
                        'user_id',
                        'passport',
                        'can_access_exam',
                    ];

                    $filtered = array_intersect_key($record, array_flip($allowed));

                    \App\Models\Student::updateOrInsert(['id' => $record['id']], $filtered);
                }
            }

            // Course Registrations
            if (isset($data['course_registrations'])) {
                foreach ($data['course_registrations'] as $record) {
                    $record = $this->formatTimestamps($record);
                    \App\Models\CourseRegistration::updateOrInsert(['id' => $record['id']], $record);
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            return back()->with('success', 'All records synced successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    public function clearCbtTables()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('course_registrations')->truncate();
        DB::table('students')->truncate();
        DB::table('courses')->whereNotIn('id', [1, 2, 3])->delete();
        DB::table('academic_sessions')->truncate();
        DB::table('semesters')->truncate();
        DB::table('levels')->truncate();
        DB::table('departments')->truncate();
        DB::table('settings')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('success', 'All CBT-related tables cleared successfully.');
    }
protected function downloadImage(?string $path, string $folder = 'uploads')
{
    if (!$path) {
        return null;
    }

    $baseAssetUrl = 'https://assascollegeofhealth.com.ng/storage/';
    $fullUrl = rtrim($baseAssetUrl, '/') . '/' . ltrim($path, '/');

    // Extract filename from path (e.g. "passports/filename.png" => "filename.png")
    $filenameOnly = basename($path);
    $storagePath = $folder . '/' . $filenameOnly;

    try {
        $contents = file_get_contents($fullUrl);
        // Ensure directory exists
        \Storage::disk('public')->makeDirectory($folder);

        \Storage::disk('public')->put($storagePath, $contents);

        \Log::info("Downloaded: " . $fullUrl . " → " . $storagePath);

        return $storagePath;
    } catch (\Exception $e) {
        \Log::error("Failed to download image from {$fullUrl}: " . $e->getMessage());
        return null;
    }
}


}
