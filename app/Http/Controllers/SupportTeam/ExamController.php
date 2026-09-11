<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Requests\Exam\ExamCreate;
use App\Http\Requests\Exam\ExamUpdate;
use App\Models\ExamRecord;
use App\Models\Mark;
use App\Models\Subject;
use App\Repositories\ExamRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    protected $exam, $my_class, $user;
    public function __construct(ExamRepo $exam, MyClassRepo $my_class, UserRepo $user)
    {
        $this->middleware('teamSA', ['except' => ['destroy', 'dashboard', 'show'] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->exam = $exam;
        $this->my_class = $my_class;
        $this->user = $user;
    }

    public function index()
    {
        $d['exams'] = $this->exam->all();
        return view('pages.support_team.exams.index', $d);
    }

    public function dashboard()
    {
        $d['exams'] = $exams = $this->exam->all();
        $d['session'] = Qs::getCurrentSession();
        $d['total_exams'] = $exams->count();
        $d['published'] = $exams->where('status', 'published')->count();
        $d['locked'] = \Mk::examIsLocked();
        $d['current_exams'] = $exams->where('year', $d['session']);

        return view('pages.support_team.exams.dashboard', $d);
    }

    public function show($id)
    {
        $ex = $this->exam->find($id);

        if (is_null($ex)) {
            return Qs::goWithDanger('exams.dashboard');
        }

        $d['ex'] = $ex;
        $d['session'] = $ex->year;
        $d['locked'] = \Mk::examIsLocked();

        $records = ExamRecord::where('exam_id', $id)->where('year', $ex->year)->get();
        $d['records'] = $records;

        $subject_ids = Mark::where('exam_id', $id)->where('year', $ex->year)->distinct()->pluck('subject_id');
        $subjects = Subject::whereIn('id', $subject_ids)->with(['my_class', 'teacher'])->get();

        $subjects->each(function ($s) use ($ex) {
            $marks = Mark::where('exam_id', $ex->id)->where('year', $ex->year)->where('subject_id', $s->id)->get();
            $totals = $marks->map(function ($m) {
                return ($m->t1 ?? 0) + ($m->t2 ?? 0) + ($m->t3 ?? 0) + ($m->exm ?? 0);
            });
            $s->average = $totals->isEmpty() ? 0 : round($totals->avg(), 2);
            $s->submitted = $marks->count();
        });

        $d['subjects'] = $subjects;
        $d['classes'] = $this->my_class->all();

        return view('pages.support_team.exams.show', $d);
    }

    public function store(ExamCreate $req)
    {
        $data = $req->only(['name', 'type', 'start_date', 'end_date', 'status', 'term']);
        $data['year'] = Qs::getSetting('current_session');
        $data['status'] = $data['status'] ?: 'pending';

        $this->exam->create($data);
        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function edit($id)
    {
        $d['ex'] = $this->exam->find($id);
        return view('pages.support_team.exams.edit', $d);
    }

    public function update(ExamUpdate $req, $id)
    {
        $data = $req->only(['name', 'type', 'start_date', 'end_date', 'status', 'term']);

        $this->exam->update($id, $data);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function publish($id)
    {
        $ex = $this->exam->find($id);

        if (is_null($ex)) {
            return Qs::goWithDanger('exams.dashboard');
        }

        $ex->update(['status' => 'published']);

        $student_ids = Mark::where('exam_id', $ex->id)->where('year', $ex->year)
            ->distinct()->pluck('student_id')->all();

        if ($student_ids) {
            $links = [];
            foreach ($student_ids as $sid) {
                $links[$sid] = route('marks.show', [$sid, $ex->year]);
            }
            \App\Models\AppNotification::createFor(
                $student_ids,
                'Results published',
                'Your results for "' . $ex->name . '" have been published. You can now view your marksheet.',
                $links,
                'success'
            );
        }

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function close($id)
    {
        $ex = $this->exam->find($id);

        if (is_null($ex)) {
            return Qs::goWithDanger('exams.dashboard');
        }

        $ex->update(['status' => 'closed']);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->exam->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }
}