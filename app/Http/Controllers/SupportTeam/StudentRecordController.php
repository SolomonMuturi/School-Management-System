<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Helpers\Mk;
use App\Http\Requests\Student\StudentRecordCreate;
use App\Http\Requests\Student\StudentRecordUpdate;
use App\Models\Exam;
use App\Models\ExamRecord;
use App\Models\FinancePayment;
use App\Models\Promotion;
use App\Models\Setting;
use App\Models\StudentDiscipline;
use App\Models\StudentGuardian;
use App\Models\StudentRecord;
use App\Models\StudentFee;
use App\Models\Mark;
use App\Models\TimeTable;
use App\Models\TimeTableRecord;
use App\Models\TimeSlot;
use App\User;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\SettingRepo;
use App\Repositories\StudentRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StudentRecordController extends Controller
{
    protected $loc, $my_class, $user, $student, $setting;

   public function __construct(LocationRepo $loc, MyClassRepo $my_class, UserRepo $user, StudentRepo $student, SettingRepo $setting)
   {
       $this->middleware('teamSA', ['only' => ['edit','update', 'reset_pass', 'create', 'store', 'graduated'] ]);
       $this->middleware('super_admin', ['only' => ['destroy',] ]);

       $this->middleware('teamSA', ['only' => [
           'guardiansStore', 'guardiansLink', 'guardiansDestroy',
           'documentsStore', 'documentsDestroy',
           'disciplineStore', 'disciplineDestroy',
           'healthStore', 'transportStore', 'activitiesStore', 'activitiesDestroy',
           'statusUpdate', 'settingsStore',
       ]]);

       $this->middleware('teamSAT', ['only' => [
           'index', 'guardians', 'attendance', 'attendanceStore', 'attendanceDestroy',
           'documents', 'documentsDownload', 'discipline', 'health', 'transport',
           'activities', 'history', 'reports', 'reportsStudents', 'reportsClass', 'reportsProfile',
           'status', 'settings',
       ]]);

        $this->loc = $loc;
        $this->my_class = $my_class;
        $this->user = $user;
        $this->student = $student;
        $this->setting = $setting;
   }

    private function getSROrAbort($sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        $sr = $sr_id ? $this->student->getRecord(['id' => $sr_id])->first() : NULL;
        if (!$sr) {
            abort(404);
        }
        return $sr;
    }

    private function studentDocTypes()
    {
        $types = Setting::where('type', 'student_doc_types')->first();
        return $types ? array_values(array_filter(array_map('trim', explode(',', $types->description)))) : [];
    }

    public function reset_pass($st_id)
    {
        $st_id = Qs::decodeHash($st_id);
        if (!$st_id || !$this->user->find($st_id)) {
            return back()->with('flash_danger', __('msg.rnf'));
        }
        $data['password'] = Hash::make('student');
        $this->user->update($st_id, $data);
        return back()->with('flash_success', __('msg.p_reset'));
    }

    public function index(Request $req)
    {
        $data['students'] = $this->student->allStudents([], $req->get('search'), $req->get('status'), $req->get('year'), $req->get('class_id'));
        $data['my_classes'] = $this->my_class->all();
        $data['statuses'] = $this->student->getStudentStatuses();
        $data['years'] = StudentRecord::whereNotNull('year_admitted')->distinct()->orderByDesc('year_admitted')->pluck('year_admitted');
        $data['selected'] = $req->all();

        return view('pages.support_team.students.index', $data);
    }

    public function create()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();
        $data['required_fields'] = $this->requiredFields();
        return view('pages.support_team.students.add', $data);
    }

    private function requiredFields()
    {
        $fields = Setting::where('type', 'student_required_fields')->first();
        return $fields ? array_values(array_filter(explode(',', $fields->description))) : [];
    }

    public function store(StudentRecordCreate $req)
    {
       $data =  $req->only(Qs::getUserRecord());
       $sr =  $req->only(Qs::getStudentData());

        $ct = $this->my_class->findTypeByClass($req->my_class_id)->code;

        $data['user_type'] = 'student';
        $data['name'] = ucwords($req->name);
        $data['code'] = strtoupper(Str::random(10));
        $data['password'] = Hash::make('student');
        $data['photo'] = Qs::getDefaultUserImage();
        $adm_no = $req->adm_no;
        $prefix = Qs::getSetting('student_id_prefix') ?: Qs::getAppCode();
        $data['username'] = strtoupper($prefix.'/'.$ct.'/'.$sr['year_admitted'].'/'.($adm_no ?: mt_rand(1000, 99999)));

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath('student').$data['code'], $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        $user = $this->user->create($data); // Create User

        $sr['adm_no'] = $data['username'];
        $sr['user_id'] = $user->id;
        $sr['session'] = Qs::getSetting('current_session');
        $sr['status'] = 'active';
        $sr['admission_date'] = $req->admission_date ?: date('Y-m-d');
        $sr['previous_school'] = $req->previous_school;

        $this->student->createRecord($sr); // Create Student
        $this->student->logEvent($user->id, 'admission', 'Student admitted into '.$sr['my_class_id'], 'Session: '.$sr['session']);

        return Qs::jsonStoreOk();
    }

    public function listByClass($class_id)
    {
        $data['my_class'] = $mc = $this->my_class->getMC(['id' => $class_id])->first();
        $data['students'] = $this->student->findStudentsByClass($class_id);

        return is_null($mc) ? Qs::goWithDanger() : view('pages.support_team.students.list', $data);
    }

    public function graduated()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['students'] = $this->student->allGradStudents();

        return view('pages.support_team.students.graduated', $data);
    }

    public function not_graduated($sr_id)
    {
        $d['grad'] = 0;
        $d['grad_date'] = NULL;
        $d['session'] = Qs::getSetting('current_session');
        $sr = $this->student->getRecord(['id' => $sr_id])->first();
        $this->student->updateRecord($sr_id, $d);
        if($sr){ $this->student->logEvent($sr->user_id, 'status', 'Marked as not graduated'); }

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function show($sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if(!$sr_id){return Qs::goWithDanger();}

        $data['sr'] = $sr = $this->student->getRecord(['id' => $sr_id])->first();
        if(!$sr){ return Qs::goWithDanger(); }

        /* Prevent Other Students/Parents from viewing Profile of others */
        if(Auth::user()->id != $sr->user_id && !Qs::userIsTeamSAT() && !Qs::userIsMyChild($sr->user_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        $data += $this->loadProfile($sr);

        return view('pages.support_team.students.show', $data);
    }

    private function loadProfile($sr)
    {
        $user_id = $sr->user_id;

        $fees = StudentFee::with(['feeStructure.feeType'])->where('student_id', $user_id)->orderByDesc('id')->get();
        $d['fees'] = $fees;
        $d['total_paid'] = $fees->sum('amount_paid');
        $d['total_balance'] = $fees->sum('balance');
        $d['payments'] = FinancePayment::where('student_id', $user_id)->with(['receipt', 'studentFee'])->orderByDesc('payment_date')->get();

        $d['exam_records'] = ExamRecord::where('student_id', $user_id)->orderByDesc('year')->orderByDesc('id')->get();
        $d['exams'] = Exam::all()->keyBy('id');
        $d['marks_year'] = Mark::where('student_id', $user_id)->orderByDesc('year')->value('year') ?: Qs::getCurrentSession();

        $d['current_session'] = Qs::getCurrentSession();
        $d['current_term'] = Setting::where('type', 'current_term')->value('description') ?: 'N/A';

        $d['marks_exam'] = $exam = Exam::where('year', $d['current_session'])->orderByDesc('term')->orderByDesc('id')->first();
        $d['marks_current'] = $exam
            ? Mark::where('student_id', $user_id)->where('exam_id', $exam->id)->with(['subject', 'grade'])->orderByDesc('tex3')->get()
            : collect();

        $ttr = TimeTableRecord::where('my_class_id', $sr->my_class_id)->where('year', $d['current_session'])->whereNull('exam_id')->first();
        $d['tt_ttr'] = $ttr;
        $d['tt_days'] = collect();
        $d['tt_slots'] = collect();
        $d['tt_grid'] = collect();
        if ($ttr) {
            $tms = TimeSlot::where('ttr_id', $ttr->id)->orderBy('timestamp_from')->get();
            $tts = TimeTable::with(['subject'])->where('ttr_id', $ttr->id)->get();
            $days = $tts->unique('day')->pluck('day');
            $grid = [];
            foreach ($days as $day) {
                foreach ($tms as $tm) {
                    $row = $tts->where('ts_id', $tm->id)->where('day', $day)->first();
                    $grid[] = ['day' => $day, 'time' => $tm->full, 'subject' => $row && $row->subject ? $row->subject->name : null];
                }
            }
            $d['tt_days'] = $days;
            $d['tt_slots'] = $tms;
            $d['tt_grid'] = collect($grid);
        }

        $att = $this->student->attendance($user_id);
        $d['attendance'] = $att;
        $d['att_present'] = $att->where('status', 'present')->count();
        $d['att_late'] = $att->where('status', 'late')->count();
        $d['att_absent'] = $att->where('status', 'absent')->count();
        $d['att_rate'] = $att->count() ? round((($d['att_present'] + $d['att_late']) / $att->count()) * 100, 1) : NULL;

        $d['guardians'] = $this->student->guardians($user_id);
        $d['documents'] = $this->student->documents($user_id);
        $d['discipline'] = $this->student->discipline($user_id);
        $d['health'] = $this->student->health($user_id);
        $d['transport'] = $this->student->transport($user_id);
        $d['activities'] = $this->student->activities($user_id);
        $d['events'] = $this->student->events($user_id);
        $d['promotions'] = Promotion::where('student_id', $user_id)->with(['fc', 'tc'])->orderByDesc('id')->get();
        $d['statuses'] = $this->student->getStudentStatuses();
        $d['doc_types'] = $this->studentDocTypes();
        $d['subjects'] = $this->my_class->findSubjectByClass($sr->my_class_id);

        return $d;
    }

    public function edit($sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if(!$sr_id){return Qs::goWithDanger();}

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();
        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();
        $data['required_fields'] = $this->requiredFields();
        $data['statuses'] = $this->student->getStudentStatuses();
        return view('pages.support_team.students.edit', $data);
    }

    public function update(StudentRecordUpdate $req, $sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if(!$sr_id){return Qs::goWithDanger();}

        $sr = $this->student->getRecord(['id' => $sr_id])->first();
        $d =  $req->only(Qs::getUserRecord());
        $d['name'] = ucwords($req->name);

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath('student').$sr->user->code, $f['name']);
            $d['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($sr->user->id, $d); // Update User Details

        $srec = $req->only(array_merge(Qs::getStudentData(), ['status', 'admission_date', 'previous_school']));

        $this->student->updateRecord($sr_id, $srec); // Update St Rec
        $this->student->logEvent($sr->user_id, 'profile', 'Student profile updated');

        /*** If Class/Section is Changed in Same Year, Delete Marks/ExamRecord of Previous Class/Section ****/
        Mk::deleteOldRecord($sr->user->id, $srec['my_class_id']);

        if (isset($srec['my_class_id']) && (int)$srec['my_class_id'] !== (int)$sr->my_class_id) {
            $new_class = $this->my_class->getMC(['id' => $srec['my_class_id']])->first();
            $this->student->logEvent($sr->user_id, 'class_change', 'Class changed to '.($new_class ? $new_class->name : $srec['my_class_id']));
        }

        if (isset($srec['status']) && $srec['status'] !== $sr->status) {
            $this->student->logEvent($sr->user_id, 'status', "Status changed from {$sr->status} to {$srec['status']}");
            if ($srec['status'] === 'graduated') {
                $this->student->updateRecord($sr_id, ['grad' => 1, 'grad_date' => date('Y-m-d')]);
            }
            if ($sr->status === 'graduated' && $srec['status'] !== 'graduated') {
                $this->student->updateRecord($sr_id, ['grad' => 0, 'grad_date' => NULL]);
            }
        }

        return Qs::jsonUpdateOk();
    }

    public function destroy($st_id)
    {
        $st_id = Qs::decodeHash($st_id);
        if(!$st_id){return Qs::goWithDanger();}

        $sr = $this->student->getRecord(['user_id' => $st_id])->first();
        $path = Qs::getUploadPath('student').$sr->user->code;
        Storage::exists($path) ? Storage::deleteDirectory($path) : false;
        $this->user->delete($sr->user->id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

    /*************** Guardians ***************/
    public function guardians()
    {
        $data['guardians'] = User::where('user_type', 'parent')->orderBy('name')->get();
        $data['students'] = StudentRecord::with(['user', 'my_class', 'my_parent', 'guardians'])
            ->where('status', '!=', 'graduated')->where('status', '!=', 'transferred')
            ->orderBy('my_class_id')->get();
        $data['links'] = StudentGuardian::with(['student', 'user'])->orderByDesc('id')->get();
        $data['statuses'] = ['Father', 'Mother', 'Guardian', 'Other'];

        return view('pages.support_team.students.guardians', $data);
    }

    public function guardiansStore(Request $req)
    {
        $req->validate([
            'name' => 'required|string|min:3|max:150',
            'phone' => 'required|string|min:6|max:20',
            'email' => 'sometimes|nullable|email|max:100|unique:users',
            'gender' => 'sometimes|nullable|string',
        ]);

        $d['name'] = ucwords($req->name);
        $d['user_type'] = 'parent';
        $d['gender'] = $req->gender ?: 'Male';
        $d['phone'] = $req->phone;
        $d['email'] = $req->email;
        $d['password'] = Hash::make('parent');
        $d['code'] = strtoupper(Str::random(10));
        $d['photo'] = Qs::getDefaultUserImage();
        $d['username'] = strtoupper('PGN/'.mt_rand(1000, 99999));

        $this->user->create($d);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function guardiansLink(Request $req)
    {
        $req->validate([
            'student_id' => 'required|exists:student_records,id',
            'user_id' => 'required|exists:users,id',
            'relationship' => 'required|string|max:50',
        ]);

        $sr = StudentRecord::find($req->student_id);
        $guardian = User::find($req->user_id);

        StudentGuardian::updateOrCreate(
            ['student_id' => $sr->user_id, 'user_id' => $req->user_id],
            ['relationship' => $req->relationship, 'is_primary' => $req->boolean('is_primary') ? 1 : 0]
        );

        if ($req->boolean('is_primary')) {
            StudentRecord::where('id', $sr->id)->update(['my_parent_id' => $req->user_id]);
        }

        $this->student->logEvent($sr->user_id, 'guardian', "Guardian linked: {$guardian->name} ({$req->relationship})");

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function guardiansDestroy($id)
    {
        $guardian = StudentGuardian::find($id);
        if ($guardian) {
            $this->student->logEvent($guardian->student_id, 'guardian', 'Guardian link removed');
            $this->student->deleteGuardian($id);
        }

        return back()->with('flash_success', __('msg.del_ok'));
    }

    /*************** Attendance ***************/
    public function attendance(Request $req)
    {
        $class_id = $req->get('class_id');
        $date = $req->get('date') ?: date('Y-m-d');

        $data['my_classes'] = $this->my_class->all();
        $data['class_id'] = $class_id;
        $data['date'] = $date;

        $students = $class_id ? $this->student->findStudentsByClass($class_id) : collect();
        $data['students'] = $students;

        $ids = $students->pluck('user_id');
        $data['attendance'] = $ids->count()
            ? $this->student->getAttendance(['date' => $date])->whereIn('student_id', $ids)->get()->keyBy('student_id')
            : collect();

        $data['summary'] = [
            'marked' => $data['attendance']->count(),
            'present' => $data['attendance']->where('status', 'present')->count(),
            'absent' => $data['attendance']->where('status', 'absent')->count(),
            'late' => $data['attendance']->where('status', 'late')->count(),
        ];

        return view('pages.support_team.students.attendance', $data);
    }

    public function attendanceStore(Request $req)
    {
        $req->validate([
            'class_id' => 'required',
            'date' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'in:present,absent,late',
        ]);

        foreach ($req->status as $student_id => $status) {
            $this->student->updateOrCreateAttendance(
                ['student_id' => $student_id, 'date' => $req->date],
                ['class_id' => $req->class_id, 'status' => $status, 'note' => $req->note ?: NULL]
            );
        }

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function attendanceDestroy($id)
    {
        $this->student->deleteAttendance($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

    /*************** Documents ***************/
    public function documents($sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);
        $data['sr'] = $sr;
        $data['documents'] = $this->student->documents($sr->user_id);
        $data['doc_types'] = $this->studentDocTypes();

        return view('pages.support_team.students.documents', $data);
    }

    public function documentsStore(Request $req, $sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);

        $req->validate([
            'title' => 'required|string|max:150',
            'doc_type' => 'sometimes|nullable|string|max:100',
            'doc' => 'required|file|mimes:pdf,jpeg,jpg,png,doc,docx,xls,xlsx|max:5120',
            'notes' => 'sometimes|nullable|string|max:200',
        ]);

        $file = $req->file('doc');
        $path = $file->storeAs('uploads/student_docs/'.$sr->user->code, Str::random(8).'.'.$file->getClientOriginalExtension());

        $this->student->createDocument([
            'student_id' => $sr->user_id, 'title' => $req->title, 'doc_type' => $req->doc_type,
            'file_path' => $path, 'notes' => $req->notes,
        ]);
        $this->student->logEvent($sr->user_id, 'document', 'Document uploaded: '.$req->title);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function documentsDownload($id)
    {
        $doc = $this->student->findDocument($id);
        if (!$doc || !$doc->file_path || !Storage::exists($doc->file_path)) {
            return abort(404);
        }
        $name = $doc->title.'.'.pathinfo($doc->file_path, PATHINFO_EXTENSION);
        return Storage::download($doc->file_path, $name);
    }

    public function documentsDestroy($id)
    {
        $doc = $this->student->findDocument($id);
        if ($doc) {
            if ($doc->file_path && Storage::exists($doc->file_path)) {
                Storage::delete($doc->file_path);
            }
            $this->student->deleteDocument($id);
        }

        return back()->with('flash_success', __('msg.del_ok'));
    }

    /*************** Discipline ***************/
    public function discipline($sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);
        $data['sr'] = $sr;
        $data['discipline'] = $this->student->discipline($sr->user_id);

        return view('pages.support_team.students.discipline', $data);
    }

    public function disciplineStore(Request $req, $sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);

        $req->validate([
            'incident_type' => 'required|string|max:100',
            'date' => 'required|date',
            'description' => 'required|string|max:500',
            'action_taken' => 'sometimes|nullable|string|max:200',
            'warning_level' => 'required|in:Minor,Warning,Major,Severe',
            'notes' => 'sometimes|nullable|string|max:200',
        ]);

        $this->student->createDiscipline([
            'student_id' => $sr->user_id, 'incident_type' => $req->incident_type, 'date' => $req->date,
            'description' => $req->description, 'action_taken' => $req->action_taken,
            'warning_level' => $req->warning_level, 'notes' => $req->notes,
        ]);
        $this->student->logEvent($sr->user_id, 'discipline', 'Discipline record added: '.$req->incident_type);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function disciplineDestroy($id)
    {
        $rec = $this->student->findDiscipline($id);
        if ($rec) {
            $this->student->logEvent($rec->student_id, 'discipline', 'Discipline record removed');
            $this->student->deleteDiscipline($id);
        }

        return back()->with('flash_success', __('msg.del_ok'));
    }

    /*************** Health ***************/
    public function health($sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);
        $data['sr'] = $sr;
        $data['health'] = $this->student->health($sr->user_id);

        return view('pages.support_team.students.health', $data);
    }

    public function healthStore(Request $req, $sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);

        $req->validate([
            'allergies' => 'sometimes|nullable|string|max:200',
            'medical_conditions' => 'sometimes|nullable|string|max:200',
            'medications' => 'sometimes|nullable|string|max:200',
            'emergency_contact_name' => 'sometimes|nullable|string|max:150',
            'emergency_contact_phone' => 'sometimes|nullable|string|max:20',
            'emergency_contact_relationship' => 'sometimes|nullable|string|max:50',
            'health_notes' => 'sometimes|nullable|string|max:500',
        ]);

        $this->student->updateOrCreateHealth(['student_id' => $sr->user_id], $req->only([
            'allergies', 'medical_conditions', 'medications', 'emergency_contact_name',
            'emergency_contact_phone', 'emergency_contact_relationship', 'health_notes'
        ]));
        $this->student->logEvent($sr->user_id, 'health', 'Health information updated');

        return back()->with('flash_success', __('msg.update_ok'));
    }

    /*************** Transport ***************/
    public function transport($sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);
        $data['sr'] = $sr;
        $data['transport'] = $this->student->transport($sr->user_id);

        return view('pages.support_team.students.transport', $data);
    }

    public function transportStore(Request $req, $sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);

        $req->validate([
            'route_name' => 'sometimes|nullable|string|max:100',
            'pickup_point' => 'sometimes|nullable|string|max:100',
            'vehicle_no' => 'sometimes|nullable|string|max:50',
            'driver_name' => 'sometimes|nullable|string|max:100',
            'driver_phone' => 'sometimes|nullable|string|max:20',
            'status' => 'sometimes|nullable|in:active,inactive',
            'notes' => 'sometimes|nullable|string|max:200',
        ]);

        $this->student->updateOrCreateTransport(['student_id' => $sr->user_id], $req->only([
            'route_name', 'pickup_point', 'vehicle_no', 'driver_name', 'driver_phone', 'status', 'notes'
        ]));
        $this->student->logEvent($sr->user_id, 'transport', 'Transport information updated');

        return back()->with('flash_success', __('msg.update_ok'));
    }

    /*************** Activities ***************/
    public function activities($sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);
        $data['sr'] = $sr;
        $data['activities'] = $this->student->activities($sr->user_id);

        return view('pages.support_team.students.activities', $data);
    }

    public function activitiesStore(Request $req, $sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);

        $req->validate([
            'activity_type' => 'required|in:Club,Sport,Other',
            'activity_name' => 'required|string|max:100',
            'role' => 'sometimes|nullable|string|max:100',
            'session' => 'sometimes|nullable|string|max:20',
            'achievements' => 'sometimes|nullable|string|max:200',
            'notes' => 'sometimes|nullable|string|max:200',
        ]);

        $this->student->createActivity($req->only([
            'activity_type', 'activity_name', 'role', 'session', 'achievements', 'notes'
        ]) + ['student_id' => $sr->user_id]);
        $this->student->logEvent($sr->user_id, 'activity', 'Activity added: '.$req->activity_name);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function activitiesDestroy($id)
    {
        $rec = $this->student->findActivity($id);
        if ($rec) {
            $this->student->logEvent($rec->student_id, 'activity', 'Activity removed: '.$rec->activity_name);
            $this->student->deleteActivity($id);
        }

        return back()->with('flash_success', __('msg.del_ok'));
    }

    /*************** History ***************/
    public function history($sr_id)
    {
        $sr = $this->getSROrAbort($sr_id);
        $data['sr'] = $sr;
        $data['events'] = $this->student->events($sr->user_id);
        $data['promotions'] = Promotion::where('student_id', $sr->user_id)->with(['fc', 'tc'])->orderByDesc('id')->get();

        return view('pages.support_team.students.history', $data);
    }

    /*************** Status ***************/
    public function status()
    {
        $data['statuses'] = $this->student->getStudentStatuses();
        $data['students'] = StudentRecord::with(['user', 'my_class'])->orderBy('my_class_id')->orderBy('status')->get();
        $data['counts'] = StudentRecord::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');

        return view('pages.support_team.students.status', $data);
    }

    public function statusUpdate(Request $req)
    {
        $req->validate([
            'student_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive,graduated,transferred,suspended,withdrawn',
        ]);

        $sr = StudentRecord::where('user_id', $req->student_id)->first();
        if (!$sr) return back()->with('flash_danger', __('msg.rnf'));

        $old = $sr->status;
        $upd = ['status' => $req->status];
        if ($req->status === 'graduated') { $upd['grad'] = 1; $upd['grad_date'] = date('Y-m-d'); }
        if ($req->status === 'transferred' || $req->status === 'withdrawn') { $upd['grad'] = 1; $upd['grad_date'] = date('Y-m-d'); }
        if ($old === 'graduated' && $req->status !== 'graduated') { $upd['grad'] = 0; $upd['grad_date'] = NULL; }

        $sr->update($upd);
        $this->student->logEvent($req->student_id, 'status', "Status changed from {$old} to {$req->status}");

        return back()->with('flash_success', __('msg.update_ok'));
    }

    /*************** Reports ***************/
    public function reports()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['students'] = StudentRecord::with(['user', 'my_class'])->orderBy('my_class_id')->orderBy('status')->get();
        return view('pages.support_team.students.reports', $data);
    }

    public function reportsStudents()
    {
        $data['students'] = $this->student->allStudents([], NULL, NULL, NULL, NULL);
        return view('pages.support_team.students.reports_students', $data);
    }

    public function reportsClass($class_id)
    {
        $data['my_class'] = $this->my_class->getMC(['id' => $class_id])->first();
        if (!$data['my_class']) {
            return Qs::goWithDanger();
        }
        $data['students'] = $this->student->findStudentsByClass($class_id);
        return view('pages.support_team.students.reports_class', $data);
    }

    public function reportsProfile($sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        $sr = $sr_id ? $this->student->getRecord(['id' => $sr_id])->first() : NULL;
        if (!$sr) return Qs::goWithDanger();

        $data['sr'] = $sr;
        $data += $this->loadProfile($sr);

        return view('pages.support_team.students.reports_profile', $data);
    }

    /*************** Settings ***************/
    public function settings()
    {
        $d['settings'] = Setting::whereIn('type', ['student_id_prefix', 'student_doc_types', 'student_required_fields'])->pluck('description', 'type');
        $d['all_fields'] = [
            'name' => 'Full Name', 'gender' => 'Gender', 'address' => 'Address', 'phone' => 'Phone',
            'email' => 'Email', 'dob' => 'Date of Birth', 'bg_id' => 'Blood Group', 'nal_id' => 'Nationality',
            'state_id' => 'State', 'lga_id' => 'LGA', 'my_class_id' => 'Class',
            'year_admitted' => 'Year Admitted', 'previous_school' => 'Previous School', 'admission_date' => 'Admission Date',
        ];

        return view('pages.support_team.students.settings', $d);
    }

    public function settingsStore(Request $req)
    {
        $this->setting->createOrUpdate('student_id_prefix', $req->student_id_prefix ?: 'CJ');
        $this->setting->createOrUpdate('student_doc_types', $req->doc_types ?: '');
        $this->setting->createOrUpdate('student_required_fields', $req->required_fields ? implode(',', $req->required_fields) : '');

        return back()->with('flash_success', __('msg.update_ok'));
    }

}