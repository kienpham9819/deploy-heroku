<?php

use Illuminate\Support\Facades\Route;

Route::get('lang/{lang}', 'LocalizationController@translate')->name('localization');
Auth::routes(['register' => false]);
Route::get('/', 'HomeController@index')->name('home')->middleware('role:student');

Route::resource('users', 'UserController')->middleware('role:admin');
Route::get('user-deleted', 'UserController@deleted')->name('users.deleted')
    ->middleware('role:admin');
Route::get('user-restore/{id}', 'UserController@restore')->name('users.restore')
    ->middleware('role:admin');
Route::delete('user-force-delete/{id}', 'UserController@forceDelete')->name('users.forceDelete')
    ->middleware('role:admin');
Route::get('change-password', 'PasswordController@editPassword')->name('change_password');
Route::patch('change-password', 'PasswordController@updatePassword')->name('update_password');

Route::resource('tasks.comments', CommentController::class)
    ->only([
        'show',
        'store',
        'update',
        'destroy',
    ])
    ->shallow();

Route::resource('attachments', AttachmentController::class)
    ->only([
        'store',
        'destroy',
    ])
    ->shallow()
    ->middleware('permission:update-task-progress');

Route::post('import-user', 'UserController@import')->name('users.import')
    ->middleware('role:admin');
Route::resource('roles', 'RoleController')
    ->middleware('role:admin');

Route::get('projects', 'ProjectController@index')->name('projects.index')
    ->middleware('permission:view-project');
Route::get('projects/{project}', 'ProjectController@show')->name('projects.show')
    ->middleware('permission:view-project', 'can:view,project');

Route::get('groups/{group}/projects/create', 'ProjectController@create')->name('groups.projects.create')
    ->middleware('permission:create-project', 'can:create,group');
Route::post('groups/{group}/projects', 'ProjectController@store')->name('groups.projects.store')
    ->middleware('permission:create-project', 'can:create,group');

Route::get('projects/{project}/edit', 'ProjectController@edit')->name('projects.edit')
    ->middleware('permission:update-project', 'can:update,project');
Route::patch('projects/{project}', 'ProjectController@update')->name('projects.update')
    ->middleware('permission:update-project', 'can:update,project');
Route::delete('projects/{project}', 'ProjectController@destroy')->name('projects.destroy')
    ->middleware('permission:delete-project', 'can:delete,project');
Route::patch('projects/{project}/toggle', 'ProjectController@toggle')->name('projects.toggle');

Route::get('projects/{project}/task-lists/', 'TaskListController@index')->name('projects.task-lists.index')
    ->middleware('permission:view-project', 'can:view,project');
Route::get('projects/{project}/task-lists/create', 'TaskListController@create')->name('projects.task-lists.create')
    ->middleware('permission:create-tasklist', 'can:update,project');
Route::get('projects/{project}/task-lists/{taskList}', 'TaskListController@show')->name('projects.task-lists.show')
    ->middleware('permission:view-project', 'can:view,project');
Route::get('projects/{project}/task-lists/{taskList}/edit', 'TaskListController@edit')->name('projects.task-lists.edit')
    ->middleware('permission:update-tasklist', 'can:update,project');
Route::post('projects/{project}/task-lists/', 'TaskListController@store')->name('projects.task-lists.store')
    ->middleware('permission:create-tasklist', 'can:update,project');
Route::patch('projects/{project}/task-lists/{taskList}', 'TaskListController@update')->name('projects.task-lists.update')
    ->middleware('permission:update-tasklist', 'can:update,project');
Route::delete('projects/{project}/task-lists/{taskList}', 'TaskListController@destroy')->name('projects.task-lists.destroy')
    ->middleware('permission:delete-tasklist', 'can:delete,project');

Route::get('task-lists/{taskList}/tasks/', 'TaskController@index')->name('task-lists.tasks.index')
    ->middleware('permission:view-project');
Route::post('task-lists/{taskList}/tasks/', 'TaskController@store')->name('task-lists.tasks.store')
    ->middleware('permission:create-tasklist');
Route::delete('tasks/{task}', 'TaskController@destroy')->name('task-lists.tasks.destroy')
    ->middleware('permission:delete-tasklist');
Route::patch('tasks/{task}/toggle', 'TaskController@toggle')->name('tasks.toggle')
    ->middleware('permission:update-task-progress');

Route::resource('courses', 'CourseController')->middleware('role:admin');
Route::post('import-course', 'CourseController@importCourse')->name('courses.importCourse')
    ->middleware('role:admin');
Route::get('restore-course/{id}', 'CourseController@restoreCourse')->name('courses.restore')
    ->middleware('role:admin');
Route::post('add-user/{course}', 'CourseController@addUserToCourse')->name('courses.addUser')
    ->middleware('role:admin');
Route::delete('delete-user/{course}/{user}', 'CourseController@deleteUserFromCourse')->name('courses.deleteUser')
    ->middleware('role:admin');

Route::post('add-group/{course}/groups', 'GroupController@store')->name('courses.groups.store')
    ->middleware('permission:create-group', 'can:create,course');
Route::get('show-detail-group/{group}', 'GroupController@show')->name('groups.show')
    ->middleware('role:admin', 'permission:view-group', 'can:view,group');
Route::get('edit-group/{group}/edit', 'GroupController@edit')->name('groups.edit')
    ->middleware('role:admin', 'permission:update-group', 'can:update,group');
Route::patch('update-group/{group}', 'GroupController@update')->name('groups.update')
    ->middleware('role:admin', 'permission:update-group', 'can:update,group');
Route::delete('destroy-group/{group}', 'GroupController@destroy')->name('groups.destroy')
    ->middleware('permission:delete-group', 'can:delete,group');
Route::post('add-user-to-group/{group}', 'GroupController@addUserToGroup')->name('groups.addUser')
    ->middleware('role:admin,lecturer');
Route::delete('delete-user-of-group/{group}/{user}', 'GroupController@deleteUserFromGroup')->name('groups.deleteUser')
    ->middleware('role:admin,lecturer');
Route::post('add-leader-to-group/{group}', 'GroupController@addLeaderToGroup')->name('groups.addLeader')
    ->middleware('role:admin,lecturer');

Route::get('course-list-student', 'StudentController@listCourse')->name('students.courseList')
    ->middleware('role:student,leader');
Route::get('course-detail-student/{course}', 'StudentController@showDetailCourse')->name('students.courseDetail')
    ->middleware('role:student,leader', 'can:view,course');
Route::get('group-detail-student/{group}', 'StudentController@showDetailGroup')->name('students.groupDetail')
    ->middleware('role:student,leader', 'permission:view-group', 'can:view,group');

Route::get('course-list-lecturer', 'LecturerController@listCourse')->name('lecturers.courseList')
    ->middleware('role:lecturer', 'permission:view-class');
Route::get('course-detail-lecturer/{course}', 'LecturerController@showDetailCourse')->name('lecturers.courseDetail')
    ->middleware('role:lecturer', 'permission:view-class', 'can:view,course');
Route::get('show-form-edit-group/{group}/edit', 'LecturerController@showFormEditGroup')->name('lecturers.showFormEditGroup')
    ->middleware('role:lecturer', 'permission:update-group', 'can:update,group');
Route::patch('update-group-lecturer/{group}', 'LecturerController@updateGroup')->name('lecturers.updateGroup')
    ->middleware('role:lecturer', 'permission:update-group', 'can:update,group');
Route::get('group-detail/{group}', 'LecturerController@groupDetail')->name('lecturers.groupDetail')
    ->middleware('role:lecturer', 'permission:view-group', 'can:view,group');
