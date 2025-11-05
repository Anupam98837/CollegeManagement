<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\UserRagisterController;
// use App\Http\Middleware\AuthenticateInstitutionRole;
use App\Http\Controllers\MailController;

Route::get('/', function () {
    return view('/index');
});
Route::get('/Unauthorised', function () {
    return view('modules/unauthorized/unauthorized');
});

// Dynamic Route for admin|faculty|principal|register|accountant
Route::group([
    'prefix' => '{role}',
    'where'  => ['role' => 'admin|faculty|principal|register|accountant'],
], function () {

     // Common dashboard
     Route::get('/dashboard', function ($role) {
        return view("users.{$role}.pages.common.dashboard");
    });
    Route::get('/login', function ($role) {
        return view("users.{$role}.pages.common.login");
    });

    // Academy section
    Route::prefix('academy')->group(function () {
        Route::get('/dashboard', function ($role) {
            return view("users.{$role}.pages.academy.dashboard");
        });
        Route::get('/campus/manage', function ($role) {
            return view("users.{$role}.pages.academy.manageCampus");
        });
        Route::get('/institute/manage', function ($role) {
            return view("users.{$role}.pages.academy.manageInstitute");
        });
    });

    // Institution section
    Route::prefix('institute')->group(function () {
        Route::get('/dashboard', function ($role) {
            return view("users.{$role}.pages.institute.dashboard");
        });
        Route::get('/course/manage', function ($role) {
            return view("users.{$role}.pages.institute.manageInstituteCourse");
        });
        Route::get('/subject/manage', function ($role) {
            return view("users.{$role}.pages.institute.manageSubject");
        });
        Route::get('/notice/manage', function ($role) {
            return view("users.{$role}.pages.institute.manageNotice");
        });
        Route::get('/event/manage', function ($role) {
            return view("users.{$role}.pages.institute.manageEvent");
        });
        Route::get('/routine/manage', function ($role) {
            return view("users.{$role}.pages.institute.manageRoutine");
        });
        Route::get('/study-materials/manage', function ($role) {
            return view("users.{$role}.pages.studyMaterial.manageStudyMaterial");
        });
    });
   


    // Course section
    Route::prefix('course')->group(function () {
        Route::get('/dashboard', function ($role) {
            return view("users.{$role}.pages.course.dashboard");
        });
        Route::get('/manage', function ($role) {
            return view("users.{$role}.pages.course.manageCourse");
        });
        Route::get('/type/manage', function ($role) {
            return view("users.{$role}.pages.course.manageCourseType");
        });
        Route::get('/intake/manage', function ($role) {
            return view("users.{$role}.pages.course.manageIntake");
        });
    });

    // Accounting section
    Route::prefix('accounting')->group(function () {
        Route::get('/dashboard', function ($role) {
            return view("users.{$role}.pages.accounting.dashboard");
        });
        Route::get('/fees/collect', function ($role) {
            return view("users.{$role}.pages.accounting.collectFees");
        });
        Route::get('/fees/manage', function ($role) {
            return view("users.{$role}.pages.accounting.manageFees");
        });
        Route::get('/expense/manage', function ($role) {
            return view("users.{$role}.pages.accounting.manageExpense");
        });
        Route::get('/fees-structure/manage', function ($role) {
            return view("users.{$role}.pages.accounting.manageFeesStructure");
        });
    });

    // Reports
    Route::get('/fees/report', function ($role) {
        return view("users.{$role}.pages.report.feesReport");
    });
    Route::get('/student/attendance/report', function ($role) {
        return view("users.{$role}.pages.report.attendanceReport");

    });

    // All users
    Route::get('/users', function ($role) {
        return view("users.{$role}.pages.allUsers.allUsers");
    });

     
    // Role management
    Route::prefix('role')->group(function () {
        Route::get('/dashboard', function ($role) {
            return view("users.{$role}.pages.role.dashboard");
        });
        Route::get('/manage', function ($role) {
            return view("users.{$role}.pages.role.manageRole");
        });
    });

    // Faculty
    Route::get('/faculty/manage', function ($role) {
        return view("users.{$role}.pages.faculty.manageFaculty");
    });

    // Student section
    Route::prefix('student')->group(function () {
        Route::get('/dashboard', function ($role) {
            return view("users.{$role}.pages.student.dashboard");
        });
        Route::get('/register', function ($role) {
            return view("users.{$role}.pages.student.registerStudent");
        });
        Route::get('/document/upload', function ($role) {
            return view("users.{$role}.pages.student.uploadStudentDoc");
        });
        Route::get('/details', function ($role) {
            return view("users.{$role}.pages.student.studentDetails");
        });
        Route::get('/promote', function ($role) {
            return view("users.{$role}.pages.student.promoteStudent");
        });
        Route::get('/scholarship', function ($role) {
            return view("users.{$role}.pages.student.manageScholarship");
        });
    });

    // Agent
    Route::prefix('agent')->group(function () {
        Route::get('/dashboard', function ($role) {
            return view("users.{$role}.pages.agent.dashboard");
        });
        Route::get('/register', function ($role) {
            return view("users.{$role}.pages.agent.registerAgent");
        });
    });

    // Transport
    Route::prefix('transport')->group(function () {
        Route::get('route/manage', function ($role) {
            return view("users.{$role}.pages.transport.manageRoute");
        });
        Route::get('vehicle/manage', function ($role) {
            return view("users.{$role}.pages.transport.manageVehicle");
        });
    });

    // Hostel
    Route::prefix('hostel')->group(function () {
        Route::get('/manage', function ($role) {
            return view("users.{$role}.pages.hostel.manageHostel");
        });
        Route::get('/room/manage', function ($role) {
            return view("users.{$role}.pages.hostel.manageRoom");
        });
    });

});

//Agent
Route::get('/agent/dashboard', function () {
    return view('users/agent/pages/common/dashboard');
});
Route::get('/agent/login', function () {
    return view('users/agent/pages/common/login');
});
Route::get('/agent/profile', function () {
    return view('users/agent/pages/common/profile');
});

Route::get('/agent/student/register', function () {
    return view('users/agent/pages/student/registerStudent');
});
Route::get('/agent/student/details', function () {
    return view('users/agent/pages/student/studentDetails');
});

//Institution role
Route::get('/institution-role/login', function () {
    return view('modules/institutionRoleLogin/institutionRoleLogin');
});

//faculty
Route::get('/faculty/student/details', function () {
    return view('users/faculty/pages/student/studentDetails');
});
Route::get('/faculty/student/attendance', function () {
    return view('users/faculty/pages/student/attendenceSheet');
});
Route::get('/faculty/student/attendance/report', function () {
    return view('users/faculty/pages/report/attendanceReport');
});

//Student
Route::get('/student/dashboard', function () {
    return view('users/student/pages/common/dashboard');
});
Route::get('/student/login', function () {
    return view('users/student/pages/common/login');
});
Route::get('/student/signup', function () {
    return view('users/student/pages/common/signUp');
});
Route::get('/student/profile', function () {
    return view('users/student/pages/profile/profile');
});
Route::get('/student/register', function () {
    return view('users/student/pages/profile/registerStudent');
});
Route::get('/student/upload-Document', function () {
    return view('users/student/pages/profile/uploadDoc');
});
Route::get('/student/fees', function () {
    return view('users/student/pages/fees/feesStructure');
});


Route::match(['get', 'post'], '/send-mail', [MailController::class, 'sendMail'])
     ->name('mail.send');
