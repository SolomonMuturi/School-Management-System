<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Requests\UserRequest;
use App\Models\RolePermission;
use App\Models\UserType;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


class UserController extends Controller
{
    protected $user, $loc, $my_class;

    public function __construct(UserRepo $user, LocationRepo $loc, MyClassRepo $my_class)
    {
        $this->middleware('teamSA', ['only' => ['index', 'store', 'edit', 'update', 'dashboard', 'roles', 'permissions', 'toggleStatus'] ]);
        $this->middleware('super_admin', ['only' => ['reset_pass', 'destroy', 'roleStore', 'roleEdit', 'roleUpdate', 'roleToggle', 'permissionsUpdate'] ]);

        $this->user = $user;
        $this->loc = $loc;
        $this->my_class = $my_class;
    }

    public function dashboard()
    {
        $users = User::orderBy('name')->get();

        $d['total_users'] = $users->count();
        $d['active_users'] = $users->where('status', 'active')->count();
        $d['inactive_users'] = $users->where('status', '!=', 'active')->count();
        $d['roles'] = UserType::all();
        $d['users_by_role'] = $users->groupBy('user_type')
            ->map(function ($group) {
                return count($group);
            });
        $d['recent_users'] = User::orderByDesc('created_at')->take(8)->get();
        $d['recent_logins'] = User::whereNotNull('last_login')->orderByDesc('last_login')->take(8)->get();

        return view('pages.support_team.users.dashboard', $d);
    }

    public function index()
    {
        $ut = $this->user->getAllTypes();
        $ut2 = $ut->where('level', '>', 2);

        $d['active_types'] = $ut->where('status', 'active');
        $d['user_types'] = Qs::userIsAdmin() ? $ut2 : $ut;
        $d['states'] = $this->loc->getStates();
        $d['users'] = $this->user->getPTAUsers();
        $d['nationals'] = $this->loc->getAllNationals();
        $d['blood_groups'] = $this->user->getBloodGroups();
        $d['students'] = \App\Models\StudentRecord::with('user')->get();
        return view('pages.support_team.users.index', $d);
    }

    public function roles()
    {
        $roles = UserType::orderBy('level')->get();

        $roles->each(function ($role) {
            $role->users_count = User::where('user_type', $role->title)->count();
        });

        $d['roles'] = $roles;

        return view('pages.support_team.users.roles', $d);
    }

