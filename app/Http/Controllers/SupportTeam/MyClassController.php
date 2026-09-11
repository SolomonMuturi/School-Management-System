<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Requests\MyClass\ClassCreate;
use App\Http\Requests\MyClass\ClassUpdate;
use App\Models\StudentRecord;
use App\Repositories\MyClassRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyClassController extends Controller
{
    protected $my_class, $user;

    public function __construct(MyClassRepo $my_class, UserRepo $user)
    {
        $this->middleware('teamSA', ['except' => ['destroy', 'dashboard', 'show'] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->my_class = $my_class;
        $this->user = $user;
    }

    public function index()
    {
        $d['my_classes'] = $this->my_class->all();
        $d['class_types'] = $this->my_class->getTypes();

        return view('pages.support_team.classes.index', $d);
    }

    public function dashboard()
    {
        $classes = $this->my_class->all()->each(function ($c) {
            $c->student_count = StudentRecord::where('my_class_id', $c->id)->where('status', 'active')->count();
        });

        $d['classes'] = $classes;
        $d['total_students'] = StudentRecord::where('status', 'active')->count();
        $d['active_classes'] = $classes->where('status', 'active')->count();
        $d['teachers'] = $this->user->getUserByType('teacher');

        return view('pages.support_team.classes.dashboard', $d);
    }

    public function show($id)
    {
        $d['c'] = $c = $this->my_class->find($id);

        if (is_null($c)) {
            return Qs::goWithDanger('classes.dashboard');
        }

        $d['students'] = StudentRecord::where('my_class_id', $id)->where('status', 'active')->with('user')->orderBy('adm_no')->get();
        $d['subjects'] = $this->my_class->findSubjectByClass($id);
        $d['teachers'] = $this->user->getUserByType('teacher');
        $d['class_teachers'] = $this->my_class->getMC(['teacher_id' => $c->teacher_id])->get();

        return view('pages.support_team.classes.show', $d);
    }

    public function store(ClassCreate $req)
    {
        $data = $req->all();
        $data['status'] = 'active';
        $this->my_class->create($data);

        return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['c'] = $c = $this->my_class->find($id);

        return is_null($c) ? Qs::goWithDanger('classes.index') : view('pages.support_team.classes.edit', $d) ;
    }

    public function update(ClassUpdate $req, $id)
    {
        $data = $req->only(['name', 'code', 'class_type_id']);
        $this->my_class->update($id, $data);

        return Qs::jsonUpdateOk();
    }

    public function assignTeacher(Request $req, $id)
    {
        $this->validate($req, [
            'teacher_id' => 'required|exists:users,id',
        ]);

        $this->my_class->update($id, ['teacher_id' => $req->teacher_id]);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function toggleStatus($id)
    {
        $c = $this->my_class->find($id);

        if (is_null($c)) {
            return Qs::goWithDanger('classes.dashboard');
        }

        $c->update(['status' => $c->status === 'active' ? 'inactive' : 'active']);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->my_class->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

}