@extends('admin.master')
@section('content')
@section('title')
    @lang('advancededuction.add_advancededuction')
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
            <a href="{{ route('advanceDeduction.advance') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> View Advance </a>
        </div>
    </div>
   
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {{ Form::open(['route' => 'advanceDeduction.advancestore', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'id' => 'advanceDeductionForm']) }}
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
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label col-sm-4" for="date">@lang('common.date') : <span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control col-md-4 dateField" required
                                                {{ isset($editModeData) ? 'disabled' : '' }} readonly
                                                placeholder="@lang('common.date_field')" id="date_of_advance_given"
                                                name="date_of_advance_given"
                                                value="@if (isset($editModeData->date_of_advance_given)) {{ dateConvertFormtoDB($editModeData->date_of_advance_given) }} @else {{ dateConvertDBToForm(date('Y-m-d')) }} @endif">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label col-md-4" for="number">@lang('common.fullname')
                                        :<span class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <div>
                                                <select class="form-control employee_id select2 required" name="emp_id"
                                                    id="emp_id" required {{ isset($editModeData) ? 'disabled' : '' }}
                                                    name="employee_id">
                                                    <option value="">---- @lang('common.please_select') ----</option>
                                                    @foreach ($results as $value)
                                                        @if (isset($value->employee_id))
                                                            <option value="{{ $value->employee_id }}"
                                                                {{ isset($editModeData) && $value->employee_id == $editModeData->employee_id ? 'selected' : '' }}>
                                                                {{ $value->displayNameWithCode() }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label col-sm-4" for="date">@lang('advancededuction.advancededuction_name') : <span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <select class="form-control advanced_deduction " name="advance_name"
                                                id="advance_name">
                                                <option value="">---- Select Advanced Deduction ----</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">

                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">@lang('advancededuction.advance_amount')
                                                :<span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                {!! Form::number(
                                                    'advance_amount',
                                                    Input::old('advance_amount'),
                                                    $attributes = [
                                                        'class' => 'form-control required advance_amount',
                                                        'id' => 'advance_amount',
                                                        'placeholder' => __('advancededuction.advance_amount'),
                                                        'readonly' => 'readonly',
                                                        'step' => 'any',
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="number">Amount
                                                :<span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                {!! Form::number(
                                                    'paid_amount',
                                                    Input::old('paid_amount'),
                                                
                                                    $attributes = [
                                                        'class' => 'form-control required payment_type',
                                                        'id' => 'paid_amount',
                                                        'placeholder' => __(''),
                                                        'min' => '0',
                                                        'step' => 'any',
                                                       
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hidden">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">@lang('advancededuction.remaining_month')
                                                :<span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                {!! Form::number(
                                                    'remaining_month',
                                                    Input::old('remaining_month'),
                                                    $attributes = [
                                                        'class' => 'form-control required remaining_month',
                                                        'id' => 'remaining_month',
                                                        'placeholder' => __('advancededuction.remaining_month'),
                                                        'min' => '0',
                                                        'readonly' => 'readonly',
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-offset-4 col-md-8">
                                                @if (isset($editModeData))
                                                    <button type="submit" class="btn btn-info btn_style"><i
                                                            class="fa fa-pencil"></i> @lang('common.update')</button>
                                                @else
                                                    <button type="submit" class="btn btn-info btn_style"><i
                                                            class="fa fa-check"></i> @lang('common.save')</button>
                                                @endif
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
    <script>
        var selectedId;
        var dataArray = [];

        $('#emp_id').on('change', function() {
            selectedId = $(this).val();
            $.ajax({
                url: '{{ url('/advanceDeduction/get-advanced-deductions') }}',
                type: 'GET',
                data: {
                    selectedId: selectedId,
                },
                success: function(response) {
                    console.log(response.data);
                    if (response.status) {
                        dataArray = response.data;
                        var select = $('.advanced_deduction');
                        select.empty();
                        select.append('<option value="">---- Select Advanced Deduction ----</option>');

                        dataArray.forEach(function(data) {
                            select.append('<option value="' + data.advance_deduction_id + '">' +
                                data.advancededuction_name + '</option>');
                        });
                        if (dataArray.length > 0) {
                            $('#advance_amount').val(dataArray[0].deduction_amouth_per_month);
                        }
                        select.change(function() {

                            var selectedAdvancedDeduction = $(this).val();
                            console.log(dataArray);
                            var selectedData = dataArray.find(function(data) {

                                return data.advance_deduction_id ==
                                    selectedAdvancedDeduction;
                            });
                            console.log(selectedData);
                            if (selectedData) {
                                $('#advance_amount').val(selectedData.advance_amount);
                            } else {
                                $('#advance_amount').val('');
                            }
                        });

                        select.change(function() {
                            var selectedAdvancedDeduction = $(this).val();
                            var selectedData = dataArray.find(function(data) {
                                return data.advance_deduction_id ==
                                    selectedAdvancedDeduction;
                            });
                            if (selectedData) {
                                $('#paid_amount').val(selectedData
                                    .deduction_amouth_per_month);
                            } else {
                                $('#paid_amount').val('');
                            }
                        });
                        select.change(function() {
                            var selectedAdvancedDeduction = $(this).val();
                            var selectedData = dataArray.find(function(data) {
                                return data.advance_deduction_id ==
                                    selectedAdvancedDeduction;
                            });
                            if (selectedData) {
                                var remaining_month = parseInt(selectedData.remaining_month);
                                var month = remaining_month - 1;
                                $('#remaining_month').val(month);
                            } else {
                                $('#remaining_month').val('');
                            }
                        });
                    }
                },

            });
        });
      
    </script>
@endsection
