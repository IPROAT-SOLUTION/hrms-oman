<?php

use App\Model\Employee;
use App\Model\Device;
?>
@extends('admin.master')
@section('content')
@section('title')
    @lang('dashboard.dashboard')
@endsection
<style>
    .dash_image {
        width: 60px;
    }

    .my-custom-scrollbar {
        position: relative;
        height: 280px;
        overflow: auto;
    }

    .table-wrapper-scroll-y {
        display: block;
    }

    tbody {
        display: block;
        height: 300px;
        overflow: auto;
    }

    thead,
    tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    thead {
        width: calc(100%);
    }

    .leaveApplication {
        overflow-x: hidden;
        height: 210px;
    }

    .noticeBord {
        overflow-x: hidden;
        height: 210px;
    }

    .btn-success-custom {
        overflow-x: hidden;
        background-color: green;
        color: white;
        border: none;
        padding: 10px 20px;
    }


    .preloader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        /* background: url('../images/timer.gif') 50% 50% no-repeat rgb(249, 249, 249); */
        opacity: .8;
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    /* .scroll-hide::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .scroll-hide {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    */
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a>
                </li>
            </ol>
        </div>
        <div class="pull-right" style="margin-right:12px;" hidden>
            <input data-id="{{ $setting_sync_live->id }}" class="toggle-class" type="checkbox" data-onstyle="info"
                data-offstyle="#3f729b" data-toggle="toggle" data-on="LIVE ON" data-off="LIVE OFF"
                {{ $setting_sync_live->status ? 'checked' : '' }}>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title"> @lang('dashboard.total_employee') </h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/employee.png') }}">
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-warning"></i> <span
                            class="counter text-warning">{{ $totalEmployee }}</span></li>
                </ul>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">@lang('dashboard.total_present')</h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/present.png') }}">
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-info"></i> <span
                            class="counter text-info">{{ $totalAttendance }}</span></li>
                </ul>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">@lang('dashboard.total_absent')</h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/absent.png') }}">
                    </li>
                    <li class="text-right"><a href="#"><i id="absentDetail"
                                class="ti-arrow-down text-danger"></i></a>
                        <span class="counter text-danger">{{ $totalAbsent }}</span>
                    </li>
                </ul>

            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="white-box analytics-info">
                <h3 class="box-title"> leave</h3>
                <ul class="list-inline two-part">
                    <li>
                        <img class="dash_image" src="{{ asset('admin_assets/img/department.png') }}">
                    </li>
                    <li class="text-right"><i class="ti-arrow-up text-purple"></i> <span
                            class="counter text-purple">{{ $totalLeave ?? 0 }}</span></li>
                </ul>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12" style="display:inline-table;">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>
                    @lang('dashboard.today_attendance')
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless">
                                <thead>
                                    <tr>
                                        <th class="text-left" style="width:100px;">Sl.No</th>
                                        <th class="text-left" style="width:150px;">@lang('dashboard.photo')</th>
                                        <td class="text-left" style="width:150px;">Employee ID</td>
                                        <td class="text-left">Employee Name</td>
                                        <td class="text-left">Date-Time</td>
                                        <td class="text-left">Device</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($attendanceData) > 0)
                                        {{ $dailyAttendanceSl = null }}
                                        @foreach ($attendanceData as $dailyAttendance)
                                            <tr>
                                                <td class="text-left" style="width:100px;">{{ ++$dailyAttendanceSl }}
                                                </td>
                                                <td class="text-left" style="width:150px;">
                                                    @if (isset($dailyAttendance->photo) && $dailyAttendance->photo != '')
                                                        <img height="40" src="{!! asset('uploads/employeePhoto/' . $dailyAttendance->photo) !!}" alt="user-img"
                                                            class="img-circle">
                                                    @else
                                                        <img height="40" src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img"
                                                            class="img-circle">
                                                    @endif
                                                </td>
                                                <td class="text-left" style="width:150px;">
                                                    <p>{{ $dailyAttendance->ID }}</p>
                                                </td>
                                                <td class="text-left">
                                                    {{ $dailyAttendance->employeeData->first_name . ' ' . $dailyAttendance->employeeData->last_name }}
                                                </td>
                                                <td class="text-left">{{ $dailyAttendance->datetime }}</td>
                                                <td class="text-left">{{ $dailyAttendance->device_name ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="text-center">
                                            <td colspan="8">@lang('common.no_data_available')</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @if ($ip_attendance_status == 1)
            <!-- employe attendance  -->
            @php
                $logged_user = employeeInfo();
            @endphp

            <div class="col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Hey {!! $logged_user[0]->user_name !!} please Check in/out your attendance</h3>
                    <hr>
                    <div class="noticeBord">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">x</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">x</button>
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <form action="{{ route('ip.attendance') }}" method="POST">
                            {{ csrf_field() }}
                            <p>Your IP is {{ \Request::ip() }}</p>
                            {{-- <p>Your IP is {{ getIp() }}</p> --}}
                            <input type="hidden" name="employee_id" value="{{ $logged_user[0]->user_name }}">

                            <input type="hidden" name="ip_check_status" value="{{ $ip_check_status }}">

                            <input type="hidden" name="finger_id" value="{{ $logged_user[0]->finger_id }}">
                            @if ($count_user_login_today > 0 && $count_user_login_today % 2 != 0)
                                <button class="btn btn-danger">
                                    <i class="fa fa-clock-o"> </i>
                                    Check Out
                                </button>
                            @else
                                <button class="btn btn-success">
                                    <i class="fa fa-clock-o"> </i>
                                    Check In
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- end attendance  -->
        @endif
       


        @if (count($leaveApplication) > 0)
            <div class="col-md-12 col-lg-6 col-sm-12">
                <div class="white-box">
                    <h3 class="box-title">@lang('dashboard.recent_leave_application')</h3>
                    <hr>
                    <div class="leaveApplication">
                        @foreach ($leaveApplication as $leaveApplication)
                            <div class="comment-center p-t-10 {{ $leaveApplication->leave_application_id }}">
                                <div class="comment-body">
                                    @if ($leaveApplication->employee->photo != '')
                                        <div class="user-img"> <img src="{!! asset('uploads/employeePhoto/' . $leaveApplication->employee->photo) !!}" alt="user"
                                                class="img-circle"></div>
                                    @else
                                        <div class="user-img"> <img src="{!! asset('admin_assets/img/default.png') !!}" alt="user"
                                                class="img-circle"></div>
                                    @endif
                                    <div class="mail-contnet">
                                        @php
                                            $d = strtotime($leaveApplication->created_at);
                                        @endphp
                                        <h5>{{ $leaveApplication->employee->first_name }}
                                            {{ $leaveApplication->employee->last_name }}
                                        </h5><span class="time">{{ date(' d M Y h:i: a', $d) }}</span> <span
                                            class="label label-rouded label-info">PENDING</span>
                                        <br /><span class="mail-desc" style="max-height: none">
                                            @lang('leave.leave_type') :
                                            {{ $leaveApplication->leaveType->leave_type_name }}<br>
                                            @lang('leave.request_duration') :
                                            {{ dateConvertDBtoForm($leaveApplication->application_from_date) }} To
                                            {{ dateConvertDBtoForm($leaveApplication->application_to_date) }}<br>
                                            @lang('leave.number_of_day') : {{ $leaveApplication->number_of_day }} <br>
                                            @lang('leave.purpose') : {{ $leaveApplication->purpose }}<br>
                                            @lang('leave.document') :@if ($leaveApplication->document)
                                                <a class="btn btn-default btn-xs"
                                                    href="{{ asset('/uploads/leave_document/' . $leaveApplication->document) }}"
                                                    download>
                                                    <i class="fa fa-download" style="margin: 0 6px;"></i>
                                                </a>

                                                <a class="btn btn-default btn-xs"
                                                    href="{{ url('viewLeaveApplication', $leaveApplication->leave_application_id) }}"
                                                    target="_blank">
                                                    <i class="fa fa-eye" style="margin: 0 6px;">
                                                    </i>
                                                </a>
                                            @else
                                                <span class="text-warning">No document</span>
                                            @endif
                                        </span>

                                        {!! Form::textarea(
                                            'remarks',
                                            old('remarks'),
                                            $attributes = [
                                                'style' => 'width: 90%',
                                                'class' => 'form-control remarks',
                                                'id' => 'leaveRemark',
                                                'placeholder' => __('remarks'),
                                                'cols' => '5',
                                                'rows' => '3',
                                            ],
                                        ) !!}

                                        <br>
                                        @if ($leaveApplication->manager_status == 1)
                                            <a href="javacript:void(0)" data-status=2
                                                data-leave_application_id="{{ $leaveApplication->leave_application_id }}"
                                                class="btn remarksForManagerLeave btn btn-rounded btn-success btn-outline m-r-5"><i
                                                    class="ti-check text-success m-r-5"></i>@lang('common.approve')</a>
                                            <a href="javacript:void(0)" data-status=3
                                                data-leave_application_id="{{ $leaveApplication->leave_application_id }}"
                                                class="btn-rounded remarksForManagerLeave btn btn-danger btn-outline"><i
                                                    class="ti-close text-danger m-r-5"></i> @lang('common.reject')</a>
                                        @elseif($leaveApplication->status == 1 && $leaveApplication->manager_status == 2)
                                            <a href="javacript:void(0)" data-status=2
                                                data-leave_application_id="{{ $leaveApplication->leave_application_id }}"
                                                class="btn remarksForLeave btn btn-rounded btn-success-custom btn-outline m-r-5"><i
                                                    class="ti-check text-success-custom m-r-5"></i>@lang('common.approve')</a>
                                            <a href="javacript:void(0)" data-status=3
                                                data-leave_application_id="{{ $leaveApplication->leave_application_id }}"
                                                class="btn-rounded remarksForLeave btn btn-danger btn-outline"><i
                                                    class="ti-close text-danger m-r-5"></i> @lang('common.reject')</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if (count($permissionApplication) > 0)
            <div class="col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Recent Permission Requests</h3>
                    <hr>
                    <div class="leaveApplication">
                        @foreach ($permissionApplication as $leaveApplication)
                            <div class="comment-center p-t-10 {{ $leaveApplication->leave_permission_id }}">
                                <div class="comment-body">
                                    @if ($leaveApplication->employee->photo != '')
                                        <div class="user-img"> <img src="{!! asset('uploads/employeePhoto/' . $leaveApplication->employee->photo) !!}" alt="user"
                                                class="img-circle"></div>
                                    @else
                                        <div class="user-img"> <img src="{!! asset('admin_assets/img/default.png') !!}" alt="user"
                                                class="img-circle"></div>
                                    @endif
                                    <div class="mail-contnet">
                                        @php
                                            $d = strtotime($leaveApplication->created_at);
                                        @endphp
                                        <h5>{{ $leaveApplication->employee->first_name }}
                                            {{ $leaveApplication->employee->last_name }}
                                        </h5><span class="time">{{ date('d M Y h:i: a', $d) }}</span>
                                        <span class="label label-rouded label-info">PENDING</span>
                                        <br /><span class="mail-desc" style="max-height: none">
                                            @lang('leave.date') :
                                            {{ dateConvertDBtoForm($leaveApplication->leave_permission_date) }}
                                            <br>
                                            @lang('leave.permission_duration') :
                                            {{ $leaveApplication->permission_duration }}
                                            <br>
                                            @lang('leave.purpose') :
                                            {{ $leaveApplication->leave_permission_purpose }}<br>
                                            {!! Form::textarea(
                                                'remarks',
                                                old('remarks'),
                                                $attributes = [
                                                    'style' => 'width: 90%',
                                                    'class' => 'form-control permissionRemarks',
                                                    'id' => 'managerPermissionRemark',
                                                    'placeholder' => __('remarks'),
                                                    'cols' => '5',
                                                    'rows' => '3',
                                                ],
                                            ) !!}
                                        </span>

                                        @if ($leaveApplication->manager_status == 1)
                                            <a href="javacript:void(0)" data-status=2
                                                data-leave_permission_id="{{ $leaveApplication->leave_permission_id }}"
                                                class="btn remarksForManagerPermission btn btn-rounded btn-success btn-outline m-r-5"><i
                                                    class="ti-check text-success m-r-5"></i>@lang('common.approve')</a>
                                            <a href="javacript:void(0)" data-status=3
                                                data-leave_permission_id="{{ $leaveApplication->leave_permission_id }}"
                                                class="btn-rounded remarksForManagerPermission btn btn-danger btn-outline"><i
                                                    class="ti-close text-danger m-r-5"></i>@lang('common.reject')</a>
                                        @elseif($leaveApplication->status == 1 && $leaveApplication->manager_status == 2)
                                            <a href="javacript:void(0)" data-status=2
                                                data-leave_application_id="{{ $leaveApplication->leave_permission_id }}"
                                                class="btn remarksForDepartmentHead btn btn-rounded btn-success btn-outline m-r-5"><i
                                                    class="ti-check text-success m-r-5"></i>@lang('common.approve')</a>
                                            <a href="javacript:void(0)" data-status=3
                                                data-leave_application_id="{{ $leaveApplication->leave_permission_id }}"
                                                class="btn-rounded remarksForDepartmentHead btn btn-danger btn-outline"><i
                                                    class="ti-close text-danger m-r-5"></i>@lang('common.reject')</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if (count($employeeDocumentExpiry) > 0)
            <div class="col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Employee Document Expire Soon List</h3>
                    <hr>
                    <div style="height: 210px; overflow-y: auto; overflow-x: hidden;">

                        @foreach ($employeeDocumentExpiry as $EData)
                            <div class="comment-center p-t-10">
                                <div class="comment-body">

                                    <div class="mail-contnet">
                                        <span class="mail-desc" style="max-height: none">
                                            @php $info=$ex_info=[]; @endphp
                                            <?php
                                            $date = DATE('Y-m-d');
                                            if (DATE('Y-m-d', strtotime($EData->expiry_date8 . '-1 MONTHS')) <= $date && $EData->expiry_date8 >= $date) {
                                                $info[] = expire('Passport', $EData->expiry_date8);
                                            } elseif (!is_null($EData->expiry_date8) && DATE('Y-m-d', strtotime($EData->expiry_date8 . '-1 MONTHS')) <= $date) {
                                                $ex_info[] = expire('Passport', $EData->expiry_date8);
                                            }
                                            
                                            if (DATE('Y-m-d', strtotime($EData->expiry_date9 . '-1 MONTHS')) <= $date && $EData->expiry_date9 >= $date) {
                                                $info[] = expire('Visa', $EData->expiry_date9);
                                            } elseif (!is_null($EData->expiry_date9) && DATE('Y-m-d', strtotime($EData->expiry_date9 . '-1 MONTHS')) <= $date) {
                                                $ex_info[] = expire('Visa', $EData->expiry_date9);
                                            }
                                            
                                            if (DATE('Y-m-d', strtotime($EData->expiry_date10 . '-1 MONTHS')) <= $date && $EData->expiry_date10 >= $date) {
                                                $info[] = expire('Driving License', $EData->expiry_date10);
                                            } elseif (!is_null($EData->expiry_date10) && DATE('Y-m-d', strtotime($EData->expiry_date10 . '-1 MONTHS')) <= $date) {
                                                $ex_info[] = expire('Driving License', $EData->expiry_date10);
                                            }
                                            
                                            if (DATE('Y-m-d', strtotime($EData->expiry_date11 . '-1 MONTHS')) <= $date && $EData->expiry_date11 >= $date) {
                                                $info[] = expire('Resident Card', $EData->expiry_date11);
                                            } elseif (!is_null($EData->expiry_date11) && DATE('Y-m-d', strtotime($EData->expiry_date11 . '-1 MONTHS')) <= $date) {
                                                $ex_info[] = expire('Resident Card', $EData->expiry_date11);
                                            }
                                            echo "<table class='table'>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <thead>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <tr  style='text-align:center;'><th>";
                                            if ($EData->photo != '') {
                                                echo '<div class="user-img"> <img src="' .
                                                    asset('uploads/employeePhoto/' . $EData->photo) .
                                                    '" alt="user"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                class="img-circle"></div>';
                                            } else {
                                                echo '<div class="user-img"> <img src="' .
                                                    asset('admin_assets/img/default.png') .
                                                    '" alt="user"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                class="img-circle"></div>';
                                            }
                                            echo '<th><th>' .
                                                strtoupper($EData->first_name . ' ' . $EData->last_name) .
                                                ' ( ' .
                                                $EData->finger_id .
                                                " )</th></tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <tr><th>Document</th><th>Date</th><th>Days</th></tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </thead>";
                                            foreach ($info as $key => $infoData) {
                                                echo '<tr><td>' . $infoData['doc'] . '</td><td>' . $infoData['date'] . '</td><td>' . $infoData['days'] . '</td></tr>';
                                            }
                                            echo '</table>';
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if (count($employeeDocumentExpired) > 0)
            <div class="col-md-6">
                <div class="white-box">
                    <h3 class="box-title">Employee Document Expired List</h3>
                    <hr>
                    <div style="height: 210px; overflow-y: auto; overflow-x: hidden;">

                        @foreach ($employeeDocumentExpired as $EData)
                            <div class="comment-center p-t-10">
                                <div class="comment-body">

                                    <div class="mail-contnet">
                                        <span class="mail-desc" style="max-height: none">
                                            @php $info=$ex_info=[]; @endphp
                                            <?php
                                            $date = DATE('Y-m-d');
                                            if (DATE('Y-m-d', strtotime($EData->expiry_date8 . '-1 MONTHS')) <= $date && $EData->expiry_date8 >= $date) {
                                                $info[] = expire('Passport', $EData->expiry_date8);
                                            } elseif (!is_null($EData->expiry_date8) && DATE('Y-m-d', strtotime($EData->expiry_date8 . '-1 MONTHS')) <= $date) {
                                                $ex_info[] = expire('Passport', $EData->expiry_date8);
                                            }
                                            
                                            if (DATE('Y-m-d', strtotime($EData->expiry_date9 . '-1 MONTHS')) <= $date && $EData->expiry_date9 >= $date) {
                                                $info[] = expire('Visa', $EData->expiry_date9);
                                            } elseif (!is_null($EData->expiry_date9) && DATE('Y-m-d', strtotime($EData->expiry_date9 . '-1 MONTHS')) <= $date) {
                                                $ex_info[] = expire('Visa', $EData->expiry_date9);
                                            }
                                            
                                            if (DATE('Y-m-d', strtotime($EData->expiry_date10 . '-1 MONTHS')) <= $date && $EData->expiry_date10 >= $date) {
                                                $info[] = expire('Driving License', $EData->expiry_date10);
                                            } elseif (!is_null($EData->expiry_date10) && DATE('Y-m-d', strtotime($EData->expiry_date10 . '-1 MONTHS')) <= $date) {
                                                $ex_info[] = expire('Driving License', $EData->expiry_date10);
                                            }
                                            
                                            if (DATE('Y-m-d', strtotime($EData->expiry_date11 . '-1 MONTHS')) <= $date && $EData->expiry_date11 >= $date) {
                                                $info[] = expire('Resident Card', $EData->expiry_date11);
                                            } elseif (!is_null($EData->expiry_date11) && DATE('Y-m-d', strtotime($EData->expiry_date11 . '-1 MONTHS')) <= $date) {
                                                $ex_info[] = expire('Resident Card', $EData->expiry_date11);
                                            }
                                            echo "<table class='table'>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <thead>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <tr  style='text-align:center;'><th>";
                                            if ($EData->photo != '') {
                                                echo '<div class="user-img"> <img src="' .
                                                    asset('uploads/employeePhoto/' . $EData->photo) .
                                                    '" alt="user"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                class="img-circle"></div>';
                                            } else {
                                                echo '<div class="user-img"> <img src="' .
                                                    asset('admin_assets/img/default.png') .
                                                    '" alt="user"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                class="img-circle"></div>';
                                            }
                                            echo '<th><th>' .
                                                strtoupper($EData->first_name . ' ' . $EData->last_name) .
                                                ' ( ' .
                                                $EData->finger_id .
                                                " )</th></tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <tr><th>Document</th><th>Date</th><th>Days ( Before )</th></tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </thead>";
                                            foreach ($ex_info as $key => $infoData) {
                                                echo '<tr><td>' . $infoData['doc'] . '</td><td>' . $infoData['date'] . '</td><td>' . $infoData['days'] . '</td></tr>';
                                            }
                                            echo '</table>';
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- up comming birthday  -->
        @if (count($upcoming_birtday) > 0)
            <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="white-box">
                    <h3 class="box-title">@lang('dashboard.upcoming_birthday')</h3>
                    <hr>
                    <div class="leaveApplication">
                        @foreach ($upcoming_birtday as $employee_birthdate)
                            <div class="comment-center p-t-10">
                                <div class="comment-body">
                                    @if ($employee_birthdate->photo != '')
                                        <div class="user-img"> <img src="{!! asset('uploads/employeePhoto/' . $employee_birthdate->photo) !!}" alt="user"
                                                class="img-circle"></div>
                                    @else
                                        <div class="user-img"> <img src="{!! asset('admin_assets/img/default.png') !!}" alt="user"
                                                class="img-circle"></div>
                                    @endif
                                    <div class="mail-contnet">

                                        @php
                                            $date_of_birth = $employee_birthdate->date_of_birth;
                                            $separate_date = explode('-', $date_of_birth);

                                            $date_current_year =
                                                date('Y') . '-' . $separate_date[1] . '-' . $separate_date[2];

                                            $create_date = date_create($date_current_year);
                                        @endphp

                                        <h5>{{ $employee_birthdate->first_name }}
                                            {{ $employee_birthdate->last_name }}</h5>
                                        <span
                                            class="time">{{ date_format(date_create($employee_birthdate->date_of_birth), 'D dS F') }}</span>
                                        <br />


                                        <span class="mail-desc">
                                            @if ($date_current_year == date('Y-m-d'))
                                                <b>Today is
                                                    @if ($employee_birthdate->gender == 0)
                                                        His
                                                    @else
                                                        Her
                                                    @endif
                                                    Birtday Wish
                                                    @if ($employee_birthdate->gender == 0)
                                                        Him
                                                    @else
                                                        Her
                                                    @endif
                                                </b>
                                            @else
                                                Wish
                                                @if ($employee_birthdate->gender == 0)
                                                    Him
                                                @else
                                                    Her
                                                @endif
                                                on {{ date_format($create_date, 'D dS F Y') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if (isset($employeeLeave))
            <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="white-box">
                    <h3 class="box-title">@lang('dashboard.leave_balance')</h3>
                    <hr>
                    <div class="leaveApplication">
                        @foreach ($employeeLeave ?? [] as $key => $leave)
                            <div class="comment-center p-t-10">
                                <div class="comment-body">
                                    <div class="mail-contnet">
                                        <div class="row">
                                            <p class="col-md-6">
                                                {{ $key + 1 }}. {!! isset($leave->leaveType) ? $leave->leaveType->leave_type_name : '' !!}
                                            </p>
                                            <p class="col-md-6">
                                                {!! isset($leave) ? $leave->leave_balance : '' !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

	 @if (count($notice) > 0)
            <div class="col-md-6">
                <div class="white-box">
                    <h3 class="box-title">@lang('dashboard.notice_board')</h3>
                    <hr>
                    <div class="noticeBord">
                        @foreach ($notice as $row)
                            @php
                                $noticeDate = strtotime($row->publish_date);
                            @endphp
                            <div class="comment-center p-t-10">
                                <div class="comment-body">

                                    <div class="user-img"><i style="font-size: 31px"
                                            class="fa fa-flag-checkered text-info"></i></div>
                                    <div class="mail-contnet">
                                        <h5 class="text-danger">{{ substr($row->title, 0, 70) }}..</h5><span
                                            class="time">Published Date:
                                            {{ date(' d M Y ', $noticeDate) }}</span>
                                        <br /><span class="mail-desc">
                                            @lang('notice.published_by'): {{ $row->createdBy->first_name }}
                                            {{ $row->createdBy->last_name }}<br>
                                            @lang('notice.description'): {!! substr($row->description, 0, 80) !!}..
                                        </span>
                                        <a href="{{ url('notice/' . $row->notice_id) }}"
                                            class="btn m-r-5 btn-rounded btn-outline btn-info">@lang('common.read_more')</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
<?php
function expire($doc, $date)
{
    return ['doc' => $doc, 'date' => DATE('d-m-Y', strtotime($date)), 'days' => days($date, DATE('Y-m-d'))];
}

function days($from_date, $to_date)
{
    $date1 = new DateTime($from_date);
    $date2 = new DateTime($to_date);
    $days = $date2->diff($date1)->format('%a');
    return $days;
}
?>
@section('page_scripts')
{{-- <script>
    $(window).load(function() {
        $("#preloaders").fadeOut(2000);
    });
</script> --}}

<script type="text/javascript">
    document.onreadystatechange = function() {
        switch (document.readyState) {
            case "loading":
                window.documentLoading = true;
                break;
            case "complete":
                window.documentLoading = false;
                break;
            default:
                window.documentLoading = false;
        }
    }

    function loading($bool) {
        // $("#preloaders").fadeOut(1000);
        if ($bool == true) {
            $.toast({
                heading: 'success',
                text: 'Processing Please Wait !',
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 3000,
                stack: 1
            });
            window.setTimeout(function() {
                location.reload()
            }, 3000);
        }
        $("#preloaders").fadeOut(1000);
    }


    // if (window.documentLoading = true) {
    //     $("#preloaders").fadeOut(1000);
    // }

    // $(document).on('click', '.loading', function() {
    //     $("#preloaders").fadeOut(1000);
    // });
</script>

<link href="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/css/site.css') !!}" rel="stylesheet" type="text/css" />
<script src="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/scripts/jquery.bootstrap.newsbox.min.js') !!}"></script>
<script type="text/javascript">
    (function() {

        $(".demo1").bootstrapNews({
            newsPerPage: 2,
            autoplay: true,
            pauseOnHover: true,
            direction: 'up',
            newsTickerInterval: 4000,
            onToDo: function() {
                //console.log(this);
            }
        });

    })();



    $(document).on('click', '.remarksForManagerLeave', function() {
        var actionTo = "{{ URL::to('ajaxapproveOrRejectManagerLeaveApplication') }}";
        var leave_application_id = $(this).attr('data-leave_application_id');
        var status = $(this).attr('data-status');
        var managerLeaveRemark = $('.remarks').val();

        if (status == 2) {
            var statusText = "Are you want to approve leave application?";
            var btnColor = "#2cabe3";
        } else {
            var statusText = "Are you want to reject leave application?";
            var btnColor = "red";
        }

        swal({
                title: "",
                text: statusText,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: btnColor,
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                showLoaderOnConfirm: true // Display loader when confirming
            },
            function(isConfirm) {
                var token = '{{ csrf_token() }}';
                if (isConfirm) {
                    $.ajax({
                        type: 'POST',
                        url: actionTo,
                        data: {
                            leave_application_id: leave_application_id,
                            status: status,
                            remarks: managerLeaveRemark,
                            _token: token
                        },
                        success: function(data) {
                            if (data == 'approve') {
                                swal({
                                        title: "Approved!",
                                        text: "Leave application approved.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_application_id).fadeOut();
                                            location.reload();
                                        }
                                    });

                            } else {
                                swal({
                                        title: "Rejected!",
                                        text: "Leave application rejected.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_application_id).fadeOut();
                                            location.reload();
                                        }
                                    });
                            }
                        }

                    });
                } else {
                    swal("Cancelled", "Your data is safe .", "error");
                }
            });
        return false;

    });

    $(document).on('click', '.remarksForLeave', function() {

        var actionTo = "{{ URL::to('ajaxapproveOrRejectLeaveApplication') }}";
        var leave_application_id = $(this).attr('data-leave_application_id');
        var status = $(this).attr('data-status');
        var leaveRemark = $('#leaveRemark').val();

        if (status == 2) {
            var statusText = "Are you want to approve leave application?";
            var btnColor = "#2cabe3";
        } else {
            var statusText = "Are you want to reject leave application?";
            var btnColor = "red";
        }

        swal({
                title: "",
                text: statusText,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: btnColor,
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                showLoaderOnConfirm: true // Display loader when confirming
            },
            function(isConfirm) {
                var token = '{{ csrf_token() }}';
                if (isConfirm) {
                    $.ajax({
                        type: 'POST',
                        url: actionTo,
                        data: {
                            leave_application_id: leave_application_id,
                            remarks: leaveRemark,
                            status: status,
                            _token: token
                        },
                        success: function(data) {
                            if (data == 'approve') {
                                swal({
                                        title: "Approved!",
                                        text: "Leave application approved.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_application_id).fadeOut();
                                            location.reload();
                                        }
                                    });

                            } else {
                                swal({
                                        title: "Rejected!",
                                        text: "Leave application rejected.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_application_id).fadeOut();
                                            location.reload();
                                        }
                                    });
                            }
                        }

                    });
                } else {
                    swal("Cancelled", "Your data is safe .", "error");

                }
            });
        return false;

    });
    $(document).on('click', '.remarksForManagerPermission', function() {

        var actionTo = "{{ URL::to('ajaxapproveOrRejectManagerPermissionApplication') }}";
        var leave_permission_id = $(this).attr('data-leave_permission_id');
        var status = $(this).attr('data-status');
        var managerPermissionRemark = $('.permissionRemarks').val();

        if (status == 2) {
            var statusText = "Are you want to approve Permission application?";
            var btnColor = "#2cabe3";
        } else {
            var statusText = "Are you want to reject Permission application?";
            var btnColor = "red";
        }

        swal({
                title: "",
                text: statusText,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: btnColor,
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                showLoaderOnConfirm: true // Display loader when confirming
            },
            function(isConfirm) {
                var token = '{{ csrf_token() }}';
                if (isConfirm) {
                    $.ajax({
                        type: 'POST',
                        url: actionTo,
                        data: {
                            leave_permission_id: leave_permission_id,
                            status: status,
                            remarks: managerPermissionRemark,
                            _token: token
                        },
                        success: function(data) {
                            if (data == 'approve') {
                                swal({
                                        title: "Approved!",
                                        text: "Permission application approved.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_permission_id).fadeOut();
                                            location.reload();
                                        }
                                    });

                            } else {
                                swal({
                                        title: "Rejected!",
                                        text: "Permission application rejected.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_permission_id).fadeOut();
                                            location.reload();
                                        }
                                    });
                            }
                        }

                    });
                } else {
                    swal("Cancelled", "Your data is safe .", "error");
                }
            });
        return false;

    });

    $(document).on('click', '.remarksForDepartmentHead', function() {

        var actionTo = "{{ URL::to('ajaxapproveOrRejectPermissionApplication') }}";
        var leave_permission_id = $(this).attr('data-leave_application_id');
        var status = $(this).attr('data-status');
        var managerPermissionRemark = $('.permissionRemarks').val();

        if (status == 2) {
            var statusText = "Are you want to approve permission application?";
            var btnColor = "#2cabe3";
        } else {
            var statusText = "Are you want to reject permission application?";
            var btnColor = "red";
        }

        swal({
                title: "",
                text: statusText,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: btnColor,
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                showLoaderOnConfirm: true // Display loader when confirming
            },
            function(isConfirm) {
                var token = '{{ csrf_token() }}';
                if (isConfirm) {
                    $.ajax({
                        type: 'POST',
                        url: actionTo,
                        data: {
                            leave_permission_id: leave_permission_id,
                            status: status,
                            remarks: managerPermissionRemark,
                            _token: token
                        },
                        success: function(data) {
                            if (data == 'approve') {
                                swal({
                                        title: "Approved!",
                                        text: "Permission application approved.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_permission_id).fadeOut();
                                            location.reload();
                                        }
                                    });

                            } else if (data == 'exceeds') {
                                swal({
                                        title: "Already applied two permission!",
                                        text: "Permission request Rejected.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_permission_id).fadeOut();
                                            location.reload();
                                        }
                                    });

                            } else {
                                swal({
                                        title: "Rejected!",
                                        text: "Permission application rejected.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.' + leave_permission_id).fadeOut();
                                            location.reload();
                                        }
                                    });
                            }

                        }

                    });
                } else {
                    swal("Cancelled", "Your data is safe .", "error");
                }
            });
        return false;

    });

    $(document).on('click', '.remarksForonDuty', function() {

        var actionTo = "{{ URL::to('approveOrRejectOnDutyApplication') }}";
        var on_duty_id = $(this).attr('data-onduty_application_id');
        var head_remark = $('#head_remark').val();
        var status = $(this).attr('data-status');

        if (status == 2) {
            var statusText = "Are you want to approve the onDuty application?";
            var btnColor = "#2cabe3";
        } else {
            var statusText = "Are you want to reject the onDuty application?";
            var btnColor = "red";
        }

        swal({
                title: "",
                text: statusText,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: btnColor,
                confirmButtonText: "Yes",
                closeOnConfirm: false
            },
            function(isConfirm) {
                var token = '{{ csrf_token() }}';
                if (isConfirm) {
                    $.ajax({
                        type: 'POST',
                        url: actionTo,
                        data: {
                            on_duty_id: on_duty_id,
                            head_remark: head_remark,
                            status: status,
                            _token: token
                        },
                        success: function(data) {
                            if (data == 'approve') {
                                swal({
                                        title: "Approved!",
                                        text: "onDuty application approved.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.onduty' + on_duty_id).fadeOut();
                                            location.reload();
                                        }
                                    });

                            } else {
                                swal({
                                        title: "Rejected!",
                                        text: "onDuty application rejected.",
                                        type: "success"
                                    },
                                    function(isConfirm) {
                                        if (isConfirm) {
                                            $('.onduty' + on_duty_id).fadeOut();
                                            location.reload();
                                        }
                                    });
                            }
                        }

                    });
                } else {
                    swal("Cancelled", "Your data is safe .", "error");
                }
            });
        return false;

    });

    /* document.getElementById('absentDetail').addEventListener('click', function() {
        document.getElementById('show_details').classList.toggle('hidden');
    }); */
    /* 
        if ($('.pagination').find('li.active span').html() != 1) {
            $('#absentDetail').trigger('click');
        } */
</script>
<script>
    $(function() {
        $('.toggle-class').change(function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');
            var action = "{{ URL::to('admin/pushSwitch') }}";
            $.ajax({
                type: "GET",
                dataType: "json",
                url: action,
                data: {
                    'status': status,
                    'id': id,
                    // '_token': $('input[name=_token]').val()
                },
                success: function(data) {
                    console.log(data.success)
                }
            });
        })
    })
</script>

@if (auth()->user())
    <script>
        function sendMarkRequest(id = null) {
            return $.ajax("{{ route('admin.markNotification') }}", {
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                }
            });
        }
        $(function() {
            $('.mark-as-read').click(function() {
                let request = sendMarkRequest($(this).data('id'));
                request.done(() => {
                    $(this).parents('div.alert').remove();
                });
            });
            $('#mark-all').click(function() {
                let request = sendMarkRequest();
                request.done(() => {
                    $('div.alert').remove();
                })
            });
        });
    </script>
@endif
@endsection
