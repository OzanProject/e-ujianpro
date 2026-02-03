<!DOCTYPE html>
<html>
<head>
    <title>{{ $subjectLine }}</title>
</head>
<body>
    <p>Halo {{ $student->name }},</p>
    
    <div>
        {!! nl2br(e($content)) !!}
    </div>

    @if(isset($student->nis))
    <hr>
    <p><strong>Informasi Akun Anda:</strong><br>
    Username: {{ $student->nis }}<br>
    (Gunakan Password yang telah diberikan)</p>
    @endif

    <p><br>Terima kasih,<br>
    {{ config('app.name') }}</p>
</body>
</html>
