@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.my_application_list')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('applyForLeave.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('leave.apply_for_leave')</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table tabole-bordered table-hover manage-u-table">
                                <thead class="tr_header">
                                    <tr style="white-space:nowrap;">
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('common.employee_name')</th>
                                        <th>@lang('leave.leave_type')</th>
                                        <th>@lang('leave.request_duration')</th>
                                        <th>@lang('leave.number_of_day')</th>
                                        <th>@lang('leave.approve_status')</th>
                                        <th>@lang('leave.reject_status')</th>
                                        <th>@lang('leave.created_by')</th>
                                        <th>@lang('leave.remarks')</th>
                                        <th>@lang('common.managerstatus')</th>
                                        <th>@lang('common.headdepartmentstatus')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <tr>
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>
                                                @if (isset($value->employee->first_name))
                                                    {!! $value->employee->first_name !!}
                                                @endif
                                                @if (isset($value->employee->last_name))
                                                    {!! $value->employee->last_name !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($value->leaveType->leave_type_name))
                                                    {!! $value->leaveType->leave_type_name !!}
                                                @endif
                                            </td>
                                            <td>
                                                {!! dateConvertDBtoForm($value->application_from_date) !!} <b>to</b> {!! dateConvertDBtoForm($value->application_to_date) !!}
                                                <br /><span class="text-muted">@lang('leave.application_date'):
                                                    {!! dateConvertDBtoForm($value->application_date) !!}</span>
                                            </td>
                                            <td>{!! $value->number_of_day !!}</td>
                                            <td>
                                                @if (isset($value->approveBy->first_name))
                                                    {!! $value->approveBy->first_name !!} {!! $value->approveBy->last_name !!}
                                                    <br /><span class="text-muted">@lang('leave.approved_date'):
                                                        {!! dateConvertDBtoForm($value->approve_date) !!}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($value->rejectBy->first_name))
                                                    {!! $value->rejectBy->first_name !!} {!! $value->rejectBy->last_name !!}
                                                    <br /><span class="text-muted">@lang('leave.rejected_date'):
                                                        {!! dateConvertDBtoForm($value->reject_date) !!}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($value->createdBy->first_name))
                                                    {!! $value->createdBy->first_name !!} {!! $value->createdBy->last_name !!}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-muted">@lang('leave.manager_remarks') :
                                                    @if (isset($value->manager_remarks))
                                                        {!! $value->manager_remarks !!}
                                                    @else
                                                        {{ '-' }}
                                                    @endif
                                                </span>
                                                <br />
                                                <span class="text-muted">@lang('leave.hr_remarks') :
                                                    @if (isset($value->remarks))
                                                        {!! $value->remarks !!}
                                                    @else
                                                        {{ '-' }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td style="width: 100px;">
                                                @if ($value->manager_status == 1)
                                                    <span class="label label-warning">@lang('common.pending')</span>
                                                @elseif ($value->manager_status == 2)
                                                    <span class="label label-success">@lang('common.approved')</span>
                                                @else
                                                    <span class="label label-danger">@lang('common.rejected')</span>
                                                @endif
                                            </td>
                                            <td style="width: 100px;">
                                                @if ($value->status == 1)
                                                    <span class="label label-warning">@lang('common.pending')</span>
                                                @elseif ($value->status == 2)
                                                    <span class="label label-success">@lang('common.approved')</span>
                                                @else
                                                    <span class="label label-danger">@lang('common.rejected')</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $results->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    // for feature referrance need to change the table into datatable.

    // $('#leaveTable').DataTable({
    //     "processing": true,
    //     "serverSide": true,
    //     "ajax": "{{ route('applyForLeave.index') }}",
    //     "columns": [{
    //             "data": "id"
    //         },
    //         {
    //             "data": "name"
    //         },
    //     ]
    // });
</script>
@endsection
