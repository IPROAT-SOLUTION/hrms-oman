@extends('admin.master')
@section('content')
@section('title')
    @lang('leave_balance.employee_leave_balance')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">

                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-block alert-dismissable">
                                <ul>
                                    <button type="button" class="close" data-dismiss="alert">x</button>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-block alert-dismissable" style="margin-top: 12px;">
                                <button type="button" class="close" data-dismiss="alert">x</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif

                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger alert-block alert-dismissable" style="margin-top: 12px;">
                                <button type="button" class="close" data-dismiss="alert">x</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        <div class="row">
                            <div class="border"
                                style="border: 1px solid #b9b8b5;border-radius:4px;margin:12px;padding:12px">
                                <a class="pull-right" href="{{ route('leaveBalance.leaveBalanceTemplate') }}">
                                    <div id="template1" class="btn btn-info btn-sm template1" value="Template"
                                        type="submit">
                                        <i class="fa fa-download" aria-hidden="true"></i><span>
                                        @lang('employee.template')</span>
                                    </div>
                                </a>
                                <div class="row hidden-xs hidden-sm">
                                    <p class="border" style="margin-left:18px">
                                        <span><i class="fa fa-upload"></i></span>
                                        <span style="margin-left: 4px"> @lang('leave.import_leave_balance')</span>
                                    </p>
                                    <form action="{{ route('leaveBalance.import') }}" method="post"
                                        enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div>
                                                <div class="col-md-4 text-right" style="margin-left:14px">
                                                    <input type="file" name="select_file"
                                                        class="form-control custom-file-upload">
                                                </div>
                                                <div class="col-sm-1">
                                                    <button class="btn btn-success btn-sm" type="submit"><span><i
                                                                class="fa fa-upload" aria-hidden="true"></i></span>
                                                                @lang('employee.upload')</button>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div id="searchBox">
                                {{ Form::open(['route' => 'leaveBalance.index', 'id' => 'leaveBalance']) }}
                                <div class="col-md-3">
                                    <div class="form-group department_name">
                                        <label class="control-label" for="email">@lang('common.department')<span
                                                class="validateRq"> </span>:</label>
                                        <select class="form-control department_id select2" name="department_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($departmentList as $value)
                                                <option value="{{ $value->department_id }}"
                                                    @if (@$value->department_id == $department_id) {{ 'selected' }} @endif>
                                                    {{ $value->department_name }} {{ $value->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group branch_name">
                                        <label class="control-label" for="email">@lang('common.branch')<span
                                                class="validateRq"> </span>:</label>
                                        <select class="form-control branch select2" name="branch_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($branch as $value)
                                                <option value="{{ $value->branch_id }}"
                                                    @if ($value->branch_id == $branch_id) {{ 'selected' }} @endif>
                                                    {{ $value->branch_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group designation_name">
                                        <label class="control-label" for="email">@lang('designation.designation_name')<span
                                                class="validateRq"> </span>:</label>
                                        <select class="form-control  select2" name="designation_id">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            @foreach ($designation as $value)
                                                <option value="{{ $value->designation_id }}"
                                                    @if (@$value->designation_id == $designation_id) {{ 'selected' }} @endif>
                                                    {{ $value->designation_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                            class="btn btn-info " value="@lang('common.filter')">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <hr>

                        <div class="table-responsive">
                            <table id="leaveBalanceReport"class="table table-hover table-bordered manage-u-table">
                                <thead class="tr_header">
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('leave_balance.employee_id')</th>
                                        <th>@lang('leave_balance.finger_id')</th>
                                        <th>@lang('leave_balance.branch')</th>
                                        <th>@lang('leave_balance.department')</th>
                                        <th>@lang('leave_balance.designation')</th>
                                        @foreach ($leave_type as $leave_name)
                                            <th>{{ ucwords($leave_name->leave_type_name) }}</th>
                                        @endforeach


                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($items as $value)
                                        <tr>
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>
                                                @if (isset($value['employee_name']))
                                                    {!! $value['employee_name'] !!}
                                                @endif

                                            </td>
                                            <td>
                                                @if (isset($value['finger_id']))
                                                    {!! $value['finger_id'] !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($value['branch']))
                                                    {!! $value['branch'] !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($value['department']))
                                                    {!! $value['department'] !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($value['designation']))
                                                    {!! $value['designation'] !!}
                                                @endif
                                            </td>
                                            @foreach (isset($value['leave_type']) ? $value['leave_type'] : [] as $key => $leave)
                                                <td>
                                                    @if (isset($leave))
                                                        {!! $leave !!}
                                                    @endif
                                                </td>
                                            @endforeach
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

@section('page_scripts')
<script class="text/javascript">
    var table = $('#leaveBalanceReport').DataTable({
        autoFill: true,
        ordering: false,
        processing: false,
        colReorder: false,
        keys: true,
        select: true,
        select: {
            style: 'multi'
        },
        dom: 'lBfrtip',

        // aLengthMenu: [
        //     [10, 25, 50, 100, 200, -1],
        //     [10, 25, 50, 100, 200, "All"]
        // ],

        buttons: ['csv', {
            extend: 'pdfHtml5',
            orientation: 'landscape',
            pageSize: 'A1',
            title: 'Attendance Summary Report',
            text: 'PDF',
        }],

        initComplete: function(settings, json) {
            $("#leaveBalanceReport").wrap(
                "<div style='overflow:auto; width:100%;position:relative;'></div>");
        },

    });
</script>
@endsection
