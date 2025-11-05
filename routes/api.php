<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AgentController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\AcademyController;
use App\Http\Controllers\API\FeesController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\ScholarshipController;
use App\Http\Controllers\API\ExpensesController;
use App\Http\Controllers\API\TransportController;
use App\Http\Controllers\API\HostelController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\StudyMaterialController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\RoutineController;


// use App\Http\Middleware\AuthenticateInstitutionRole; 

use Illuminate\Support\Facades\Log;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('student/dashboard/{uid}', [DashboardController::class, 'studentDashboard']);
Route::get('/agent/dashboard/{agent_uid}', [DashboardController::class, 'agentDashboard']);

Route::middleware(['isAuthenticateInstitutionRole:Admin'])->group(function () {
Route::post('/register-agent', [AgentController::class, 'registerAgent']);
Route::get('/view-agents', [AgentController::class, 'getAllAgents']);
});
Route::post('/agent/login', [AgentController::class, 'agentLogin']);
Route::post('/agent/logout', [AgentController::class, 'agentLogout']);
Route::get('/agent/students', [AgentController::class, 'getAgentStudents']);

Route::middleware(['isAuthenticateInstitutionRole:Agent'])->group(function () {
    Route::post('/agent/register-student', [AgentController::class, 'ragisterStudentByAgent']);
    Route::get('/agent/details', [AgentController::class, 'getAgentById']);
    Route::post('/agent/change-password', [AgentController::class, 'changeAgentPassword']);
    Route::put('/agent/edit', [AgentController::class, 'editAgent']);
});

Route::post('/student/signup', [StudentController::class, 'studentSignup']);
Route::post('/student/login', [StudentController::class, 'studentLogin']);
Route::post('/student/logout', [StudentController::class, 'studentLogout']);
Route::post('/agent/approve-student', [StudentController::class, 'approveStudentRegistration']);


Route::post('/admin/login', [AdminController::class, 'adminLogin']);
Route::post('/admin/logout', [AdminController::class, 'adminLogout']);
Route::post('/admin/upload-image', [AdminController::class, 'adminUploadImage']);
Route::post('/admin/change-password', [AdminController::class, 'adminChangePassword']);

