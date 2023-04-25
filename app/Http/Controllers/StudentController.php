<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\SchoolResource;
use App\Http\Resources\StudentResource;
use App\Models\Address;
use App\Models\Responsible;
use App\Models\School;
use App\Models\Student;

class StudentController extends Controller
{
    /**
     * @return StudentResource
     */
    public function index(): StudentResource
    {
        return new StudentResource(Student::query()->orderBy('first_name')->orderBy('last_name')->get());
    }

    /**
     * @param StoreStudentRequest $request
     * @return StudentResource
     */
    public function store(StoreStudentRequest $request): StudentResource
    {
        $responsible = Responsible::query()->findOrFail($request->input('responsible.id'));
        $school = School::query()->findOrFail($request->input('school.id'));

        $address = new Address();
        $address->fill($request->input('address'));
        $address->save();

        $student = new Student();
        $student->fill($request->all());
        $student->address()->associate($address);
        $student->responsible()->associate($responsible);
        $student->school()->associate($school);
        $student->save();

        return new StudentResource($student);
    }

    /**
     * @param Student $student
     * @return StudentResource
     */
    public function show(Student $student): StudentResource
    {
        return new StudentResource($student);
    }

    /**
     * @param UpdateStudentRequest $request
     * @param Student $student
     * @return StudentResource
     */
    public function update(UpdateStudentRequest $request, Student $student): StudentResource
    {
        $responsible = Responsible::query()->findOrFail($request->input('responsible.id'));
        $school = School::query()->findOrFail($request->input('school.id'));

        $student->address->fill($request->input('address'));
        $student->address->save();
        $student->fill($request->except('address', 'responsible', 'school'));
        $student->responsible()->associate($responsible);
        $student->school()->associate($school);
        $student->save();

        return new StudentResource($student);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}