    public function roleStore(Request $req)
    {
        $this->validate($req, [
            'title' => 'required|string|max:50|unique:user_types,title',
            'name' => 'required|string|max:50',
            'level' => 'required|integer|min:1|max:20',
        ]);

        UserType::create([
            'title' => strtolower(str_replace(' ', '_', $req->title)),
            'name' => ucwords($req->name),
            'level' => $req->level,
            'status' => 'active',
        ]);

        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function roleEdit($id)
    {
        $d['role'] = UserType::find($id);

        return is_null($d['role']) ? back()->with('flash_danger', __('msg.rnf')) : view('pages.support_team.users.role_edit', $d);
    }

    public function roleUpdate(Request $req, $id)
    {
        $this->validate($req, [
            'name' => 'required|string|max:50',
            'level' => 'required|integer|min:1|max:20',
        ]);

        UserType::where('id', $id)->update([
            'name' => ucwords($req->name),
            'level' => $req->level,
        ]);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function roleToggle($id)
    {
        $role = UserType::find($id);

        if (is_null($role)) {
            return back()->with('flash_danger', __('msg.rnf'));
        }

        $role->update(['status' => $role->status === 'active' ? 'inactive' : 'active']);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function permissions()
    {
        $d['roles'] = $this->permissionRoles();
        $d['modules'] = $permissionModules = Qs::getPermissionModules();

        $stored = RolePermission::all(['role', 'module', 'allowed']);
        $matrix = [];

        foreach ($d['roles'] as $role) {
            $defaults = Qs::defaultRolePermissions($role->title);
            foreach ($permissionModules as $module) {
                $allowed = $stored->first(function ($row) use ($role, $module) {
                    return $row->role === $role->title && $row->module === $module;
                });
                $matrix[$role->title][$module] = is_null($allowed)
                    ? in_array($module, $defaults, true)
                    : (bool)$allowed->allowed;
            }
        }

        $d['permissions'] = $matrix;

        return view('pages.support_team.users.permissions', $d);
    }

    public function permissionsUpdate(Request $req)
    {
        $submitted = $req->input('permissions', []);
        $modules = Qs::getPermissionModules();

        if (!is_array($submitted) || empty($submitted)) {
            return back()->with('flash_danger', 'No permissions were submitted. Please select at least one role and save.')->withInput();
        }

        $validRoles = collect($this->permissionRoles())->pluck('title')->all();

        foreach ($submitted as $role => $granted) {
            if (!in_array($role, $validRoles, true)) {
                continue;
            }

            $granted = is_array($granted) ? array_values($granted) : [];

            foreach ($modules as $module) {
                $allowed = ($role === 'super_admin' || in_array($module, $granted, true)) ? 1 : 0;
                RolePermission::updateOrCreate(['role' => $role, 'module' => $module], [
                    'allowed' => $allowed,
                    'updated_at' => now(),
                ]);
            }

            \Illuminate\Support\Facades\Cache::forget('perms_' . $role);
        }

        return back()->with('flash_success', __('msg.update_ok'));
    }

    protected function permissionRoles()
    {
        $roles = UserType::orderBy('level')->get(['id', 'title', 'name', 'level', 'status']);

        $known = $roles->pluck('title')->all();

        $used = User::distinct()->pluck('user_type')->all();
        foreach ($used as $title) {
            if (!in_array($title, $known, true)) {
                $roles->push((object)[
                    'id' => null,
                    'title' => $title,
                    'name' => ucwords(str_replace('_', ' ', $title)),
                    'level' => 20,
                    'status' => 'active',
                ]);
            }
        }

        return $roles;
    }

    public function toggleStatus($id)
    {
        if (Qs::headSA($id)) {
            return back()->with('flash_danger', __('msg.denied'));
        }

        $user = $this->user->find($id);

        if (is_null($user)) {
            return back()->with('flash_danger', __('msg.rnf'));
        }

        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function edit($id)
    {
        $id = Qs::decodeHash($id);
        $d['user'] = $this->user->find($id);
        if (!$d['user']) {
            return Qs::goWithDanger();
        }
        $d['states'] = $this->loc->getStates();
        $d['users'] = $this->user->getPTAUsers();
        $d['blood_groups'] = $this->user->getBloodGroups();
        $d['nationals'] = $this->loc->getAllNationals();
        $d['students'] = \App\Models\StudentRecord::with('user')->get();
        return view('pages.support_team.users.edit', $d);
    }

    public function reset_pass($id)
    {
        $id = Qs::decodeHash($id);

        if (!$id || !$this->user->find($id)) {
            return back()->with('flash_danger', __('msg.rnf'));
        }

        // Redirect if Making Changes to Head of Super Admins
        if(Qs::headSA($id)){
            return back()->with('flash_danger', __('msg.denied'));
        }

        $data['password'] = Hash::make('user');
        $this->user->update($id, $data);
        return back()->with('flash_success', __('msg.pu_reset'));
    }

    public function store(UserRequest $req)
    {
        $user_type = $this->user->findType($req->user_type)->title;

        $data = $req->except(Qs::getStaffRecord());
        $data['name'] = ucwords($req->name);
        $data['user_type'] = $user_type;
        $data['photo'] = Qs::getDefaultUserImage();
        $data['code'] = strtoupper(Str::random(10));

        $user_is_staff = in_array($user_type, Qs::getStaff());
        $user_is_teamSA = in_array($user_type, Qs::getTeamSA());

        $staff_id = Qs::getAppCode().'/STAFF/'.date('Y/m', strtotime($req->emp_date)).'/'.mt_rand(1000, 9999);
        $data['username'] = $uname = ($user_is_teamSA) ? $req->username : $staff_id;

        $pass = $req->password ?: $user_type;
        $data['password'] = Hash::make($pass);

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath($user_type).$data['code'], $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        /* Ensure that both username and Email are not blank*/
        if(!$uname && !$req->email){
            return back()->with('pop_error', __('msg.user_invalid'));
        }

        $user = $this->user->create($data); // Create User

        /* CREATE STAFF RECORD */
        if($user_is_staff){
            $d2 = $req->only(Qs::getStaffRecord());
            $d2['user_id'] = $user->id;
            $d2['code'] = $staff_id;
            $this->user->createStaffRecord($d2);
        }

        /* LINK PARENT TO STUDENTS */
        if($user_type == 'parent'){
            $this->linkStudents((array)$req->students, $user->id);
        }

        return Qs::jsonStoreOk();
    }

    public function update(UserRequest $req, $id)
    {
        $id = Qs::decodeHash($id);

        // Redirect if Making Changes to Head of Super Admins
        if(Qs::headSA($id)){
            return Qs::json(__('msg.denied'), FALSE);
        }

        $user = $this->user->find($id);

        $user_type = $user->user_type;
        $user_is_staff = in_array($user_type, Qs::getStaff());
        $user_is_teamSA = in_array($user_type, Qs::getTeamSA());

        $data = $req->except(Qs::getStaffRecord());
        $data['name'] = ucwords($req->name);
        $data['user_type'] = $user_type;

        if($user_is_staff && !$user_is_teamSA){
            $data['username'] = Qs::getAppCode().'/STAFF/'.date('Y/m', strtotime($req->emp_date)).'/'.mt_rand(1000, 9999);
        }
        else {
            $data['username'] = $user->username;
        }

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath($user_type).$user->code, $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($id, $data);   /* UPDATE USER RECORD */

        /* UPDATE STAFF RECORD */
        if($user_is_staff){
            $d2 = $req->only(Qs::getStaffRecord());
            $d2['code'] = $data['username'];
            $this->user->updateStaffRecord(['user_id' => $id], $d2);
        }

        /* LINK PARENT TO STUDENTS */
        \App\Models\StudentRecord::where('my_parent_id', $id)->update(['my_parent_id' => null]);
        if($user_type == 'parent'){
            $this->linkStudents((array)$req->students, $id);
        }

        return Qs::jsonUpdateOk();
    }

    public function show($user_id)
    {
        $user_id = Qs::decodeHash($user_id);
        if(!$user_id){return back();}

        $data['user'] = $this->user->find($user_id);
        $data['role'] = UserType::where('title', $data['user']->user_type)->first();

        /* Prevent Other Students from viewing Profile of others*/
        if(Auth::user()->id != $user_id && !Qs::userIsTeamSAT() && !Qs::userIsMyChild(Auth::user()->id, $user_id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return view('pages.support_team.users.show', $data);
    }

    public function destroy($id)
    {
        $id = Qs::decodeHash($id);

        // Redirect if Making Changes to Head of Super Admins
        if(Qs::headSA($id)){
            return back()->with('pop_error', __('msg.denied'));
        }

        $user = $this->user->find($id);

        if($user->user_type == 'teacher' && $this->userTeachesSubject($user)) {
            return back()->with('pop_error', __('msg.del_teacher'));
        }

        $path = Qs::getUploadPath($user->user_type).$user->code;
        Storage::exists($path) ? Storage::deleteDirectory($path) : true;
        $this->user->delete($user->id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

    protected function userTeachesSubject($user)
    {
        $subjects = $this->my_class->findSubjectByTeacher($user->id);
        return ($subjects->count() > 0) ? true : false;
    }

    protected function linkStudents(array $studentIds, $parentUserId)
    {
        $ids = array_filter(array_map('intval', $studentIds));
        if (empty($ids)) { return; }

        \App\Models\StudentRecord::whereIn('id', $ids)->update(['my_parent_id' => $parentUserId]);
    }

}
