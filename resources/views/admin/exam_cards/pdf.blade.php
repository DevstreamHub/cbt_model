<!DOCTYPE html>
<html>
<head>
    <title>Exam Card</title>
    <style>
        @page {
            size: 95mm 60mm; /* This matches 270pt x 170pt */
            margin: 3mm; /* keep margin for aesthetics */
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 5px;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }

        .card {
            width: 100%;
            padding: 3px;
            border: 0.4px solid #000;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            background-color: #004080;
            color: white;
            padding: 1px 0;
        }

        .header h3, .header h4 {
            font-size: 5px;
            margin: 1px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        td {
            vertical-align: top;
            padding: 1px;
        }

        .passport {
            width: 30px;
            height: 30px;
            object-fit: cover;
            border: 0.4px solid #000;
        }

        .label {
            font-weight: bold;
            color: #004080;
            display: inline-block;
            width: 40px;
        }

        .details div {
            line-height: 1.2;
            margin: 1px 0;
        }

        .section-title {
            margin-top: 2px;
            font-weight: bold;
            color: #004080;
            font-size: 6.3px;
        }

        .courses-table {
            width: 100%;
            border: 0.3px solid #ccc;
            font-size: 5px;
        }

        .courses-table th,
        .courses-table td {
            padding: 1px 2px;
            border: 0.3px solid #ccc;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h3>{{ $session->name }} Exam Card</h3>
            <h4>{{ $semester->name }} Semester</h4>
        </div>

        <table>
            <tr>
                <td width="65%">
                    <div class="details">
                        <div><span class="label">Passcode:</span> {{ $student->surname }}</div>
                        <div><span class="label">Student ID:</span> {{ $student->matric_no }}</div>
                        <div><span class="label">Department:</span> {{ $student->department->name ?? 'N/A' }}</div>
                    </div>
                </td>
                <td width="35%" align="right">
                    @php
                        $passportPath = $student->passport 
                            ? public_path('downloaded_images/' . $student->passport) 
                            : public_path('default.png');
                    @endphp

                    @if(file_exists($passportPath))
                        <img src="{{ $passportPath }}" alt="Passport" class="passport">
                    @else
                        <img src="{{ public_path('default.png') }}" alt="Default Passport" class="passport">
                    @endif
                </td>
            </tr>
        </table>
        <hr>
        <div class="section-title">Registered Courses:</div>
        <table class="courses-table">
            <thead>
                <tr>
                    <th style="width: 35%">Code</th>
                    <th style="width: 65%">Course Title</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                    <tr>
                        <td>{{ $course->course_code }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($course->name, 35) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
