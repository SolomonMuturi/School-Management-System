<?php

namespace App\Repositories;

use App\Helpers\Qs;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Curriculum;
use App\Models\CurriculumTopic;
use App\Models\Lesson;
use App\Models\TeacherAssignment;
use App\Models\Setting;
use App\User;
use Illuminate\Support\Facades\DB;

class AcademicRepo
{
    /*********************** Academic Years & Terms ***********************/

    public function currentYear()
    {
        $year = AcademicYear::where('is_current', 1)->first();
        if (!$year) {
            $session = Qs::getCurrentSession() ?: (date('Y').'-'.(date('Y') + 1));
            $year = AcademicYear::firstOrCreate(
                ['name' => $session],
                ['is_current' => 1, 'status' => 'active', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+1 year'))]
            );
            $year->update(['is_current' => 1]);
        }

        return $year;
    }

    public function currentTerm()
    {
        $term = AcademicTerm::where('is_current', 1)->first();
        return $term ?: AcademicTerm::orderBy('sequence')->first();
    }

    public function allYears()
    {
        return AcademicYear::orderByDesc('id')->get();
    }

    public function allTerms()
    {
        return AcademicTerm::orderBy('sequence')->get();
    }

    public function createYear($data)
    {
        return AcademicYear::create($data);
    }

    public function updateYear($id, $data)
    {
        return AcademicYear::find($id)->update($data);
    }

    public function deleteYear($id)
    {
        return AcademicYear::destroy($id);
    }

    public function createTerm($data)
    {
        return AcademicTerm::create($data);
    }

    public function setCurrentTerm($id)
    {
        DB::table('academic_terms')->update(['is_current' => 0]);
        $term = AcademicTerm::find($id);
        $term->update(['is_current' => 1]);

        // System Settings remains the single source of truth for the current term.
        Setting::updateOrCreate(['type' => 'current_term'], ['description' => (string)$term->sequence]);

        return true;
    }

    public function setCurrentYear($id)
    {
        DB::table('academic_years')->update(['is_current' => 0]);
        $year = AcademicYear::find($id);
        $year->update(['is_current' => 1]);

        // System Settings remains the single source of truth for the current session.
        Setting::updateOrCreate(['type' => 'current_session'], ['description' => $year->name]);

        return true;
    }

    public function yearTerms($year_id)
    {
        return AcademicYear::find($year_id)->terms()
            ->withPivot('start_date', 'end_date', 'status')->orderBy('sequence')->get();
    }

    public function attachTermToYear($year_id, $term_id, $data = [])
    {
        $year = AcademicYear::find($year_id);
        return $year->terms()->syncWithoutDetaching([$term_id => $data]);
    }

    /*********************** Curriculum ***********************/

    public function allCurriculums()
    {
        return Curriculum::with(['academicYear', 'academicTerm'])->orderByDesc('id')->get();
    }

    public function findCurriculum($id)
    {
        return Curriculum::with(['subjects', 'topics'])->find($id);
    }

    public function createCurriculum($data)
    {
        return Curriculum::create($data);
    }

    public function deleteCurriculum($id)
    {
        return Curriculum::destroy($id);
    }

    public function attachSubjectToCurriculum($curriculum_id, $subject_id, $class_id = NULL)
    {
        $curriculum = Curriculum::find($curriculum_id);
        return $curriculum->subjects()->syncWithoutDetaching([$subject_id => ['my_class_id' => $class_id]]);
    }

    public function detachSubjectFromCurriculum($curriculum_id, $subject_id)
    {
        return Curriculum::find($curriculum_id)->subjects()->detach($subject_id);
    }

    public function createCurriculumTopic($data)
    {
        return CurriculumTopic::create($data);
    }

    public function deleteCurriculumTopic($id)
    {
        return CurriculumTopic::destroy($id);
    }

    /*********************** Teacher & Subject Assignment ***********************/

    public function allTeacherAssignments()
    {
        return TeacherAssignment::with(['teacher', 'subject', 'myClass', 'academicYear', 'academicTerm'])->orderByDesc('id')->get();
    }

    public function createTeacherAssignment($data)
    {
        return TeacherAssignment::firstOrCreate(
            ['teacher_id' => $data['teacher_id'], 'subject_id' => $data['subject_id'], 'academic_year_id' => $data['academic_year_id'] ?? NULL],
            $data
        );
    }

    public function deleteTeacherAssignment($id)
    {
        return TeacherAssignment::destroy($id);
    }

    public function teacherAssignments($teacher_id)
    {
        return TeacherAssignment::with(['subject', 'myClass', 'academicYear', 'academicTerm'])
            ->where('teacher_id', $teacher_id)->get();
    }

    /*********************** Lessons ***********************/

    public function allLessons()
    {
        return Lesson::with(['teacher', 'subject', 'myClass', 'academicYear', 'academicTerm'])->orderByDesc('lesson_date')->orderByDesc('id')->get();
    }

    public function createLesson($data)
    {
        return Lesson::create($data);
    }

    public function deleteLesson($id)
    {
        return Lesson::destroy($id);
    }

    /*********************** Assignments & Homework ***********************/

    public function allAssignments()
    {
        return Assignment::with(['teacher', 'subject', 'myClass', 'academicYear', 'academicTerm', 'submissions'])->orderByDesc('id')->get();
    }

    public function studentAssignments($student_id, $class_id = NULL)
    {
        $q = Assignment::with(['teacher', 'subject', 'myClass', 'submissions'])
            ->when($class_id, function ($query) use ($class_id) { return $query->where('my_class_id', $class_id); })
            ->orderByDesc('due_date');
        return $q->get();
    }

    public function findAssignment($id)
    {
        return Assignment::with(['submissions.student', 'subject', 'myClass', 'teacher'])->find($id);
    }

    public function createAssignment($data)
    {
        return Assignment::create($data);
    }

    public function deleteAssignment($id)
    {
        return Assignment::destroy($id);
    }

    public function createSubmission($data)
    {
        return AssignmentSubmission::firstOrCreate(
            ['assignment_id' => $data['assignment_id'], 'student_id' => $data['student_id']],
            $data
        );
    }

    public function submissions($assignment_id)
    {
        return AssignmentSubmission::with('student')->where('assignment_id', $assignment_id)->orderByDesc('id')->get();
    }

    public function findSubmission($id)
    {
        return AssignmentSubmission::find($id);
    }

    public function gradeSubmission($id, $data)
    {
        return AssignmentSubmission::find($id)->update($data);
    }

    /*********************** Teachers / Students / Quick Data ***********************/

    public function allTeachers()
    {
        return User::where('user_type', 'teacher')->orderBy('name')->get();
    }

    public function teachersWithSubjects()
    {
        return User::where('user_type', 'teacher')
            ->with(['subjects' => function ($q) { $q->with('my_class'); }])
            ->orderBy('name')->get();
    }

    public function studentsCount()
    {
        return \App\Models\StudentRecord::where('status', 'active')->count();
    }

    public function performanceSummary($year = NULL)
    {
        $year = $year ?: Qs::getCurrentSession();
        $marks = DB::table('marks')
            ->where('year', $year)
            ->selectRaw('AVG(tca) as avg_tca, AVG(exm) as avg_exm, AVG(tca + exm) as avg_total, COUNT(*) as count')
            ->first();

        return [
            'avg_tca' => round((float)$marks->avg_tca, 1),
            'avg_exm' => round((float)$marks->avg_exm, 1),
            'avg_total' => round((float)$marks->avg_total, 1),
            'count' => $marks->count,
        ];
    }

    public function classPerformance($class_id, $year = NULL)
    {
        $year = $year ?: Qs::getCurrentSession();
        return DB::table('marks')
            ->where('year', $year)->where('my_class_id', $class_id)
            ->selectRaw('subject_id, AVG(tca) as avg_tca, AVG(exm) as avg_exm, AVG(tca + exm) as avg_total, COUNT(DISTINCT student_id) as students')
            ->groupBy('subject_id')->get();
    }
}