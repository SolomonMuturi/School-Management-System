<?php

namespace App\Repositories;

use App\Helpers\Qs;
use App\Models\Promotion;
use App\Models\StudentActivity;
use App\Models\StudentAttendance;
use App\Models\StudentDiscipline;
use App\Models\StudentDocument;
use App\Models\StudentEvent;
use App\Models\StudentGuardian;
use App\Models\StudentHealth;
use App\Models\StudentRecord;
use App\Models\StudentTransport;

class StudentRepo {


    public function findStudentsByClass($class_id)
    {
        return $this->activeStudents()->where(['my_class_id' => $class_id])->with(['my_class', 'user'])->get()->sortBy('user.name');
    }

    public function activeStudents()
    {
        return StudentRecord::where(['grad' => 0]);
    }

    public function gradStudents()
    {
        return StudentRecord::where(['grad' => 1])->orderByDesc('grad_date');
    }

    public function allGradStudents()
    {
        return $this->gradStudents()->with(['my_class', 'user'])->get()->sortBy('user.name');
    }

    public function createRecord($data)
    {
        return StudentRecord::create($data);
    }

    public function updateRecord($id, array $data)
    {
        return StudentRecord::find($id)->update($data);
    }

    public function update(array $where, array $data)
    {
        return StudentRecord::where($where)->update($data);
    }

    public function getRecord(array $data)
    {
        return $this->activeStudents()->where($data)->with('user');
    }

    public function getRecordByUserIDs($ids)
    {
        return $this->activeStudents()->whereIn('user_id', $ids)->with('user');
    }

    public function findByUserId($st_id)
    {
        return $this->getRecord(['user_id' => $st_id]);
    }

    public function getAll()
    {
        return $this->activeStudents()->with('user');
    }

    public function getGradRecord($data=[])
    {
        return $this->gradStudents()->where($data)->with('user');
    }

    public function exists($student_id)
    {
        return $this->getRecord(['user_id' => $student_id])->exists();
    }

    /************* Promotions *************/
    public function createPromotion(array $data)
    {
        return Promotion::create($data);
    }

    public function findPromotion($id)
    {
        return Promotion::find($id);
    }

    public function deletePromotion($id)
    {
        return Promotion::destroy($id);
    }

    public function getAllPromotions()
    {
        return Promotion::with(['student', 'fc', 'tc'])->where(['from_session' => Qs::getCurrentSession(), 'to_session' => Qs::getNextSession()])->get();
    }

    public function getPromotions(array $where)
    {
        return Promotion::where($where)->get();
    }

    public function getStudentStatuses()
    {
        return ['active', 'inactive', 'graduated', 'transferred', 'suspended', 'withdrawn'];
    }

    public function allStudents(array $where = [], $search = NULL, $status = NULL, $year = NULL, $class_id = NULL)
    {
        $q = StudentRecord::with(['user', 'my_class', 'my_parent']);
        if (!empty($where)) $q->where($where);
        if ($status) {
            $q->where('status', $status);
        } else {
            $q->where('status', '!=', 'graduated')->where('status', '!=', 'transferred');
        }
        if ($year) $q->where('year_admitted', $year);
        if ($class_id) $q->where('my_class_id', $class_id);
        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('adm_no', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('username', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }
        return $q->orderBy('my_class_id')->get();
    }

    /************* Guardians *************/
    public function guardians($student_id)
    {
        return StudentGuardian::where('student_id', $student_id)->get();
    }

    public function createGuardian($data)
    {
        return StudentGuardian::create($data);
    }

    public function deleteGuardian($id)
    {
        return StudentGuardian::destroy($id);
    }

    /************* Attendance *************/
    public function attendance($student_id)
    {
        return StudentAttendance::where('student_id', $student_id)->get();
    }

    public function getAttendance(array $where)
    {
        return StudentAttendance::where($where);
    }

    public function createAttendance(array $data)
    {
        return StudentAttendance::create($data);
    }

    public function updateOrCreateAttendance(array $attributes, array $data)
    {
        return StudentAttendance::updateOrCreate($attributes, $data);
    }

    public function deleteAttendance($id)
    {
        return StudentAttendance::destroy($id);
    }

    /************* Documents *************/
    public function documents($student_id)
    {
        return StudentDocument::where('student_id', $student_id)->orderByDesc('id')->get();
    }

    public function createDocument($data)
    {
        return StudentDocument::create($data);
    }

    public function findDocument($id)
    {
        return StudentDocument::find($id);
    }

    public function deleteDocument($id)
    {
        return StudentDocument::destroy($id);
    }

    /************* Discipline *************/
    public function discipline($student_id)
    {
        return StudentDiscipline::where('student_id', $student_id)->orderByDesc('date')->get();
    }

    public function createDiscipline($data)
    {
        return StudentDiscipline::create($data);
    }

    public function findDiscipline($id)
    {
        return StudentDiscipline::find($id);
    }

    public function deleteDiscipline($id)
    {
        return StudentDiscipline::destroy($id);
    }

    /************* Health *************/
    public function health($student_id)
    {
        return StudentHealth::where('student_id', $student_id)->first();
    }

    public function updateOrCreateHealth(array $attributes, array $data)
    {
        return StudentHealth::updateOrCreate($attributes, $data);
    }

    /************* Transport *************/
    public function transport($student_id)
    {
        return StudentTransport::where('student_id', $student_id)->first();
    }

    public function updateOrCreateTransport(array $attributes, array $data)
    {
        return StudentTransport::updateOrCreate($attributes, $data);
    }

    /************* Activities *************/
    public function activities($student_id)
    {
        return StudentActivity::where('student_id', $student_id)->orderByDesc('id')->get();
    }

    public function createActivity($data)
    {
        return StudentActivity::create($data);
    }

    public function findActivity($id)
    {
        return StudentActivity::find($id);
    }

    public function deleteActivity($id)
    {
        return StudentActivity::destroy($id);
    }

    /************* Events / History *************/
    public function logEvent($student_id, $event_type, $description, $meta = NULL)
    {
        return StudentEvent::create([
            'student_id' => $student_id,
            'event_type' => $event_type,
            'description' => $description,
            'meta' => $meta,
            'recorded_by' => auth()->id(),
        ]);
    }

    public function events($student_id)
    {
        return StudentEvent::where('student_id', $student_id)->with('recorder')->orderByDesc('id')->get();
    }

}
