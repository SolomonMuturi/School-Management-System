<?php

Auth::routes();

/*************** M-Pesa Daraja Callback (public, CSRF-exempt) *****************/
Route::any('finance/mpesa/callback', 'SupportTeam\MpesaController@callback')->name('finance.mpesa.callback');

//Route::get('/test', 'TestController@index')->name('test');
Route::get('/privacy-policy', 'HomeController@privacy_policy')->name('privacy_policy');
Route::get('/terms-of-use', 'HomeController@terms_of_use')->name('terms_of_use');


Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'HomeController@dashboard')->name('home');
    Route::get('/home', 'HomeController@dashboard')->name('home');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

    Route::group(['prefix' => 'my_account'], function() {
        Route::get('/', 'MyAccountController@edit_profile')->name('my_account');
        Route::put('/', 'MyAccountController@update_profile')->name('my_account.update');
        Route::put('/change_password', 'MyAccountController@change_pass')->name('my_account.change_pass');
    });

    /*************** Notifications *****************/
    Route::group(['prefix' => 'notifications'], function(){
        Route::get('/', 'NotificationController@index')->name('notifications.index');
        Route::post('read-all', 'NotificationController@markAll')->name('notifications.read_all');
        Route::post('{id}/read', 'NotificationController@markRead')->name('notifications.read');
        Route::delete('{id}', 'NotificationController@destroy')->name('notifications.destroy');
    });

    /*************** Support Team *****************/
    Route::group(['namespace' => 'SupportTeam',], function(){

        /*************** Students *****************/
        Route::group(['prefix' => 'students'], function(){
            Route::get('reset_pass/{st_id}', 'StudentRecordController@reset_pass')->name('st.reset_pass');
            Route::get('graduated', 'StudentRecordController@graduated')->name('students.graduated');
            Route::put('not_graduated/{id}', 'StudentRecordController@not_graduated')->name('st.not_graduated');
            Route::get('list/{class_id}', 'StudentRecordController@listByClass')->name('students.list')->middleware('teamSAT');

            /* Promotions */
            Route::post('promote_selector', 'PromotionController@selector')->name('students.promote_selector');
            Route::get('promotion/manage', 'PromotionController@manage')->name('students.promotion_manage');
            Route::delete('promotion/reset/{pid}', 'PromotionController@reset')->name('students.promotion_reset');
            Route::delete('promotion/reset_all', 'PromotionController@reset_all')->name('students.promotion_reset_all');
            Route::get('promotion/{fc?}/{tc?}', 'PromotionController@promotion')->name('students.promotion');
            Route::post('promote/{fc}/{tc}', 'PromotionController@promote')->name('students.promote');

            /* Attendance */
            Route::get('attendance', 'StudentRecordController@attendance')->name('students.attendance');
            Route::post('attendance', 'StudentRecordController@attendanceStore')->name('students.attendance.store');
            Route::delete('attendance/{id}', 'StudentRecordController@attendanceDestroy')->name('students.attendance.destroy');

            /* Guardians */
            Route::get('guardians', 'StudentRecordController@guardians')->name('students.guardians');
            Route::post('guardians', 'StudentRecordController@guardiansStore')->name('students.guardians.store');
            Route::post('guardians/link', 'StudentRecordController@guardiansLink')->name('students.guardians.link');
            Route::delete('guardians/{id}', 'StudentRecordController@guardiansDestroy')->name('students.guardians.destroy');

            /* Reports */
            Route::get('reports', 'StudentRecordController@reports')->name('students.reports');
            Route::get('reports/students', 'StudentRecordController@reportsStudents')->name('students.reports.students');
            Route::get('reports/class/{class_id}', 'StudentRecordController@reportsClass')->name('students.reports.class');
            Route::get('reports/profile/{sr_id}', 'StudentRecordController@reportsProfile')->name('students.reports.profile');

            /* Status */
            Route::get('status', 'StudentRecordController@status')->name('students.status');
            Route::post('status', 'StudentRecordController@statusUpdate')->name('students.status.update');

            /* Settings */
            Route::get('settings', 'StudentRecordController@settings')->name('students.settings');
            Route::post('settings', 'StudentRecordController@settingsStore')->name('students.settings.store');

            /* Per-Student sub-modules (must stay before the students resource) */
            Route::get('{sr_id}/documents', 'StudentRecordController@documents')->name('students.documents');
            Route::post('{sr_id}/documents', 'StudentRecordController@documentsStore')->name('students.documents.store');
            Route::get('{sr_id}/documents/download/{id}', 'StudentRecordController@documentsDownload')->name('students.documents.download');
            Route::delete('{sr_id}/documents/{id}', 'StudentRecordController@documentsDestroy')->name('students.documents.destroy');
            Route::get('{sr_id}/discipline', 'StudentRecordController@discipline')->name('students.discipline');
            Route::post('{sr_id}/discipline', 'StudentRecordController@disciplineStore')->name('students.discipline.store');
            Route::delete('{sr_id}/discipline/{id}', 'StudentRecordController@disciplineDestroy')->name('students.discipline.destroy');
            Route::get('{sr_id}/health', 'StudentRecordController@health')->name('students.health');
            Route::post('{sr_id}/health', 'StudentRecordController@healthStore')->name('students.health.store');
            Route::get('{sr_id}/transport', 'StudentRecordController@transport')->name('students.transport');
            Route::post('{sr_id}/transport', 'StudentRecordController@transportStore')->name('students.transport.store');
            Route::get('{sr_id}/activities', 'StudentRecordController@activities')->name('students.activities');
            Route::post('{sr_id}/activities', 'StudentRecordController@activitiesStore')->name('students.activities.store');
            Route::delete('{sr_id}/activities/{id}', 'StudentRecordController@activitiesDestroy')->name('students.activities.destroy');
            Route::get('{sr_id}/history', 'StudentRecordController@history')->name('students.history');

        });

        /*************** Analytics *****************/
        Route::group(['prefix' => 'analytics'], function(){
            Route::get('/', 'AnalyticsController@dashboard')->name('analytics.dashboard');
        });

        /*************** Users *****************/
        Route::group(['prefix' => 'users'], function(){
            Route::get('reset_pass/{id}', 'UserController@reset_pass')->name('users.reset_pass');
            Route::get('dashboard', 'UserController@dashboard')->name('users.dashboard');
            Route::get('roles', 'UserController@roles')->name('users.roles');
            Route::post('roles', 'UserController@roleStore')->name('users.roles.store');
            Route::get('roles/{id}/edit', 'UserController@roleEdit')->name('users.roles.edit');
            Route::put('roles/{id}', 'UserController@roleUpdate')->name('users.roles.update');
            Route::put('roles/{id}/status', 'UserController@roleToggle')->name('users.roles.toggle');
            Route::get('permissions', 'UserController@permissions')->name('users.permissions');
            Route::post('permissions', 'UserController@permissionsUpdate')->name('users.permissions.update');
            Route::get('activity', 'ActivityLogController@index')->name('users.activity');
            Route::put('{id}/status', 'UserController@toggleStatus')->name('users.status');
        });

        /*************** TimeTables *****************/
        Route::group(['prefix' => 'timetables'], function(){
            Route::get('/', 'TimeTableController@index')->name('tt.index');

            Route::group(['middleware' => 'teamSA'], function() {
                Route::post('/', 'TimeTableController@store')->name('tt.store');
                Route::put('/{tt}', 'TimeTableController@update')->name('tt.update');
                Route::delete('/{tt}', 'TimeTableController@delete')->name('tt.delete');
            });

            /*************** TimeTable Records *****************/
            Route::group(['prefix' => 'records'], function(){

                Route::group(['middleware' => 'teamSA'], function(){
                    Route::get('manage/{ttr}', 'TimeTableController@manage')->name('ttr.manage');
                    Route::post('/', 'TimeTableController@store_record')->name('ttr.store');
                    Route::get('edit/{ttr}', 'TimeTableController@edit_record')->name('ttr.edit');
                    Route::put('/{ttr}', 'TimeTableController@update_record')->name('ttr.update');
                });

                Route::get('show/{ttr}', 'TimeTableController@show_record')->name('ttr.show');
                Route::get('print/{ttr}', 'TimeTableController@print_record')->name('ttr.print');
                Route::delete('/{ttr}', 'TimeTableController@delete_record')->name('ttr.destroy');

            });

            /*************** Time Slots *****************/
            Route::group(['prefix' => 'time_slots', 'middleware' => 'teamSA'], function(){
                Route::post('/', 'TimeTableController@store_time_slot')->name('ts.store');
                Route::post('/use/{ttr}', 'TimeTableController@use_time_slot')->name('ts.use');
                Route::get('edit/{ts}', 'TimeTableController@edit_time_slot')->name('ts.edit');
                Route::delete('/{ts}', 'TimeTableController@delete_time_slot')->name('ts.destroy');
                Route::put('/{ts}', 'TimeTableController@update_time_slot')->name('ts.update');
            });

        });

        /*************** Finance *****************/
        Route::group(['prefix' => 'finance', 'middleware' => 'teamAccount'], function(){

            // Dashboard
            Route::get('dashboard', 'FinanceController@dashboard')->name('finance.dashboard');

            // Fee Types
            Route::get('fee-types', 'FinanceController@feeTypes')->name('finance.fee_types');
            Route::post('fee-types', 'FinanceController@feeTypesStore')->name('finance.fee_types.store');
            Route::put('fee-types/{id}', 'FinanceController@feeTypesUpdate')->name('finance.fee_types.update');
            Route::post('fee-types/toggle/{id}', 'FinanceController@feeTypesToggle')->name('finance.fee_types.toggle');

            // Fee Structures
            Route::get('fee-structures', 'FinanceController@feeStructures')->name('finance.fee_structures');
            Route::post('fee-structures', 'FinanceController@feeStructuresStore')->name('finance.fee_structures.store');
            Route::put('fee-structures/{id}', 'FinanceController@feeStructuresUpdate')->name('finance.fee_structures.update');
            Route::delete('fee-structures/{id}', 'FinanceController@feeStructuresDestroy')->name('finance.fee_structures.destroy');

            // Billing / Student Fees
            Route::get('billing', 'FinanceController@billing')->name('finance.billing');
            Route::post('billing/generate', 'FinanceController@generateBilling')->name('finance.billing.generate');
            Route::get('student-bill/{student_id}', 'FinanceController@studentBill')->name('finance.student_bill');
            Route::get('invoice', 'FinanceController@createInvoice')->name('finance.create_invoice');

            // Payments
            Route::get('payments', 'FinanceController@payments')->name('finance.payments');
            Route::post('payments', 'FinanceController@paymentsStore')->name('finance.payments.store');
            Route::post('payments/void/{id}', 'FinanceController@paymentsVoid')->name('finance.payments.void');
            Route::get('payments/students', 'FinanceController@paymentStudents')->name('finance.payments.students');
            Route::get('payments/fees', 'FinanceController@paymentFees')->name('finance.payments.fees');
            Route::get('outstanding/detail/{student_id}', 'FinanceController@outstandingDetail')->name('finance.outstanding.detail');

            // M-Pesa
            Route::get('mpesa', 'FinanceController@mpesaTransactions')->name('finance.mpesa');
            Route::post('mpesa/stk-push', 'MpesaController@stkPush')->name('finance.mpesa.stk_push');
            Route::post('mpesa/status/{id}', 'MpesaController@status')->name('finance.mpesa.status');
            Route::post('settings/mpesa', 'FinanceController@mpesaSettingsStore')->name('finance.settings.mpesa_store');

            // Receipts
            Route::get('receipts', 'FinanceController@receipts')->name('finance.receipts');
            Route::get('receipts/pdf/{id}', 'FinanceController@receiptsPdf')->name('finance.receipts.pdf');
            Route::get('receipts/{id}', 'FinanceController@receiptsShow')->name('finance.receipts.show');

            // Statement
            Route::get('statement/pdf/{student_id}', 'FinanceController@statementPdf')->name('finance.statement.pdf');
            Route::get('statement/{student_id}', 'FinanceController@statement')->name('finance.statement');

            // Discounts
            Route::get('discounts', 'FinanceController@discounts')->name('finance.discounts');
            Route::post('discounts', 'FinanceController@discountsStore')->name('finance.discounts.store');
            Route::post('discounts/revoke/{id}', 'FinanceController@discountsRevoke')->name('finance.discounts.revoke');

            // Refunds
            Route::get('refunds', 'FinanceController@refunds')->name('finance.refunds');
            Route::post('refunds', 'FinanceController@refundsStore')->name('finance.refunds.store');
            Route::post('refunds/void/{id}', 'FinanceController@refundsVoid')->name('finance.refunds.void');

            // Expenses
            Route::get('expenses', 'FinanceController@expenses')->name('finance.expenses');
            Route::post('expenses', 'FinanceController@expensesStore')->name('finance.expenses.store');
            Route::post('expenses/void/{id}', 'FinanceController@expensesVoid')->name('finance.expenses.void');
            Route::post('expense-categories', 'FinanceController@expenseCategoriesStore')->name('finance.expense_categories.store');

            // Suppliers
            Route::get('suppliers', 'FinanceController@suppliers')->name('finance.suppliers');
            Route::post('suppliers', 'FinanceController@suppliersStore')->name('finance.suppliers.store');
            Route::put('suppliers/{id}', 'FinanceController@suppliersUpdate')->name('finance.suppliers.update');
            Route::post('suppliers/toggle/{id}', 'FinanceController@suppliersToggle')->name('finance.suppliers.toggle');

            // Accounts (Cash & Bank)
            Route::get('accounts', 'FinanceController@accounts')->name('finance.accounts');
            Route::post('accounts', 'FinanceController@accountsStore')->name('finance.accounts.store');
            Route::put('accounts/{id}', 'FinanceController@accountsUpdate')->name('finance.accounts.update');

            // Reports
            Route::get('reports', 'FinanceController@reports')->name('finance.reports');

            // Settings
            Route::get('settings', 'FinanceController@settings')->name('finance.settings');
        });

        /*************** Pins *****************/
        Route::group(['prefix' => 'pins'], function(){
            Route::get('create', 'PinController@create')->name('pins.create');
            Route::get('/', 'PinController@index')->name('pins.index');
            Route::post('/', 'PinController@store')->name('pins.store');
            Route::get('enter/{id}', 'PinController@enter_pin')->name('pins.enter');
            Route::post('verify/{id}', 'PinController@verify')->name('pins.verify');
            Route::delete('/', 'PinController@destroy')->name('pins.destroy');
        });

        /*************** Marks *****************/
        Route::group(['prefix' => 'marks'], function(){

           // FOR teamSA
            Route::group(['middleware' => 'teamSA'], function(){
                Route::get('batch_fix', 'MarkController@batch_fix')->name('marks.batch_fix');
                Route::put('batch_update', 'MarkController@batch_update')->name('marks.batch_update');
                Route::get('tabulation/{exam?}/{class?}', 'MarkController@tabulation')->name('marks.tabulation');
                Route::post('tabulation', 'MarkController@tabulation_select')->name('marks.tabulation_select');
                Route::get('tabulation/print/{exam}/{class}', 'MarkController@print_tabulation')->name('marks.print_tabulation');
            });

            // FOR teamSAT
            Route::group(['middleware' => 'teamSAT'], function(){
                Route::get('/', 'MarkController@index')->name('marks.index');
                Route::get('manage/{exam}/{class}/{subject}', 'MarkController@manage')->name('marks.manage');
                Route::put('update/{exam}/{class}/{subject}', 'MarkController@update')->name('marks.update');
                Route::put('comment_update/{exr_id}', 'MarkController@comment_update')->name('marks.comment_update');
                Route::put('skills_update/{skill}/{exr_id}', 'MarkController@skills_update')->name('marks.skills_update');
                Route::post('selector', 'MarkController@selector')->name('marks.selector');
                Route::get('bulk/{class?}', 'MarkController@bulk')->name('marks.bulk');
                Route::post('bulk', 'MarkController@bulk_select')->name('marks.bulk_select');
            });

            Route::get('select_year/{id}', 'MarkController@year_selector')->name('marks.year_selector');
            Route::post('select_year/{id}', 'MarkController@year_selected')->name('marks.year_select');
            Route::get('show/{id}/{year}', 'MarkController@show')->name('marks.show');
            Route::get('print/{id}/{exam_id}/{year}', 'MarkController@print_view')->name('marks.print');

        });

        /************************%%%%% ACADEMICS %%%%%****************************/
        Route::group(['prefix' => 'academics', 'middleware' => 'teamAcademic'], function(){

            /* Dashboard */
            Route::get('/', 'AcademicController@dashboard')->name('academic.dashboard');

/* Academic Years & Terms */
            Route::get('years', 'AcademicController@years')->name('academic.years');
            Route::post('years', 'AcademicController@yearsStore')->middleware('teamSA')->name('academic.years.store');
            Route::post('years/current', 'AcademicController@yearCurrent')->middleware('teamSA')->name('academic.years.current');
            Route::post('terms', 'AcademicController@termStore')->middleware('teamSA')->name('academic.terms.store');
            Route::post('terms/current', 'AcademicController@termCurrent')->middleware('teamSA')->name('academic.terms.current');
            Route::post('year_term', 'AcademicController@attachTerm')->middleware('teamSA')->name('academic.year_term.store');

            /* Classes */
            Route::get('classes', 'AcademicController@classes')->name('academic.classes');

            /* Curriculum */
            Route::get('curriculum', 'AcademicController@curriculum')->name('academic.curriculum');
            Route::post('curriculum', 'AcademicController@curriculumStore')->middleware('teamSA')->name('academic.curriculum.store');
            Route::post('curriculum/subject', 'AcademicController@curriculumAddSubject')->middleware('teamSA')->name('academic.curriculum.subject.store');
            Route::delete('curriculum/subject/{curriculum_id}/{subject_id}', 'AcademicController@curriculumRemoveSubject')->middleware('teamSA')->name('academic.curriculum.subject.destroy');
            Route::post('curriculum/topic', 'AcademicController@topicStore')->middleware('teamSA')->name('academic.curriculum.topic.store');
            Route::delete('curriculum/topic/{id}', 'AcademicController@topicDestroy')->middleware('teamSA')->name('academic.curriculum.topic.destroy');
            Route::delete('curriculum/{id}', 'AcademicController@curriculumDestroy')->middleware('teamSA')->name('academic.curriculum.destroy');

            /* Teacher & Subject Assignment */
            Route::get('assign', 'AcademicController@assignments')->name('academic.assign');
            Route::post('assign', 'AcademicController@assignmentsStore')->middleware('teamSA')->name('academic.assign.store');
            Route::delete('assign/{id}', 'AcademicController@teacherAssignmentDestroy')->middleware('teamSA')->name('academic.assign.destroy');

            /* Lessons */
            Route::get('lessons', 'AcademicController@lessons')->name('academic.lessons');
            Route::post('lessons', 'AcademicController@lessonsStore')->middleware('teamSAT')->name('academic.lessons.store');
            Route::delete('lessons/{id}', 'AcademicController@lessonDestroy')->middleware('teamSAT')->name('academic.lessons.destroy');

            /* Assignments & Homework */
            Route::get('homework', 'AcademicController@assignmentsMgmt')->name('academic.homework');
            Route::post('homework', 'AcademicController@assignmentsMgmtStore')->middleware('teamSAT')->name('academic.homework.store');
            Route::get('homework/{id}', 'AcademicController@assignmentShow')->name('academic.homework.show');
            Route::post('homework/submit', 'AcademicController@submissionStore')->middleware('teamSAT')->name('academic.homework.submit');
            Route::post('homework/grade', 'AcademicController@submissionGrade')->middleware('teamSAT')->name('academic.homework.grade');
            Route::delete('homework/{id}', 'AcademicController@assignmentDestroy')->middleware('teamSAT')->name('academic.homework.destroy');

            /* Report Cards */
            Route::get('report-cards', 'AcademicController@reportCards')->name('academic.report_cards');
            Route::get('report-cards/students', 'AcademicController@reportCardStudents')->name('academic.report_cards.students');
            Route::post('report-cards', 'AcademicController@reportCard')->name('academic.report_cards.show');
            Route::post('report-cards/pdf', 'AcademicController@reportCardPdf')->name('academic.report_cards.pdf');

            /* Academic Performance */
            Route::get('performance', 'AcademicController@performance')->name('academic.performance');
            Route::get('performance/class', 'AcademicController@performanceClass')->name('academic.performance.class');
            Route::get('performance/student', 'AcademicController@performanceStudent')->name('academic.performance.student');

            /* Academic Reports */
            Route::get('reports', 'AcademicController@reports')->name('academic.reports');
            Route::get('reports/class', 'AcademicController@reportsClassList')->name('academic.reports.class');
            Route::get('reports/subject', 'AcademicController@reportsSubject')->name('academic.reports.subject');
            Route::get('reports/exam', 'AcademicController@reportsExamList')->name('academic.reports.exam');

            /* Academic Settings */
            Route::get('settings', 'AcademicController@settings')->name('academic.settings');
            Route::post('settings', 'AcademicController@settingsStore')->middleware('teamSA')->name('academic.settings.store');

        });

        Route::resource('students', 'StudentRecordController');
        Route::resource('users', 'UserController')->except('create');

        /*************** Classes / Subjects / Exams Dashboards *****************/
        Route::get('classes/dashboard', 'MyClassController@dashboard')->name('classes.dashboard');
        Route::post('classes/{class}/assign_teacher', 'MyClassController@assignTeacher')->name('classes.assign_teacher');
        Route::put('classes/{class}/status', 'MyClassController@toggleStatus')->name('classes.status');

        Route::get('subjects/dashboard', 'SubjectController@dashboard')->name('subjects.dashboard');
        Route::put('subjects/{subject}/status', 'SubjectController@toggleStatus')->name('subjects.status');

        Route::get('exams/dashboard', 'ExamController@dashboard')->name('exams.dashboard');
        Route::post('exams/{exam}/publish', 'ExamController@publish')->name('exams.publish');
        Route::post('exams/{exam}/close', 'ExamController@close')->name('exams.close');

        Route::resource('classes', 'MyClassController')->except('create');
        Route::resource('subjects', 'SubjectController')->except('create');
        Route::resource('grades', 'GradeController')->except('create', 'show');
        Route::resource('exams', 'ExamController')->except('create');

    });

    /************************ AJAX ****************************/
    Route::group(['prefix' => 'ajax'], function() {
        Route::get('get_lga/{state_id}', 'AjaxController@get_lga')->name('get_lga');
        Route::get('get_class_subjects/{class_id}', 'AjaxController@get_class_subjects')->name('get_class_subjects');
    });

});

/************************ SUPER ADMIN ****************************/
Route::group(['namespace' => 'SuperAdmin','middleware' => 'perm:System Settings', 'prefix' => 'super_admin'], function(){

    Route::get('/settings', 'SettingController@index')->name('settings');
    Route::put('/settings', 'SettingController@update')->name('settings.update');

});

/************************ PARENT ****************************/
Route::group(['namespace' => 'MyParent','middleware' => 'my_parent',], function(){

    Route::get('/my_children', 'MyController@children')->name('my_children');

});