Route::post('/register-student', [StudentController::class, 'ragisterStudent'])->middleware('isAuthenticateInstitutionRole:Admin,Accountant,Register,Student');
Route::post('/update-student', [StudentController::class, 'editStudent'])->middleware('isAuthenticateInstitutionRole:Admin,Accountant,Register,Student');
Route::post('/bulk-register-students', [StudentController::class, 'uploadBulkStudents'])->middleware('isAuthenticateInstitutionRole:Admin,Accountant,Register');
Route::post('/get-student-by-email', [StudentController::class, 'getStudentByEmail'])->middleware('isAuthenticateInstitutionRole:Admin,Accountant,Register,Student');
Route::post('/upload-student-documents', [StudentController::class, 'uploadStudentDocuments'])->middleware('isAuthenticateInstitutionRole:Admin,Accountant,Register,Student');
Route::post('/update-student-documents', [StudentController::class, 'updateStudentDocuments'])->middleware('isAuthenticateInstitutionRole:Admin,Accountant,Register,Student'); // New Route
Route::get('/view-students', [StudentController::class, 'viewStudents'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::get('/view-students-by-institute', [StudentController::class, 'viewStudentsByInstitute'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::post('/student/toggle-status', [StudentController::class, 'toggleStudentStatus'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::post('/student/change-email', [StudentController::class, 'changeStudentEmailByUid'])->middleware('isAuthenticateInstitutionRole:Student');
Route::post('/student/change-password', [StudentController::class, 'changeStudentPassword'])->middleware('isAuthenticateInstitutionRole:Student');
Route::post('/students/promote', [StudentController::class, 'promoteStudents'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');

Route::middleware(['isAuthenticateInstitutionRole:Admin'])->group(function () {
    Route::post('/add-campus', [AcademyController::class, 'addCampus']);
    Route::get('/get-campuses', [AcademyController::class, 'getCampuses']);
    Route::put('/edit-campus/{campus_id}', [AcademyController::class, 'editCampus']);
    Route::put('/toggle-campus-status/{campus_id}', [AcademyController::class, 'toggleCampusStatus']);
});

Route::middleware(['isAuthenticateInstitutionRole:Admin,,Accountant,Register'])->group(function () {
Route::post('/add-institution', [AcademyController::class, 'addInstitution']);
Route::put('/edit-institution/{id}', [AcademyController::class, 'editInstitution']);
Route::put('/toggle-institution-status/{id}', [AcademyController::class, 'toggleInstitutionStatus']);
Route::post('/add-institution-courses', [AcademyController::class, 'mergeInstitutionCourses']);
Route::put('/edit-institution-courses/{id}', [AcademyController::class, 'editInstitutionCourses']);
Route::delete('/delete-institution-courses/{id}', [AcademyController::class, 'deleteInstitutionCourses']);
Route::post('/update-institution-logo/{id}', [AcademyController::class, 'updateInstitutionLogo']);
});

Route::get('/view-institution-courses', [AcademyController::class, 'viewInstitutionCourses'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::get('/view-institution/{id}', [AcademyController::class, 'viewInstitutionById'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::get('/view-institutions', [AcademyController::class, 'viewInstitutions'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register,Student,Agent');
// Route::get('/view-institution/{id}', [AcademyController::class, 'viewInstitutionById']);


Route::post('/scholarship', [ScholarshipController::class, 'addScholarship'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Student');
Route::post('/scholarship/view', [ScholarshipController::class, 'viewScholarship'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Student');
// Route::post('/scholarship/edit', [ScholarshipController::class, 'editScholarship']);

Route::middleware(['isAuthenticateInstitutionRole:Admin'])->group(function () {
Route::post('/add-course-type', [AcademyController::class, 'addCourseType']);
Route::get('/view-course-type', [AcademyController::class, 'viewCourseTypes']);
Route::put('/edit-course-type/{id}', [AcademyController::class, 'editCourseType']);
Route::put('/toggle-course-type/{id}', [AcademyController::class, 'toggleCourseType']);

Route::post('/add-course', [AcademyController::class, 'addCourse']);
Route::put('/edit-course/{courseId}', [AcademyController::class, 'editCourse']);
Route::put('/toggle-course-status/{id}', [AcademyController::class, 'toggleCourseStatus']);
});
Route::get('/view-courses', [AcademyController::class, 'viewCourses'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::post('/courses-semister', [AcademyController::class, 'coursesSemister'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register,Faculty');

Route::middleware(['isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register'])->group(function () {
Route::post('/add-board', [AcademyController::class, 'addBoard']);
Route::get('/view-boards', [AcademyController::class, 'viewBoards']);
});

Route::middleware(['isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register'])->group(function () {
Route::post('/add-intake', [AcademyController::class, 'addIntake']);
Route::get('/view-all-intakes', [AcademyController::class, 'viewAllIntakes']);
Route::get('/view-intakes/{program_code}', [AcademyController::class, 'viewIntakes']);
Route::put('/edit-intake/{intakeId}', [AcademyController::class, 'editIntake']);
Route::put('/toggle-intake-status/{intakeId}', [AcademyController::class, 'toggleIntakeStatus']);
});
Route::get('/view-intakes-by-institution', [AcademyController::class, 'viewIntakesByInstitution'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');


Route::post('/notices/create', [AcademyController::class, 'createNotice']);
Route::get('/notices', [AcademyController::class, 'viewNotices']);
Route::put('/notices/{id}', [AcademyController::class, 'editNotice']);
Route::delete('/notices/{id}', [AcademyController::class, 'deleteNotice']);


Route::prefix('events')->group(function(){
    Route::post   ('/add',         [AcademyController::class,'addEvent']);
    Route::get    ('/view',        [AcademyController::class,'viewEvents']);
    Route::put    ('/edit/{id}',   [AcademyController::class,'editEvent']);
    Route::put    ('/toggle/{id}', [AcademyController::class,'toggleEvent']);
});

Route::prefix('study-materials')->group(function () {
    Route::post('add',       [StudyMaterialController::class, 'addStudyMaterial']);
    Route::get('view',       [StudyMaterialController::class, 'viewStudyMaterials']);
    Route::put('edit/{id}', [StudyMaterialController::class, 'editStudyMaterial']);
    Route::put('toggle/{id}',[StudyMaterialController::class, 'toggleStudyMaterialStatus']);
});


// -------------------------
// Subjects & Subject-Types
// -------------------------
Route::middleware(['isAuthenticateInstitutionRole:Admin,Principal'])->group(function () {
    // Subject Types
    Route::post   ('/add-subject-type',         [SubjectController::class, 'addSubjectType']);
    Route::get    ('/view-subject-types',       [SubjectController::class, 'viewSubjectTypes']);
    Route::put    ('/edit-subject-type/{id}',   [SubjectController::class, 'editSubjectType']);
    Route::put    ('/toggle-subject-type/{id}', [SubjectController::class, 'toggleSubjectTypeStatus']);

    // Subjects
    Route::post   ('/add-subject',              [SubjectController::class, 'addSubject']);
    Route::get    ('/view-subjects',            [SubjectController::class, 'viewSubjects']);
    Route::put    ('/edit-subject/{id}',        [SubjectController::class, 'editSubject']);
    Route::put    ('/toggle-subject-status/{id}', [SubjectController::class, 'toggleSubjectStatus']);
});




Route::middleware(['isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register'])->group(function () {
Route::post('/add-fees-structure', [FeesController::class, 'addFeesStructure']);
Route::get('/view-fees-structure', [FeesController::class, 'viewFeesStructure']);
Route::get('/view-all-fees-structure', [FeesController::class, 'viewAllFeesStructure']);
Route::put('/edit-fee-structure/{id}', [FeesController::class, 'editFeesStructure']);
Route::post('/toggle-fee-structure/{id}', [FeesController::class, 'toggleFeesStructureStatus']);

Route::post('/save-fees', [FeesController::class, 'addFees']);
Route::post('/edit-fees', [FeesController::class, 'editFees']);
});

Route::get('/view-fees', [FeesController::class, 'viewFees'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register,Student,Agent');
Route::post('/search-fees', [FeesController::class, 'searchFees'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register,Student');
Route::post('/pay-fees', [FeesController::class, 'payFees'])->middleware('isAuthenticateInstitutionRole:Admin,Accountant,Register,Student');
Route::post('/get-fees', [FeesController::class, 'getFees'])->middleware('isAuthenticateInstitutionRole:Admin,Accountant,Register,Student');
Route::get('/fees-payment-summary', [FeesController::class, 'feesPaymentSummary'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register,Student');
Route::get('/fees-report', [FeesController::class, 'feesReport'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::get('/fees-dashboard', [FeesController::class, 'feesDashboard'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');

Route::post('/expense-category', [ExpensesController::class, 'addExpenseCategory'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::get('/expense-categories', [ExpensesController::class, 'viewExpenseCategories'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::put('/edit-expense-category/{id}', [ExpensesController::class, 'editExpenseCategory'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::post('/toggle-expense-category/{id}', [ExpensesController::class, 'toggleExpenseCategory'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::post('/expense', [ExpensesController::class, 'addExpense'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::get('/expenses', [ExpensesController::class, 'viewExpenses'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');

Route::post('vahicle/add', [TransportController::class, 'addVehicle'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::get('vahicle/view', [TransportController::class, 'viewVehicles'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::put('vahicle/edit/{id}', [TransportController::class, 'editVehicle'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::patch('vahicle/toggle/{id}', [TransportController::class, 'toggleVehicleStatus'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');

Route::post('transport-route/add', [TransportController::class, 'addTransportRoute'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::get('transport-route/view', [TransportController::class, 'viewTransportRoutes'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::put('transport-route/edit/{id}', [TransportController::class, 'editTransportRoute'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::patch('transport-route/toggle/{id}', [TransportController::class, 'toggleTransportRoute'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');

Route::post('hostel/add', [HostelController::class, 'addHostel']);
Route::get('hostel/view', [HostelController::class, 'viewHostels']);
Route::put('hostel/edit/{id}', [HostelController::class, 'editHostel']);
Route::patch('hostel/toggle/{id}', [HostelController::class, 'toggleHostel']);
Route::get('hostel/all', [HostelController::class, 'getAllHostels']);

Route::post('room/add', [HostelController::class, 'addRoom']);
Route::get('room/view', [HostelController::class, 'viewRooms']);
Route::put('room/edit/{id}', [HostelController::class, 'editRoom']);
Route::patch('room/toggle/{id}', [HostelController::class, 'toggleRoom']);
Route::get('room/all', [HostelController::class, 'getAllRooms']);

Route::middleware(['isAuthenticateInstitutionRole:Admin'])->group(function () {
Route::post('/assign-role', [RoleController::class, 'assignRole']);
Route::get('/institution/{institution_id}/roles', [RoleController::class, 'getRoleBasedUsers']);
Route::get('/all-institution/roles', [RoleController::class, 'getAllRoles']);
Route::post('/edit-role/{id}', [RoleController::class, 'editRole']);
Route::post('/toggle-user-status/{id}', [RoleController::class, 'toggleUserStatus']);
});
Route::get('/institutions/{institution_id}/faculties', [RoleController::class, 'getFacultyByInstitution'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::post('/faculty-course-assignments', [RoleController::class, 'assignFacultyCourses'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register');
Route::get('/faculty/{faculty_id}/courses/semesters',[RoleController::class, 'getFacultyCoursesByYear'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register,Faculty');
Route::get('students/program/{program_code}/semester/{current_semester}/year/{year}',[RoleController::class, 'getStudentsByProgramSemesterYear'])->middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register,Faculty');

Route::middleware('isAuthenticateInstitutionRole:Admin,Principal,Accountant,Register,Faculty')->group(function() {
    Route::post('/attendance/record', [RoleController::class, 'recordAttendance']);
    Route::get('/attendance',        [RoleController::class, 'getAttendanceByDate']);
    Route::get('/attendance-report', [RoleController::class, 'getAttendanceReport']);

});

Route::post('/institution-role-login', [RoleController::class, 'institutionRoleLogin']);
Route::post('/institution-role-logout', [RoleController::class, 'institutionRoleLogout']);



Route::middleware(['isAuthenticateInstitutionRole:Admin,Principal,Register'])->prefix('routine')->group(function () {
    Route::post    ('add',                [RoutineController::class, 'addRoutine']);
    Route::get     ('view',               [RoutineController::class, 'viewRoutines']);
    Route::put     ('edit/{id}',          [RoutineController::class, 'editRoutine']);
    Route::put     ('toggle-status/{id}', [RoutineController::class, 'toggleRoutineStatus']);
    Route::delete  ('delete/{id}',        [RoutineController::class, 'deleteRoutine']);
});

// Route::post('send-mail', [MailController::class, 'sendMail']);
