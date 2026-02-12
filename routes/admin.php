<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

// Group Route untuk Admin Lembaga
Route::middleware(['auth', 'role:admin_lembaga,operator,pengajar'])->group(function () {

    // Route Dashboard Admin Lembaga
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Route Panduan Sistem
    Route::get('/guide', [\App\Http\Controllers\Admin\GuideController::class, 'index'])->name('admin.guide.index');

    // Route Resource Operator
    Route::resource('operator', \App\Http\Controllers\Admin\OperatorController::class)->names('admin.operator');

    // Route Resource Pengawas (Proctor)
    Route::resource('proctor', \App\Http\Controllers\Admin\ProctorController::class)->names('admin.proctor');
    Route::prefix('exam_room')->name('admin.exam_room.')->group(function () {
        Route::get('fix_data', [\App\Http\Controllers\Admin\ExamRoomController::class, 'fixData'])->name('fix_data');
        Route::get('{id}/assignments', [\App\Http\Controllers\Admin\ExamRoomController::class, 'assignments'])->name('assignments');
        Route::post('{id}/assign_random', [\App\Http\Controllers\Admin\ExamRoomController::class, 'assignRandom'])->name('assign_random');
        Route::delete('{id}/bulk_remove', [\App\Http\Controllers\Admin\ExamRoomController::class, 'bulkRemove'])->name('bulk_remove');
        Route::delete('{id}/student/{student_id}', [\App\Http\Controllers\Admin\ExamRoomController::class, 'removeStudent'])->name('remove_student');
    });
    Route::resource('exam_room', \App\Http\Controllers\Admin\ExamRoomController::class)->names('admin.exam_room');

    // Route Resource Mata Pelajaran
    Route::resource('subject', \App\Http\Controllers\Admin\SubjectController::class)->names('admin.subject');

    // Route Resource Bank Soal
    Route::post('question/import', [\App\Http\Controllers\Admin\QuestionController::class, 'import'])->name('admin.question.import');
    Route::get('question/template', [\App\Http\Controllers\Admin\QuestionController::class, 'downloadTemplate'])->name('admin.question.template');
    Route::get('question/template-word', [\App\Http\Controllers\Admin\QuestionController::class, 'downloadTemplateWord'])->name('admin.question.template.word'); // New Route
    Route::resource('question', \App\Http\Controllers\Admin\QuestionController::class)->names('admin.question');
    Route::resource('reading_text', \App\Http\Controllers\Admin\ReadingTextController::class)->names('admin.reading_text');
    Route::resource('question_group', \App\Http\Controllers\Admin\QuestionGroupController::class)->names('admin.question_group');

    // Route Poin / Dompet
    Route::get('point', [\App\Http\Controllers\Admin\PointController::class, 'index'])->name('admin.point.index');
    Route::get('point/topup', [\App\Http\Controllers\Admin\PointController::class, 'topup'])->name('admin.point.topup');
    Route::get('point/checkout', [\App\Http\Controllers\Admin\PointController::class, 'checkout'])->name('admin.point.checkout');
    Route::post('point/checkout', [\App\Http\Controllers\Admin\PointController::class, 'storeCheckout'])->name('admin.point.checkout.store');
    Route::get('point/payment/{id}', [\App\Http\Controllers\Admin\PointController::class, 'payment'])->name('admin.point.payment');
    Route::post('point/payment/{id}', [\App\Http\Controllers\Admin\PointController::class, 'storePayment'])->name('admin.point.payment.store');

    // Route Resource Peserta (Siswa)
    Route::post('student/import', [\App\Http\Controllers\Admin\StudentController::class, 'import'])->name('admin.student.import');
    Route::get('student/template', [\App\Http\Controllers\Admin\StudentController::class, 'downloadTemplate'])->name('admin.student.template');
    Route::get('student/export', [\App\Http\Controllers\Admin\StudentController::class, 'export'])->name('admin.student.export');
    Route::post('student/delete-all', [\App\Http\Controllers\Admin\StudentController::class, 'deleteAll'])->name('admin.student.delete_all');
    Route::get('student/cards', [\App\Http\Controllers\Admin\StudentController::class, 'printCards'])->name('admin.student.cards');
    Route::get('student/upload-photo', [\App\Http\Controllers\Admin\StudentController::class, 'uploadPhoto'])->name('admin.student.upload_photo');
    Route::post('student/upload-photo', [\App\Http\Controllers\Admin\StudentController::class, 'storePhoto'])->name('admin.student.store_photo');
    Route::post('student/broadcast/email', [\App\Http\Controllers\Admin\StudentController::class, 'broadcastEmail'])->name('admin.student.broadcast.email');
    Route::post('student/broadcast/whatsapp', [\App\Http\Controllers\Admin\StudentController::class, 'broadcastWhatsapp'])->name('admin.student.broadcast.whatsapp');
    Route::resource('student', \App\Http\Controllers\Admin\StudentController::class)->names('admin.student');

    // Route Resource Jadwal Ujian
    Route::post('exam_session/{exam_session}/regenerate-token', [\App\Http\Controllers\Admin\ExamSessionController::class, 'regenerateToken'])->name('admin.exam_session.regenerate_token');
    Route::resource('exam_session', \App\Http\Controllers\Admin\ExamSessionController::class)->names('admin.exam_session');

    // Route Resource Jenis Ujian (Master Data)
    Route::resource('exam_type', \App\Http\Controllers\Admin\ExamTypeController::class)->names('admin.exam_type');

    // Route Resource Paket Soal
    Route::resource('exam_package', \App\Http\Controllers\Admin\ExamPackageController::class)->names('admin.exam_package');
    Route::post('exam_package/{exam_package}/assign', [\App\Http\Controllers\Admin\ExamPackageController::class, 'assignQuestions'])->name('admin.exam_package.assign');
    Route::post('exam_package/{exam_package}/random', [\App\Http\Controllers\Admin\ExamPackageController::class, 'generateRandomQuestions'])->name('admin.exam_package.random');
    Route::get('exam_package/{exam_package}/preview', [\App\Http\Controllers\Admin\ExamPackageController::class, 'preview'])->name('admin.exam_package.preview');



    // Route Correction
    Route::get('correction', [\App\Http\Controllers\Admin\ExamCorrectionController::class, 'index'])->name('admin.correction.index');
    Route::get('correction/{session}', [\App\Http\Controllers\Admin\ExamCorrectionController::class, 'show'])->name('admin.correction.show');
    Route::get('correction/{attempt}/grade', [\App\Http\Controllers\Admin\ExamCorrectionController::class, 'edit'])->name('admin.correction.edit');
    Route::put('correction/{attempt}', [\App\Http\Controllers\Admin\ExamCorrectionController::class, 'update'])->name('admin.correction.update');

    // Institution Data
    Route::get('institution', [App\Http\Controllers\Admin\InstitutionController::class, 'index'])->name('admin.institution.index');
    Route::put('institution', [App\Http\Controllers\Admin\InstitutionController::class, 'update'])->name('admin.institution.update');

    // Score Scales (Konversi Skor)
    Route::resource('score-scales', App\Http\Controllers\Admin\ScoreScaleController::class)
        ->only(['index', 'store'])
        ->names('admin.score-scales');

    // Route Resource Teacher (Guru)
    Route::post('teacher/{id}/approve', [\App\Http\Controllers\Admin\TeacherController::class, 'approve'])->name('admin.teacher.approve');
    Route::post('teacher/{id}/suspend', [\App\Http\Controllers\Admin\TeacherController::class, 'suspend'])->name('admin.teacher.suspend');
    Route::post('teacher/{id}/activate', [\App\Http\Controllers\Admin\TeacherController::class, 'activate'])->name('admin.teacher.activate');
    Route::resource('teacher', \App\Http\Controllers\Admin\TeacherController::class)->names('admin.teacher');

    // Route Student Group
    Route::resource('student_group', \App\Http\Controllers\Admin\StudentGroupController::class)->except(['create', 'edit', 'show'])->names('admin.student_group');

    // Route Learning Materials (Modul)
    Route::get('learning_material', [\App\Http\Controllers\Admin\LearningMaterialController::class, 'index'])->name('admin.learning_material.index');
    Route::post('learning_material', [\App\Http\Controllers\Admin\LearningMaterialController::class, 'store'])->name('admin.learning_material.store');
    Route::delete('learning_material/{learning_material}', [\App\Http\Controllers\Admin\LearningMaterialController::class, 'destroy'])->name('admin.learning_material.destroy');

    // Route Report
    Route::get('report', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.report.index');
    Route::get('report/exam-schedule', [\App\Http\Controllers\Admin\ReportController::class, 'examSchedule'])->name('admin.report.exam_schedule');
    Route::get('report/exam-schedule/print', [\App\Http\Controllers\Admin\ReportController::class, 'printExamSchedule'])->name('admin.report.print_exam_schedule');
    Route::get('report/desk-card', [\App\Http\Controllers\Admin\ReportController::class, 'deskCardIndex'])->name('admin.report.desk_card.index');
    Route::get('report/desk-card/print', [\App\Http\Controllers\Admin\ReportController::class, 'printDeskCard'])->name('admin.report.desk_card.print');
    Route::get('report/attendance', [\App\Http\Controllers\Admin\ReportController::class, 'attendanceIndex'])->name('admin.report.attendance.index');
    Route::get('report/attendance/print', [\App\Http\Controllers\Admin\ReportController::class, 'printAttendance'])->name('admin.report.attendance.print');

    // Route Attendance Proctor (Dedicated)
    Route::get('report/attendance-proctor', [\App\Http\Controllers\Admin\ReportController::class, 'attendanceProctorIndex'])->name('admin.report.attendance_proctor.index');

    // Route Recap (Data Results)
    Route::get('recap/exam-result', [\App\Http\Controllers\Admin\RecapController::class, 'examResult'])->name('admin.recap.exam_result');
    Route::get('recap/exam-result/print', [\App\Http\Controllers\Admin\RecapController::class, 'printExamResult'])->name('admin.recap.print_exam_result');









    // Nanti akan kita tambahkan route untuk Operator, Lembaga, Materi, dll.
});


