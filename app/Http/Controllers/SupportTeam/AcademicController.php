<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Mark;
use App\Models\MyClass;
use App\Models\AcademicTerm;
use App\Models\Subject;
use App\Repositories\AcademicRepo;
use App\Repositories\ExamRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class AcademicController extends Controller
{
    protected $academic;
    protected $my_class;
    protected $exam;
    protected $student;

    public function __construct(AcademicRepo $academic, MyClassRepo $my_class, ExamRepo $exam, StudentRepo $student)
    {
        $this->academic = $academic;
        $this->my_class = $my_class;
        $this->exam = $exam;
        $this->student = $student;
    }

    /******************************* Dashboard *******************************/
    public function dashboard()
    {
        $d['students_total'] = \App\Models\StudentRecord::count();
        $d['classes'] = $this->my_class->all();
        $d['classes_count'] = $d['classes']->count();
        $d['subjects_count'] = Subject::count();
        $d['teachers'] = $this->academic->allTeachers();
        $d['teachers_count'] = $d['teachers']->count();
        $d['current_year'] = $this->academic->currentYear();
        $d['current_term'] = $this->academic->currentTerm();
        $d['upcoming_exams'] = $this->exam->all()->take(6);
        $d['exams_count'] = $this->exam->all()->count();
        $d['performance'] = $this->academic->performanceSummary();
        $d['recent_marks'] = Mark::with(['user', 'subject', 'my_class'])->latest()->take(8)->get();
        $d['recent_assignments'] = $this->academic->allAssignments()->take(5);
        $d['recent_lessons'] = $this->academic->allLessons()->take(5);
        $d['year'] = Qs::getCurrentSession();

        return view('pages.support_team.academics.dashboard', $d);
    }

    /******************************* Classes & Grades *******************************/
    public function classes()
    {
        $d['classes'] = $this->my_class->all();
        $d['current_year'] = $this->academic->currentYear();

        return view('pages.support_team.academics.classes', $d);
    }

    /******************************* Academic Years & Terms *******************************/
    public function years()
    {
        $d['years'] = $this->academic->allYears();
        $d['terms'] = $this->academic->allTerms();
        $d['current_year'] = $this->academic->currentYear();
        $d['current_term'] = $this->academic->currentTerm();
        $d['year_terms'] = $d['current_year']->id ? $this->academic->yearTerms($d['current_year']->id) : collect();

        return view('pages.support_team.academics.years', $d);
    }

    public function yearsStore(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:30',
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date|after:start_date',
        ]);

        $data = ['name' => $req->name, 'start_date' => $req->start_date, 'end_date' => $req->end_date, 'status' => 'active', 'is_current' => 0];
        $this->academic->createYear($data);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function yearCurrent(Request $req)
    {
        $req->validate(['year_id' => 'required|exists:academic_years,id']);
        $this->academic->setCurrentYear($req->year_id);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function termCurrent(Request $req)
    {
        $req->validate(['term_id' => 'required|exists:academic_terms,id']);
        $this->academic->setCurrentTerm($req->term_id);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function termStore(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:50|unique:academic_terms,name',
            'abbr' => 'sometimes|nullable|string|max:10',
            'sequence' => 'sometimes|nullable|integer|min:1',
        ]);

        $data = [
            'name' => $req->name,
            'abbr' => $req->abbr ?: '',
            'sequence' => $req->sequence ?: (AcademicTerm::max('sequence') + 1),
            'is_current' => 0,
        ];
        $this->academic->createTerm($data);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function attachTerm(Request $req)
    {
        $req->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_term_id' => 'required|exists:academic_terms,id',
        ]);

        $this->academic->attachTermToYear($req->academic_year_id, $req->academic_term_id, [
            'start_date' => $req->start_date ?: NULL,
            'end_date' => $req->end_date ?: NULL,
        ]);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    /******************************* Curriculum *******************************/
    public function curriculum()
    {
        $d['curriculums'] = $this->academic->allCurriculums();
        $d['subjects'] = Subject::with('my_class')->orderBy('name')->get();
        $d['classes'] = $this->my_class->all();
        $d['terms'] = $this->academic->allTerms();
        $d['years'] = $this->academic->allYears();
        $d['current_year'] = $this->academic->currentYear();

        return view('pages.support_team.academics.curriculum', $d);
    }

    public function curriculumStore(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:150',
            'program' => 'sometimes|nullable|string|max:150',
            'academic_year_id' => 'sometimes|nullable|exists:academic_years,id',
            'academic_term_id' => 'sometimes|nullable|exists:academic_terms,id',
            'description' => 'sometimes|nullable|string|max:300',
        ]);

        $this->academic->createCurriculum($req->all());

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function curriculumAddSubject(Request $req)
    {
        $req->validate([
            'curriculum_id' => 'required|exists:curriculums,id',
            'subject_id' => 'required|exists:subjects,id',
            'my_class_id' => 'sometimes|nullable',
        ]);

        $this->academic->attachSubjectToCurriculum($req->curriculum_id, $req->subject_id, $req->my_class_id);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function curriculumRemoveSubject($curriculum_id, $subject_id)
    {
        $this->academic->detachSubjectFromCurriculum($curriculum_id, $subject_id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

    public function topicStore(Request $req)
    {
        $req->validate([
            'curriculum_id' => 'required|exists:curriculums,id',
            'subject_id' => 'required|exists:subjects,id',
            'topic' => 'required|string|max:200',
            'learning_objectives' => 'sometimes|nullable|string|max:1000',
            'term' => 'sometimes|nullable|string|max:20',
            'my_class_id' => 'sometimes|nullable',
        ]);

        $this->academic->createCurriculumTopic($req->only(['curriculum_id', 'subject_id', 'my_class_id', 'topic', 'learning_objectives', 'term']));

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function topicDestroy($id)
    {
        $this->academic->deleteCurriculumTopic($id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

    /******************************* Teacher & Subject Assignment *******************************/
    public function assignments()
    {
        $d['assignments_ta'] = $this->academic->allTeacherAssignments();
        $d['teachers'] = $this->academic->allTeachers();
        $d['subjects'] = Subject::with('my_class')->orderBy('name')->get();
        $d['classes'] = $this->my_class->all();
        $d['terms'] = $this->academic->allTerms();
        $d['years'] = $this->academic->allYears();
        $d['current_year'] = $this->academic->currentYear();

        return view('pages.support_team.academics.assignments', $d);
    }

    public function assignmentsStore(Request $req)
    {
        $req->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_term_id' => 'sometimes|nullable|exists:academic_terms,id',
        ]);

        $this->academic->createTeacherAssignment([
            'teacher_id' => $req->teacher_id,
            'subject_id' => $req->subject_id,
            'my_class_id' => $req->my_class_id,
            'academic_year_id' => $req->academic_year_id ?: $this->academic->currentYear()->id,
            'academic_term_id' => $req->academic_term_id ?: $this->academic->currentTerm()->id,
            'status' => $req->status ?: 'active',
        ]);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function teacherAssignmentDestroy($id)
    {
        $this->academic->deleteTeacherAssignment($id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

    /******************************* Lessons *******************************/
    public function lessons()
    {
        $d['lessons'] = $this->academic->allLessons();
        $d['teachers'] = $this->academic->allTeachers();
        $d['subjects'] = Subject::orderBy('name')->get();
        $d['classes'] = $this->my_class->all();
        $d['terms'] = $this->academic->allTerms();
        $d['years'] = $this->academic->allYears();
        $d['current_year'] = $this->academic->currentYear();
        $d['current_term'] = $this->academic->currentTerm();

        return view('pages.support_team.academics.lessons', $d);
    }

    public function lessonsStore(Request $req)
    {
        $req->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'my_class_id' => 'required|exists:my_classes,id',
            'lesson_date' => 'required|date',
            'topic' => 'required|string|max:200',
        ]);

        $this->academic->createLesson([
            'teacher_id' => $req->teacher_id,
            'subject_id' => $req->subject_id,
            'my_class_id' => $req->my_class_id,
            'lesson_date' => $req->lesson_date,
            'topic' => $req->topic,
            'plan' => $req->plan,
            'learning_objectives' => $req->learning_objectives,
            'teaching_notes' => $req->teaching_notes,
            'academic_year_id' => $req->academic_year_id ?: $this->academic->currentYear()->id,
            'academic_term_id' => $req->academic_term_id ?: $this->academic->currentTerm()->id,
        ]);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function lessonDestroy($id)
    {
        $this->academic->deleteLesson($id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

    /******************************* Assignments & Homework *******************************/
    public function assignmentsMgmt()
    {
        $d['assignments_hm'] = $this->academic->allAssignments();
        $d['teachers'] = $this->academic->allTeachers();
        $d['subjects'] = Subject::orderBy('name')->get();
        $d['classes'] = $this->my_class->all();
        $d['terms'] = $this->academic->allTerms();
        $d['years'] = $this->academic->allYears();
        $d['current_year'] = $this->academic->currentYear();
        $d['current_term'] = $this->academic->currentTerm();

        return view('pages.support_team.academics.homework', $d);
    }

    public function assignmentsMgmtStore(Request $req)
    {
        $req->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'my_class_id' => 'required|exists:my_classes,id',
            'title' => 'required|string|max:200',
            'due_date' => 'required|date',
            'max_marks' => 'sometimes|nullable|integer|min:1',
        ]);

        $file_path = NULL;
        if ($req->hasFile('attachment')) {
            $file = $req->file('attachment');
            $file_path = $file->storeAs('uploads/academic/assignments', time().'_'.$file->getClientOriginalName());
        }

        $this->academic->createAssignment([
            'teacher_id' => $req->teacher_id,
            'subject_id' => $req->subject_id,
            'my_class_id' => $req->my_class_id,
            'title' => $req->title,
            'instructions' => $req->instructions,
            'due_date' => $req->due_date,
            'attachment_path' => $file_path,
            'max_marks' => $req->max_marks ?: 100,
            'academic_year_id' => $req->academic_year_id ?: $this->academic->currentYear()->id,
            'academic_term_id' => $req->academic_term_id ?: $this->academic->currentTerm()->id,
            'status' => $req->status ?: 'open',
        ]);

        $student_ids = \App\Models\StudentRecord::where('my_class_id', $req->my_class_id)
            ->where('grad', 0)->pluck('user_id')->all();

        if ($student_ids) {
            \App\Models\AppNotification::createFor(
                $student_ids,
                'New assignment',
                'New assignment: "' . $req->title . '" is due on ' . $req->due_date . '.',
                route('academic.homework'),
                'info'
            );
        }

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function assignmentShow($id)
    {
        $d['assignment'] = $this->academic->findAssignment($id);
        if (!$d['assignment']) { return Qs::goWithDanger(); }

        $d['submissions'] = $this->academic->submissions($id);
        $class_id = $d['assignment']->my_class_id;
        $d['students'] = $class_id ? $this->student->findStudentsByClass($class_id) : collect();

        return view('pages.support_team.academics.submissions', $d);
    }

    public function submissionStore(Request $req)
    {
        $req->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'student_id' => 'required|exists:users,id',
            'submission_text' => 'sometimes|nullable|string|max:2000',
        ]);

        $file_path = NULL;
        if ($req->hasFile('attachment')) {
            $file = $req->file('attachment');
            $file_path = $file->storeAs('uploads/academic/submissions', time().'_'.$file->getClientOriginalName());
        }

        $this->academic->createSubmission([
            'assignment_id' => $req->assignment_id,
            'student_id' => $req->student_id,
            'submission_text' => $req->submission_text,
            'attachment_path' => $file_path,
            'status' => 1,
            'submitted_at' => now(),
        ]);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function submissionGrade(Request $req)
    {
        $req->validate([
            'submission_id' => 'required|exists:assignment_submissions,id',
            'marks' => 'sometimes|nullable|integer|min:0',
            'feedback' => 'sometimes|nullable|string|max:1000',
        ]);

        $this->academic->gradeSubmission($req->submission_id, ['marks' => $req->marks, 'feedback' => $req->feedback]);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function assignmentDestroy($id)
    {
        $this->academic->deleteAssignment($id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

    /******************************* Report Cards *******************************/
    public function reportCards()
    {
        $d['classes'] = $this->my_class->all();
        $d['exams'] = $this->exam->all();
        $d['terms'] = $this->academic->allTerms();
        $d['years'] = $this->academic->allYears();
        $d['settings'] = $this->academicSettings();

        return view('pages.support_team.academics.report_cards', $d);
    }

    public function reportCardStudents(Request $req)
    {
        $class_id = $req->get('class_id');
        if (!$class_id) { return response()->json([]); }

        $students = $this->student->findStudentsByClass($class_id);

        return $students->map(function ($sr) {
            return ['id' => Qs::hash($sr->id), 'name' => $sr->user->name, 'adm_no' => $sr->adm_no];
        });
    }

    public function reportCard(Request $req)
    {
        $req->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:my_classes,id',
        ]);

        $sr_id = Qs::decodeHash($req->student_id);
        $sr = $this->student->getRecord(['id' => $sr_id])->with(['user', 'my_class'])->first();
        if (!$sr) { return Qs::goWithDanger(); }

        $class_type_id = $sr->my_class->class_type_id;
        $exam = $this->exam->find($req->exam_id);
        $marks = $this->exam->getMark(['exam_id' => $exam->id, 'my_class_id' => $req->class_id, 'student_id' => $sr->user_id])
            ->load('subject', 'grade');
        $exam_records = $this->exam->getRecord(['exam_id' => $exam->id, 'student_id' => $sr->user_id, 'my_class_id' => $req->class_id]);
        $er = $exam_records->first();

        $classes_count = \App\Models\StudentRecord::where('my_class_id', $req->class_id)->count();
        $class_avg = \App\Models\ExamRecord::where('exam_id', $exam->id)->where('my_class_id', $req->class_id)->avg('ave');

        $d['sr'] = $sr;
        $d['exam'] = $exam;
        $d['marks'] = $marks;
        $d['er'] = $er;
        $d['class_count'] = $classes_count;
        $d['class_avg'] = $class_avg;
        $d['class_type_id'] = $class_type_id;
        $d['settings'] = $this->academicSettings();

        return view('pages.support_team.academics.report_card_print', $d);
    }

    public function reportCardPdf(Request $req)
    {
        $req->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:my_classes,id',
            'student_id' => 'required',
        ]);

        $sr_id = Qs::decodeHash($req->student_id);
        $sr = $this->student->getRecord(['id' => $sr_id])->with(['user', 'my_class'])->first();
        if (!$sr) { return Qs::goWithDanger(); }

        $class_type_id = $sr->my_class->class_type_id;
        $exam = $this->exam->find($req->exam_id);
        $marks = $this->exam->getMark(['exam_id' => $exam->id, 'my_class_id' => $req->class_id, 'student_id' => $sr->user_id])
            ->load('subject', 'grade');
        $exam_records = $this->exam->getRecord(['exam_id' => $exam->id, 'student_id' => $sr->user_id, 'my_class_id' => $req->class_id]);
        $er = $exam_records->first();

        $classes_count = \App\Models\StudentRecord::where('my_class_id', $req->class_id)->count();
        $class_avg = \App\Models\ExamRecord::where('exam_id', $exam->id)->where('my_class_id', $req->class_id)->avg('ave');

        $d['sr'] = $sr;
        $d['exam'] = $exam;
        $d['marks'] = $marks;
        $d['er'] = $er;
        $d['class_count'] = $classes_count;
        $d['class_avg'] = $class_avg;
        $d['class_type_id'] = $class_type_id;
        $d['settings'] = $this->academicSettings();
        $d['pdf'] = true;

        $pdf_name = 'ReportCard_' . $sr->user->name . '_' . $exam->name . '_' . $exam->year;
        return PDF::loadView('pages.support_team.academics.report_card_print', $d)->download($pdf_name);
    }

    /******************************* Academic Performance *******************************/
    public function performance()
    {
        $d['classes'] = $this->my_class->all();
        $d['subjects'] = Subject::all();
        $d['exams'] = $this->exam->all();
        $d['years'] = $this->academic->allYears();
        $d['current_year'] = $this->academic->currentYear();
        $d['summary'] = $this->academic->performanceSummary(Qs::getCurrentSession());
        $d['students'] = \App\Models\StudentRecord::with('user')
            ->where('status', '!=', 'graduated')->where('status', '!=', 'transferred')
            ->orderBy('id')->limit(500)->get();

        return view('pages.support_team.academics.performance', $d);
    }

    public function performanceClass(Request $req)
    {
        $class_id = $req->get('class_id');
        $year = $req->get('year') ?: Qs::getCurrentSession();
        $d['class'] = $this->my_class->getMC(['id' => $class_id])->first();
        if (!$d['class']) {
            return Qs::goWithDanger();
        }
        $d['year'] = $year;
        $d['rows'] = $this->academic->classPerformance($class_id, $year);
        $d['subjects'] = Subject::pluck('name', 'id');

        return view('pages.support_team.academics.performance_class', $d);
    }

    public function performanceStudent(Request $req)
    {
        $student_id = $req->get('student_id') ? Qs::decodeHash($req->get('student_id')) : NULL;
        $sr = $student_id ? $this->student->getRecord(['id' => $student_id])->with('user')->first() : NULL;
        if (!$sr) { return Qs::goWithDanger(); }

        $d['sr'] = $sr;
        $d['terms'] = $this->exam->getExamYears($sr->user_id);
        $d['marks'] = Mark::with('subject')->where('student_id', $sr->user_id)->orderByDesc('year')->orderByDesc('id')->get();

        return view('pages.support_team.academics.performance_student', $d);
    }

    /******************************* Academic Reports *******************************/
    public function reports()
    {
        $d['classes'] = $this->my_class->all();
        $d['subjects'] = Subject::all();
        $d['exams'] = $this->exam->all();
        $d['years'] = $this->academic->allYears();

        return view('pages.support_team.academics.reports', $d);
    }

    public function reportsClassList(Request $req)
    {
        $class_id = $req->get('class_id');
        if (!$class_id) { return Qs::goWithDanger(); }

        $d['my_class'] = $this->my_class->getMC(['id' => $class_id])->first();
        if (!$d['my_class']) { return Qs::goWithDanger(); }
        $d['students'] = $this->student->findStudentsByClass($class_id);

        return view('pages.support_team.academics.reports_class_list', $d);
    }

    public function reportsSubject(Request $req)
    {
        $req->validate(['subject_id' => 'required|exists:subjects,id']);
        $d['subject'] = Subject::with('my_class')->find($req->subject_id);
        $d['year'] = $req->year ?: Qs::getCurrentSession();
        $d['rows'] = DB::table('marks')
            ->where('subject_id', $req->subject_id)->where('year', $d['year'])
            ->selectRaw('my_class_id, AVG(tca) as avg_tca, AVG(exm) as avg_exm, AVG(tca + exm) as avg_total, COUNT(DISTINCT student_id) as students')
            ->groupBy('my_class_id')->get();
        $d['classes'] = MyClass::pluck('name', 'id');

        return view('pages.support_team.academics.reports_subject', $d);
    }

    public function reportsExamList(Request $req)
    {
        $exam_id = $req->get('exam_id');
        if (!$exam_id) { return Qs::goWithDanger(); }

        $d['exam'] = $this->exam->find($exam_id);
        if (!$d['exam']) { return Qs::goWithDanger(); }

        $d['records'] = \App\Models\ExamRecord::where('exam_id', $exam_id)->orderByDesc('ave')->get();
        $d['users'] = \App\User::whereIn('id', $d['records']->pluck('student_id')->unique())->pluck('name', 'id');
        $d['classes'] = MyClass::pluck('name', 'id');

        return view('pages.support_team.academics.reports_exam', $d);
    }

    /******************************* Academic Settings *******************************/
    private function academicSettings()
    {
        $raw = \App\Models\Setting::where('type', 'academic_settings')->value('description');
        $parts = $raw ? array_filter(explode(';', $raw)) : [];
        $settings = [];
        foreach ($parts as $p) {
            $kv = explode('=', $p, 2);
            if (count($kv) === 2) { $settings[trim($kv[0])] = trim($kv[1]); }
        }

        return $settings;
    }

    public function settings()
    {
        $d['settings'] = $this->academicSettings();
        $d['grades'] = Grade::orderBy('name')->get();
        $d['subjects'] = Subject::with('my_class')->get();
        $d['years'] = $this->academic->allYears();
        $d['terms'] = $this->academic->allTerms();

        return view('pages.support_team.academics.settings', $d);
    }

    public function settingsStore(Request $req)
    {
        $vals = [
            'enable_ranking' => $req->boolean('enable_ranking') ? 1 : 0,
            'enable_teacher_comments' => $req->boolean('enable_teacher_comments') ? 1 : 0,
            'report_card_position' => $req->boolean('report_card_position') ? 1 : 0,
        ];
        $str = implode(';', array_map(function ($k, $v) { return $k.'='.$v; }, array_keys($vals), $vals));

        \App\Models\Setting::updateOrCreate(['type' => 'academic_settings'], ['description' => $str]);

        return back()->with('flash_success', __('msg.update_ok'));
    }
}