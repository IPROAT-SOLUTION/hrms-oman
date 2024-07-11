@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('approve_overtime.edit_overtime_approval')
    @else
        @lang('approve_overtime.add_overtime_approval')
    @endif
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
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('approveOvertime.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('approve_overtime.view_overtime_approval')</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            {{ Form::model($editModeData, ['route' => ['approveOvertime.update', $editModeData->employee_attendance_id], 'method' => 'PUT', 'files' => 'true', 'class' => 'form-horizontal']) }}
                        @else
                            {{ Form::open(['route' => 'approveOvertime.store', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) }}
                        @endif
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-6">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">×</span></button>
                                            @foreach ($errors->all() as $error)
                                                <strong>{!! $error !!}</strong><br>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if (session()->has('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <i
                                                class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                        </div>
                                    @endif
                                    @if (session()->has('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <i
                                                class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-4">@lang('employee.employee_id') <span
                                        class="validateRq">*</span></label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                        </div>
                                        {!! Form::text(
                                            'finger_print_id',
                                            isset($editModeData) ? $editModeData->finger_print_id : old('finger_print_id'),
                                            $attributes = [
                                                'class' => 'form-control   finger_print_id',
                                                'id' => 'finger_print_id',
                                                'type' => 'text',
                                                'placeholder' => __('finger_print_id'),
                                                'readonly' => true,
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">@lang('approve_overtime.date') <span
                                        class="validateRq">*</span></label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </div>
                                        {!! Form::text(
                                            'date',
                                            isset($editModeData) ? date('d/m/Y', strtotime($editModeData->date)) : Input::old('date'),
                                            $attributes = [
                                                'class' => 'form-control required  date',
                                                'id' => 'date',
                                                'type' => 'date',
                                                'placeholder' => __('Date'),
                                                'readonly' => true,
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">@lang('attendance.over_time') <span
                                        class="validateRq">*</span></label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        {!! Form::text(
                                            'over_time',
                                            isset($editModeData) ? date('H:i', strtotime($editModeData->over_time)) : Input::old('over_time'),
                                            $attributes = [
                                                'class' => 'form-control required over_time',
                                                'id' => 'over_time',
                                                'placeholder' => __('Actual overtime'),
                                                'readonly' => true,
                                            ],
                                        ) !!}
                                       
                                    </div>
                                </div>


                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">@lang('approve_overtime.approved_over_time')<span
                                        class="validateRq">*</span></label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <div class="bootstrap-timepicker">
                                            {!! Form::text(
                                                'approved_over_time',
                                                isset($editModeData)
                                                    ? date('H:i', strtotime($editModeData->approved_over_time ?? $editModeData->over_time))
                                                    : old('approved_over_time'),
                                                $attributes = [
                                                    'class' => 'form-control timePickerCustom approved_over_time',
                                                    'id' => 'approved_over_time',
                                                    'placeholder' => __('approve_overtime.approved_over_time'),
                                                    'autocomplete' => false,
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-offset-6 col-md-8">
                                                @if (isset($editModeData))
                                                    <button id="saveBtn" type="submit"
                                                        class="btn btn-info btn_style"><i class="fa fa-pencil"></i>
                                                        @lang('common.update')</button>
                                                @else
                                                    <button id="updateBtn" type="submit"
                                                        class="btn btn-info btn_style"><i class="fa fa-check"></i>
                                                        @lang('common.save')</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script type="text/javascript">
    $(document).ready(function() {

        $(document).on("focus", ".timePickerCustom", function() {

            var time = $('.actual_overtime').val();

            $(this).timepicker({
                showInputs: false,
                showMeridian: false,
                timeFormat: 'H:i',
                scrollDefaultNow: 'true',
                closeOnWindowScroll: 'true',
                showDuration: false,
                ignoreReadonly: true,
                minuteStep: 1,
                defaultTime: time ?? '00:00'
            });
        });

        $('.finger_print_id, .date').change(function(e) {

            e.preventDefault();
            var finger_print_id = $('.finger_print_id').val();
            var date = $('.dateField').val();

            if (finger_print_id != '' && date != '') {
                $.ajax({
                    type: "GET",
                    url: "{{ route('approveOvertime.reportDetails') }}",
                    data: {
                        finger_print_id: finger_print_id,
                        date: date
                    },

                    success: function(response) {

                        if (response != 'notFound') {
                            $('.actual_overtime').val(response);
                            $('.approved_overtime').val(response);
                        }

                        if (response == 'notFound') {
                            $('.actual_overtime').val('00:00');
                            $('.approved_overtime').val('00:00');
                            $('#approved_overtime').prop('disabled', true);
                            $('#remark').prop('disabled', true);
                            $("#saveBtn").prop('disabled', true);
                            $("#updateBtn").prop('disabled', true);
                        } else {
                            $('#approved_overtime').prop('disabled', false);
                            $('#remark').prop('disabled', false);
                            $("#saveBtn").prop('disabled', false);
                            $("#updateBtn").prop('disabled', false);
                        }
                    }
                });
            }
        });
    });
</script>
@endsection
