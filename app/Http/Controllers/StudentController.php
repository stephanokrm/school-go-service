<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Responsible;
use App\Models\School;
use App\Models\Student;
use App\Services\AddressService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentController extends Controller
{
    public function __construct(
        private readonly AddressService $addressService
    )
    {
    }

    /**
     * @param Request $request
     * @return StudentResource
     */
    public function index(Request $request): StudentResource
    {
        $students = Student::query()
            ->when($request->filled('morning'), function (Builder $builder) use ($request) {
                $builder->orWhere('morning', $request->input('morning'));
            })
            ->when($request->filled('afternoon'), function (Builder $builder) use ($request) {
                $builder->orWhere('afternoon', $request->input('afternoon'));
            })
            ->when($request->filled('night'), function (Builder $builder) use ($request) {
                $builder->orWhere('night', $request->input('night'));
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return new StudentResource($students);
    }

    /**
     * @param StoreStudentRequest $request
     * @return StudentResource
     */
    public function store(StoreStudentRequest $request): StudentResource
    {
        $responsible = Responsible::query()->findOrFail($request->input('responsible.id'));
        $school = School::query()->findOrFail($request->input('school.id'));

        $address = $this->addressService->store(collect($request->input('address')));

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

        $this->addressService->update($student->getAddress(), collect($request->input('address')));

        $student->fill($student->getFillable());
        $student->responsible()->associate($responsible);
        $student->school()->associate($school);
        $student->save();

        return new StudentResource($student);
    }

    /**
     * @param Student $student
     * @return bool|null
     */
    public function destroy(Student $student): ?bool
    {
        return $student->delete();
    }

    /**
     * @param Request $request
     * @return StudentResource
     */
    public function trips(Request $request): StudentResource {
        $students = Student::query()
            ->where('responsible_id', $request->user()->responsible->id)
            ->whereRelation('trips', 'arrive_at', Carbon::today())
            ->get();

        return new StudentResource($students);
    }
}
