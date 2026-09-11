<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected $my_class, $student;

    public function __construct(MyClassRepo $my_class, StudentRepo $student)
    {
        $this->middleware('teamSA');

        $this->my_class = $my_class;
        $this->student = $student;
    }

    public function promotion($fc = NULL, $tc = NULL)
    {
        $d['old_year'] = $old_yr = Qs::getSetting('current_session');
        $d['new_year'] = Qs::getNextSession();
        $d['my_classes'] = $this->my_class->all();
        $d['selected'] = false;

        if($fc && $tc){
            $d['selected'] = true;
            $d['fc'] = $fc;
            $d['tc'] = $tc;
            $d['students'] = $sts = $this->student->getRecord(['my_class_id' => $fc, 'session' => $d['old_year']])->get();

            if($sts->count() < 1){
                return redirect()->route('students.promotion')->with('flash_success', __('msg.nstp'));
            }
        }

        return view('pages.support_team.students.promotion.index', $d);
    }

    public function selector(Request $req)
    {
        return redirect()->route('students.promotion', [$req->fc, $req->tc]);
    }

    public function promote(Request $req, $fc, $tc)
    {
        $oy = Qs::getSetting('current_session'); $d = [];
        $ny = Qs::getNextSession();
        $students = $this->student->getRecord(['my_class_id' => $fc, 'session' => $oy ])->get()->sortBy('user.name');

        if($students->count() < 1){
            return redirect()->route('students.promotion')->with('flash_danger', __('msg.srnf'));
        }

        foreach($students as $st){
            $p = 'p-'.$st->id;
            $p = $req->$p;
            if($p === 'P'){ // Promote
                $d['my_class_id'] = $tc;
                $d['session'] = $ny;
            }
            if($p === 'D'){ // Don't Promote
                $d['my_class_id'] = $fc;
                $d['session'] = $ny;
            }
            if($p === 'G'){ // Graduated
                $d['my_class_id'] = $fc;
                $d['grad'] = 1;
                $d['grad_date'] = $oy;
            }

            $this->student->updateRecord($st->id, $d);

//            Insert New Promotion Data
            $promote['from_class'] = $fc;
            $promote['grad'] = ($p === 'G') ? 1 : 0;
            $promote['to_class'] = in_array($p, ['D', 'G']) ? $fc : $tc;
            $promote['student_id'] = $st->user_id;
            $promote['from_session'] = $oy;
            $promote['to_session'] = $ny;
            $promote['status'] = $p;

            $this->student->createPromotion($promote);
        }
        return redirect()->route('students.promotion')->with('flash_success', __('msg.update_ok'));
    }

    public function manage()
    {
        $data['promotions'] = $this->student->getAllPromotions();
        $data['old_year'] = Qs::getCurrentSession();
        $data['new_year'] = Qs::getNextSession();

        return view('pages.support_team.students.promotion.reset', $data);
    }

    public function reset($promotion_id)
    {
        $this->reset_single($promotion_id);

        return redirect()->route('students.promotion_manage')->with('flash_success', __('msg.update_ok'));
    }

    public function reset_all()
    {
        $next_session = Qs::getNextSession();
        $where = ['from_session' => Qs::getCurrentSession(), 'to_session' => $next_session];
        $proms = $this->student->getPromotions($where);

        if ($proms->count()){
          foreach ($proms as $prom){
              $this->reset_single($prom->id);

              // Delete Marks if Already Inserted for New Session
              $this->delete_old_marks($prom->student_id, $next_session);
          }
        }

        return Qs::jsonUpdateOk();
    }

    protected function delete_old_marks($student_id, $year)
    {
        Mark::where(['student_id' => $student_id, 'year' => $year])->delete();
    }

    protected function reset_single($promotion_id)
    {
        $prom = $this->student->findPromotion($promotion_id);

        $data['my_class_id'] = $prom->from_class;
        $data['session'] = $prom->from_session;
        $data['grad'] = 0;
        $data['grad_date'] = null;

        $this->student->update(['user_id' => $prom->student_id], $data);

        return $this->student->deletePromotion($promotion_id);
    }
}