@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('advancededuction.edit_advancededuction')
    @else
        @lang('advancededuction.add_advancededuction')
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
            <a href="{{ route('advanceDeduction.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('advancededuction.view_advancededuction') </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            {{ Form::model($editModeData, ['route' => ['advanceDeduction.update', $editModeData->advance_deduction_id], 'method' => 'PUT', 'files' => 'true', 'class' => 'form-horizontal', 'id' => 'advanceDeductionForm']) }}
                        @else
                            {{ Form::open(['route' => 'advanceDeduction.store', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'id' => 'advanceDeductionForm']) }}
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
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label col-sm-4" for="date">@lang('common.date') :<span
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
                                    <label class="control-label col-sm-4" for="date">@lang('advancededuction.advancededuction_name') : <span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control col-md-4" required
                                                {{ isset($editModeData) ? 'disabled' : '' }}
                                                placeholder="@lang('advancededuction.advancededuction_name')" id="advancededuction_name"
                                                name="advancededuction_name"
                                                value="@if (isset($editModeData->advancededuction_name)) {{ $editModeData->advancededuction_name }} @endif">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label col-md-4" for="number">@lang('common.fullname')
                                        : <span class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <div>
                                                <select class="form-control employee_id select2 required" required
                                                    {{ isset($editModeData) ? 'disabled' : '' }} name="employee_id">
                                                    <option value="">----
                                                        @lang('common.please_select') ----</option>
                                                    @foreach ($results as $value)
                                                        @foreach ($value as $v)
                                                            <option value="{{ $v['employee_id'] }}"
                                                                @if (isset($editModeData) && $v['employee_id'] == $editModeData->employee_id) {{ 'selected' }} @else {{ $v['employee_id'] }} @endif>
                                                                {{ $v->displayNameWithCode() }}
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">@lang('advancededuction.advance_amount')
                                                : <span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                {!! Form::number(
                                                    'advance_amount',
                                                    Input::old('advance_amount'),
                                                    $attributes = [
                                                        'class' => 'form-control required advance_amount',
                                                        'id' => 'advance_amount',
                                                        'placeholder' => __('advancededuction.advance_amount'),
                                                        // 'readonly' => isset($editModeData) ? 'readonly' : '',
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
                                            <label class="control-label col-md-4">@lang('advancededuction.no_of_month_to_be_deducted')
                                                : <span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                {!! Form::number(
                                                    'no_of_month_to_be_deducted',
                                                    Input::old('no_of_month_to_be_deducted'),
                                                    $attributes = [
                                                        'class' => 'form-control required no_of_month_to_be_deducted',
                                                        'id' => 'no_of_month_to_be_deducted',
                                                        'placeholder' => __('advancededuction.no_of_month_to_be_deducted'),
                                                        'min' => '0',
                                                        // 'readonly' => isset($editModeData) ? 'readonly' : '',
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">@lang('advancededuction.deduction_amouth_per_month')
                                                : <span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                {!! Form::number(
                                                    'deduction_amouth_per_month',
                                                    Input::old('deduction_amouth_per_month'),
                                                
                                                    $attributes = [
                                                        'class' => 'form-control required deduction_amouth_per_month',
                                                        'id' => 'deduction_amouth_per_month',
                                                        'placeholder' => __('advancededuction.deduction_amouth_per_month'),
                                                        'min' => '0',
                                                        'step' => 'any',
                                                        'readonly' => 'readonly',
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Payment type --}}
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="control-label col-md-4"
                                                for="payment_type">@lang('Payment Type')
                                                : <span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                @if (isset($editModeData))
                                                    <div>
                                                        <select class="form-control status select2 required" required
                                                            name="payment_type">
                                                            <option value="0"
                                                                {{ $editModeData->payment_type == 0 ? 'selected' : '' }}>
                                                                Bank</option>
                                                            <option value="1"
                                                                {{ $editModeData->payment_type == 1 ? 'selected' : '' }}>
                                                                Cash</option>

                                                        </select>
                                                    </div>
                                                @else
                                                    <div>
                                                        <select class="form-control status select2 required" required
                                                            name="payment_type">
                                                            <option value="0"
                                                                {{ Input::old('Payment_type') == 0 ? 'selected' : '' }}>
                                                                Bank</option>
                                                            <option value="1"
                                                                {{ Input::old('Payment_type') == 1 ? 'selected' : '' }}>
                                                                Cash</option>

                                                        </select>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="row">
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
                                                    ],
                                                ) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="control-label col-md-4" for="number">@lang('common.status')
                                                : <span class="validateRq">*</span></label>
                                            <div class="col-md-8">
                                                @if (isset($editModeData))
                                                    <div>
                                                        <select class="form-control status select2 required" required
                                                            name="status">
                                                            <option value="0"
                                                                {{ $editModeData->status == 0 ? 'selected' : '' }}>
                                                                Active</option>
                                                            <option value="1"
                                                                {{ $editModeData->status == 1 ? 'selected' : '' }}>
                                                                Hold</option>
                                                            <option value="2"
                                                                {{ $editModeData->status == 2 ? 'selected' : '' }}>
                                                                No Due</option>
                                                        </select>
                                                    </div>
                                                @else
                                                    <div>
                                                        <select class="form-control status select2 required" required
                                                            name="status">
                                                            <option value="0"
                                                                {{ Input::old('status') == 0 ? 'selected' : '' }}>
                                                                Active</option>
                                                            <option value="1"
                                                                {{ Input::old('status') == 1 ? 'selected' : '' }}>
                                                                Hold</option>
                                                            <option value="2"
                                                                {{ Input::old('status') == 2 ? 'selected' : '' }}>
                                                                No Due</option>
                                                        </select>
                                                    </div>
                                                @endif
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
        $('.advance_amount').keyup(function(e) {
            $('#deduction_amouth_per_month').val('');
            $('#no_of_month_to_be_deducted').val('');
        });

        $('.no_of_month_to_be_deducted').keyup(function(e) {
            var advance_amount = parseFloat($('#advance_amount').val());
            var no_of_months = parseFloat($(this).val());

            if (isNaN(advance_amount) || advance_amount <= 0) {
                $.toast({
                    heading: 'Warning',
                    text: 'Enter The Advanced Amount !',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'warning',
                    hideAfter: 3000,
                    stack: 6
                });
            } else if (isNaN(no_of_months) || no_of_months <= 0) {
                $.toast({
                    heading: 'Warning',
                    text: 'Invalid number of months entered !',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'warning',
                    hideAfter: 3000,
                    stack: 6
                });
            } else {
                // var result = Math.round(advance_amount / no_of_months);
                var result = advance_amount / no_of_months;
                result = result.toFixed(3);

                $('#deduction_amouth_per_month').val(result);
            }

            e.preventDefault();
        });
    </script>
@endsection
