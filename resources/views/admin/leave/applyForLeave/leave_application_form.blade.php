@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.leave_application_form')
@endsection
<style>
    .datepicker table tr td.disabled,
    .datepicker table tr td.disabled:hover {
        background: none;
        color: red !important;
        cursor: default;
    }

    td {
        color: black !important;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('applyForLeave.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('leave.view_leave_applicaiton')</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@lang('leave.leave_application_form')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">×</span></button>
                                @foreach ($errors->all() as $error)
                                    <strong>{!! $error !!}</strong><br>
                                @endforeach
                            </div>
                        @endif
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
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        {{ Form::open(['route' => 'applyForLeave.store', 'enctype' => 'multipart/form-data', 'id' => 'leaveApplicationForm']) }}
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{-- {!! Form::hidden('employee_id', isset($getEmployeeInfo[0]) ? $getEmployeeInfo[0]->employee_id : '', ['class' => 'employee_id']) !!} --}}


                                        <label for="exampleInput">@lang('common.employee_name')<span
                                                class="validateRq">*</span></label>
                                        {{ Form::select('employee_id', $getEmployeeInfo, old('employee_id'), ['class' => 'form-control employee_id select2 required']) }}
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.total_leave_taken')<span
                                                class="validateRq">*</span></label>
                                        {!! Form::text(
                                            '',
                                            $data['sumOfLeaveTaken'],
                                            $attributes = [
                                                'class' => 'form-control total_leave_taken',
                                                'readonly' => 'readonly',
                                                'placeholder' => __('leave.total_leave_taken'),
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.leave_type')<span
                                                class="validateRq">*</span></label>
                                        {{ Form::select('leave_type_id', $leaveTypeList, old('leave_type_id'), ['class' => 'form-control leave_type_id select2 required']) }}
                                    </div>
                                </div>
                                <div class="col-md-3 doc_upload">
                                    <label class="form-label" for="customFile">@lang('leave.upload_document')<span
                                            class="validateRq">*</span></label>
                                    <input type="file" class="form-control required" id="document"
                                        name="document" />
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.current_balance')<span
                                                class="validateRq">*</span></label>
                                        {!! Form::text(
                                            '',
                                            '',
                                            $attributes = [
                                                'class' => 'form-control current_balance',
                                                'readonly' => 'readonly',
                                                'placeholder' => __('leave.current_balance'),
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInput">@lang('common.from_date')<span class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text(
                                            'application_from_date',
                                            old('application_from_date'),
                                            $attributes = [
                                                'class' => 'form-control application_from_date',
                                                'readonly' => 'readonly',
                                                'placeholder' => __('common.from_date'),
                                            ],
                                        ) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInput">@lang('common.to_date')<span
                                            class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        {!! Form::text(
                                            'application_to_date',
                                            old('application_to_date'),
                                            $attributes = [
                                                'class' => 'form-control application_to_date',
                                                'readonly' => 'readonly',
                                                'placeholder' => __('common.to_date'),
                                            ],
                                        ) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 half_day_section normal-section">
                                    <div class="form-group">
                                        <label for="exampleInput"
                                            title="{{ PHP_EOL }}1 will be 0.5 day,{{ PHP_EOL }}2 will be 1.5 days{{ PHP_EOL }}if select @lang('leave.half_day')"> @lang('leave.leave_day')
                                            <i class="fa fa-exclamation-circle"></i></label>
                                        {{ Form::select(
                                            'half_day',
                                            ['' => 'Full Day', '0.5' => __('leave.half_day')],
                                            null,
                                            $attributes = ['class' => 'form-control half_day', 'readonly' => 'readonly'],
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3" style="display: block">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.number_of_day')<span
                                                class="validateRq">*</span></label>
                                        {!! Form::text(
                                            'number_of_day',
                                            '',
                                            $attributes = [
                                                'class' => 'form-control number_of_day',
                                                'readonly' => 'readonly',
                                                'placeholder' => __('leave.number_of_day'),
                                            ],
                                        ) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.purpose')<span
                                                class="validateRq">*</span></label>
                                        {!! Form::textarea(
                                            'purpose',
                                            old('purpose'),
                                            $attributes = [
                                                'class' => 'form-control purpose',
                                                'id' => 'purpose',
                                                'placeholder' => __('leave.purpose'),
                                                'cols' => '30',
                                                'rows' => '3',
                                            ],
                                        ) !!}
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" id="formSubmit" class="btn btn-info "><i
                                            class="fa fa-paper-plane"></i> @lang('leave.send_application')</button>
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
<script>
    jQuery(function() {

        displayHalfDay();

        function displayHalfDay() {
            var application_from_date = $('.application_from_date').val();
            var application_to_date = $('.application_to_date').val();
            if (application_from_date == application_to_date) {
                $('.half_day_section').show();
            } else {
                $('.half_day_section').hide();
            }
        }

        // $('.half_day_section').hide();
        $(document).on("focus", ".application_from_date", function(e) {
            e.stopPropagation();
            // Calculate the date 30 days ago
            var thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

            $(this).datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                clearBtn: true,
                startDate: thirtyDaysAgo, // Set the start date to 30 days ago
            }).on('changeDate', function(e) {
                $(this).datepicker('hide');
            });
        });
        $(document).on("focus", ".application_to_date", function() {
            // Calculate the date 30 days ago
            var thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

            $(this).datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                clearBtn: true,
                startDate: thirtyDaysAgo, // Set the start date to 30 days ago
            }).on('changeDate', function(e) {
                $(this).datepicker('hide');
            });
        });

        $(document).on("change", ".application_from_date,.application_to_date", function(e) {
            e.stopPropagation();
            var application_from_date = $('.application_from_date').val();
            var application_to_date = $('.application_to_date').val();
            var employee_id = $('.employee_id').val();
            var leave_type_id = $('.leave_type_id').val();

            if (application_from_date != '' && application_to_date != '') {
                var action = "{{ URL::to('applyForLeave/applyForTotalNumberOfDays') }}";
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: {
                        'application_from_date': application_from_date,
                        'application_to_date': application_to_date,
                        'employee_id': employee_id,
                        'leave_type_id': leave_type_id,
                        '_token': $('input[name=_token]').val()
                    },
                    dataType: 'json',
                    success: function(data) {
                        var currentBalance = $('.current_balance').val();
                        if (data > currentBalance) {
                            $.toast({
                                heading: 'Warning',
                                text: 'Your leave balance has been exceeded. You only have ' +
                                    $('.current_balance')
                                    .val() + ' days remaining.',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'warning',
                                hideAfter: 3000,
                                stack: 1
                            });
                            $('body').find('#formSubmit').attr('disabled', true);
                            $('.number_of_day,.half_day').val('');

                        } else if (data == 0) {
                            $.toast({
                                heading: 'Warning',
                                text: 'The dates provided are invalid or your leave balance is zero.',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'warning',
                                hideAfter: 3000,
                                stack: 1
                            });
                            $('body').find('#formSubmit').attr('disabled', true);
                            $('.number_of_day,.half_day').val('');
                        } else {
                            $('.number_of_day').val(data);

                            $('body').find('#formSubmit').attr('disabled', false);
                        }
                    }
                });
                displayHalfDay()
            } else {
                $('body').find('#formSubmit').attr('disabled', true);
            }


        });

        $(document).on("change", ".half_day,.application_from_date,.application_to_date", function(e) {
            e.stopPropagation();
            var application_from_date = $('.application_from_date').val();
            var application_to_date = $('.application_to_date').val();
            if (application_from_date == application_to_date) {
                if ($('.half_day').val() == 0.5) {
                    $('.number_of_day').val(0.5);
                } else {
                    $('.number_of_day').val(1);
                }
            }
        });

        $(document).on("change", ".leave_type_id", function(e) {
            e.stopPropagation();
            var leave_type_id = $('.leave_type_id ').val();
            if (leave_type_id == 7) {
                $('.doc_upload').hide();
            } else {
                $('.doc_upload').show();

            }
        });

        $(document).on("change", ".leave_type_id", function(e) {
            e.stopPropagation();
            var leave_type_id = $('.leave_type_id ').val();
            var employee_id = $('.employee_id ').val();
            var total_leave_taken = $('.total_leave_taken ').val();
            if (employee_id != '' && leave_type_id != '') {
                var action = "{{ URL::to('applyForLeave/getEmployeeLeaveBalance') }}";
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: {
                        'leave_type_id': leave_type_id,
                        'employee_id': employee_id,
                        '_token': $('input[name=_token]').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == true) {
                            // console.log(response.total_leave_taken);
                            $('.total_leave_taken').val(response.leave_taken);
                            if (response.leave_balance == 0) {
                                $.toast({
                                    heading: 'Warning',
                                    text: 'You have no leave balance',
                                    position: 'top-right',
                                    loaderBg: '#ff6849',
                                    icon: 'warning',
                                    hideAfter: 3000,
                                    stack: 1
                                });
                                $('.current_balance').val(response.leave_balance);
                                $('body').find('#formSubmit').attr('disabled', true);
                            } else {
                                $('.current_balance').val(response.leave_balance);
                                $('body').find('#formSubmit').attr('disabled', false);
                            }
                        } else {
                            $.toast({
                                heading: 'Warning',
                                text: response.message,
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'warning',
                                hideAfter: 3000,
                                stack: 1
                            });
                        }
                    }
                });
            } else {
                $('body').find('#formSubmit').attr('disabled', true);
                $.toast({
                    heading: 'Warning',
                    text: 'Please select leave type !',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'warning',
                    hideAfter: 3000,
                    stack: 1
                });
                $('.current_balance').val('');
            }
        });

    });

    $(function() {
        var leave_type_id = $('.leave_type_id ').val();
        var employee_id = $('.employee_id ').val();
        var total_leave_taken = $('.total_leave_taken ').val();

        if (employee_id != '' && leave_type_id != '') {
            var action = "{{ URL::to('applyForLeave/getEmployeeLeaveBalance') }}";
            $.ajax({
                type: 'POST',
                url: action,
                data: {
                    'leave_type_id': leave_type_id,
                    'employee_id': employee_id,
                    '_token': $('input[name=_token]').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        $('.total_leave_taken').val(response.leave_taken);
                        if (response.leave_balance == 0) {
                            $.toast({
                                heading: 'Warning',
                                text: 'You have no leave balance',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'warning',
                                hideAfter: 3000,
                                stack: 1
                            });
                            $('.current_balance').val(response.leave_balance);
                            $('body').find('#formSubmit').attr('disabled', true);
                        } else {
                            $('.current_balance').val(response.leave_balance);
                            $('body').find('#formSubmit').attr('disabled', false);
                        }
                    } else {
                        $.toast({
                            heading: 'Warning',
                            text: response.message,
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'warning',
                            hideAfter: 3000,
                            stack: 1
                        });
                    }
                }
            });
        }
    });
</script>
@endsection
