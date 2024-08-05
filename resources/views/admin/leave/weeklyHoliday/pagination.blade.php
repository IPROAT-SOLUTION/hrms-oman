{{-- Checkbox data pagination --}}
@php
    use App\Model\Employee;
    use App\Model\WeeklyHoliday;
@endphp
<div class="row">
    <div>
        @if (count($employeeList) != 0)
            @forelse ($employeeList as $key => $employee)
                <div class="list" role="listbox" tabindex="0" aria-label="email list" style="font-weight: 400;">
                    <ul>
                        <li tabindex="-1" role="option" aria-checked="false"
                            style="background: #FFFFFF; color: black; text-align: left; border: 1px solid #cccccc">
                            <div class="form-check checkbox-style" style="width: 100%;">
                                <input data-id="{{ $employee->employee_id }}" value="{{ $employee->employee_id }}"
                                    name="employee_id[]" tabindex="-1" id="employee_id"
                                    class="form-check-input employee_id pull-right" type="checkbox"
                                    {{ strtotime(date('Y-m', strtotime($employee->weekoff_updated_at))) == strtotime(date('Y-m')) ? 'checked' : '' }}>
                                @php
                                    $employeeWeekOff = Employee::where('employee_id', $employee->employee_id)
                                        ->select('weekoff_updated_at')
                                        ->first();

                                    if ($employeeWeekOff['weekoff_updated_at'] != null) {
                                        $dayName = WeeklyHoliday::where('employee_id', $employee->employee_id)
                                            ->select('day_name')
                                            ->orderBy('week_holiday_id', 'desc')
                                            ->first();
                                        $monthOfHoliday = date('F', strtotime($employeeWeekOff['weekoff_updated_at']));
                                    } else {
                                        $dayName = ['day_name' => ''];
                                        $monthOfHoliday = '';
                                    }
                                @endphp
                                {{ $employee->finger_id }} <br>
                                {{ trim($employee->first_name . ' ' . $employee->last_name) }}<br>
                                @if (isset($dayName['day_name']))
                                    @if ($monthOfHoliday == date('F', strtotime(date('Y-m-d H:i:s'))))
                                        @php
                                            $day = str_replace('[', '', $dayName['day_name']);
                                            $day = str_replace(']', '', $day);
                                            $day = str_replace('"', '', $day);
                                        @endphp
                                        <p style="color: #68B984"> {{ $day . '-' . $monthOfHoliday }}</p>
                                    @else
                                        <p style="color: orange"> {{ $dayName['day_name'] . '-' . $monthOfHoliday }}</p>
                                    @endif
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            @empty
                <div class="list" role="listbox" tabindex="0" aria-label="email list">
                    <ul>
                        <li>
                            <p><b>No Results Found...</b></p>
                        </li>
                    </ul>
                </div>
            @endforelse
        @endif
    </div>

    <div class="text-right">
        {{ $employeeList->links() }}
    </div>
</div>
