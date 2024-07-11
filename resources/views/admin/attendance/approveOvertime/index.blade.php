@extends('admin.master')
@section('content')
@section('title')
    @lang('approve_overtime.approval_list')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        {{-- <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('approveOvertime.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('approve_overtime.add_overtime_approval')</a>
        </div> --}}
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif



                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open(['route' => 'approveOvertime.index', 'id' => 'approveOvertime', 'method' => 'POST']) }}
                                <div class="form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-md-3">
                                        <div class="form-group branchName">
                                            <label class="control-label" for="email">@lang('common.employee')<span
                                                    class="validateRq">*</span></label>
                                            <select class="form-control finger_print_id select2 required" required
                                                name="finger_print_id">
                                                @foreach ($employeeList as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ $key == $finger_print_id ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>



                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label" for="date">@lang('common.month')<span
                                                    class="validateRq">*</span>:</label>
                                            <input type="text" class="form-control monthField required"
                                                style="height: 35px;" readonly placeholder="@lang('common.date')"
                                                id="date" name="month"
                                                value="@if (isset($month)) {{ $month }}@else {{ date('Y-m') }} @endif"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <input type="submit" id="filter" style="margin-top: 25px;"
                                                class="btn btn-info" value="@lang('common.filter')">
                                        </div>
                                    </div>

                                </div>
                                {{ Form::close() }}

                            </div>
                        </div>
                        <br>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        {{-- <th >@lang('approve_overtime.branch')</th> --}}
                                        <th>@lang('approve_overtime.date')</th>
                                        <th>@lang('approve_overtime.employee_id')</th>
                                        <th>@lang('approve_overtime.employee_name')</th>
                                        <th>@lang('attendance.in_time')</th>
                                        <th>@lang('attendance.out_time')</th>
                                        <th>@lang('attendance.over_time')</th>
                                        <th>@lang('payroll.gross_salary')</th>
                                        <th>@lang('payroll.per_day_salary')</th>
                                        <th>@lang('payroll.overtime_amount')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <td style="width: 50px;">{!! ++$sl !!}</td>
                                        {{-- <td style="width: 50px;">{!! $value->branch->branch_name !!}</td> --}}
                                        <td style="width: 50px;">{!! DateConvertDBToFOrm($value->date) !!}</td>
                                        <td style="width: 50px;">{!! $value->finger_print_id !!}</td>
                                        <td style="width: 50px;">{!! $value->employee->first_name . ' ' . $value->employee->last_name !!}</td>
                                        <td style="width: 50px;">{!! dateTimeToTime($value->in_time) !!}</td>
                                        <td style="width: 50px;">{!! dateTimeToTime($value->out_time) !!}</td>

                                        <td style="width: 50px; "> <span
                                                style="color:{{ $value->approved_over_time ? 'green' : 'red' }}">{!! round(decimalHours($value->approved_over_time ?? ($value->over_time ?? '00:00:00')), 2) !!}</span>
                                        </td>
                                        {{-- 'housing_allowance',
                                        'utility_allowance', 'transport_allowance', 'living_allowance',
                                        'mobile_allowance',   'special_allowance', 'education_and_club_allowance', 'membership_allowance' --}}
                                        @php
                                            $gross_salary =
                                                $value->employee->basic_salary +
                                                $value->employee->housing_allowance +
                                                $value->employee->utility_allowance +
                                                $value->employee->transport_allowance +
                                                $value->employee->living_allowance +
                                                $value->employee->mobile_allowance +
                                                $value->employee->special_allowance +
                                                $value->employee->education_and_club_allowance +
                                                $value->employee->membership_allowance;
                                            $per_day_amount = $gross_salary / 31 / 9;
                                            // if ($value->over_time) {
                                            //     dd($gross_salary, $per_day_amount);
                                            // }
                                        @endphp
                                        <td style="width: 50px;">{!! $gross_salary !!}</td>
                                        <td style="width: 50px;">{!! round($per_day_amount, 6) !!}</td>
                                        <td style="width: 50px;">{!! round($per_day_amount * decimalHours($value->approved_over_time ?? ($value->over_time ?? '00:00:00')), 3) !!}</td>

                                        <td style="width: 100px;">
                                            <a href="{!! route('approveOvertime.edit', $value->employee_attendance_id) !!}" class="btn btn-success btn-xs btnColor">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                            {{-- <a href="{!! route('approveOvertime.delete', $value->approve_over_time_id) !!}" data-token="{!! csrf_token() !!}"
                                                data-id="{!! $value->approve_over_time_id !!}"
                                                class="delete btn btn-danger btn-xs deleteBtn btnColor"><i
                                                    class="fa fa-trash-o" aria-hidden="true"></i></a> --}}
                                        </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
