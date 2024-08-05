@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('socialSecurity.edit_socialSecurity')
    @else
        @lang('socialSecurity.add_socialSecurity')
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
            <a href="{{ route('socialSecurity.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('socialSecurity.view_socialSecurity') </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            {{ Form::model($editModeData, ['route' => ['socialSecurity.update', $editModeData->social_security_id], 'method' => 'PUT', 'files' => 'true', 'class' => 'form-horizontal', 'id' => 'socialSecurityForm']) }}
                            <input type="text" hidden name="social_security_id"
                                value="{{ $editModeData->social_security_id }}">
                        @else
                            {{ Form::open(['route' => 'socialSecurity.store', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'id' => 'socialSecurityForm']) }}
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
                                    <label class="control-label col-sm-4" for="year">@lang('common.year')<span
                                            class="validateRq">*</span>:</label>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::text(
                                                'year',
                                                Input::old('year'),
                                                $attributes = [
                                                    'class' => 'form-control required year yearPicker',
                                                    'id' => 'percentage',
                                                    'placeholder' => __('socialSecurity.year'),
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label col-md-4">@lang('socialSecurity.gross_salary'):<span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::number(
                                                'gross_salary',
                                                Input::old('gross_salary'),
                                                $attributes = [
                                                    'class' => 'form-control required gross_salary',
                                                    'id' => 'gross_salary',
                                                    'placeholder' => __('socialSecurity.gross_salary'),
                                                    'min' => '0',
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label col-sm-4" for="date">@lang('employee.nationality')<span
                                            class="validateRq">*</span>:</label>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::select('nationality', $nationality, old('nationality'), ['class' => 'form-control nationality select2 required']) }}
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label col-md-4">@lang('socialSecurity.percentage'):<span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::number(
                                                'percentage',
                                                Input::old('percentage'),
                                                $attributes = [
                                                    'class' => 'form-control required percentage',
                                                    'id' => 'percentage',
                                                    'placeholder' => __('socialSecurity.percentage'),
                                                    'min' => '0',
                                                    'step' => '0.1',
                                                ],
                                            ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label col-md-4">@lang('socialSecurity.employer_percentage'):<span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::number(
                                                'employer_contribution',
                                                Input::old('employer_contribution'),
                                                $attributes = [
                                                    'class' => 'form-control required employer_contribution',
                                                    'id' => 'employer_contribution',
                                                    'placeholder' => __('socialSecurity.employer_percentage'),
                                                    'min' => '0',
                                                    'step' => '0.1',
                                                ],
                                            ) !!}
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
                result = result.toFixed(2);

                $('#deduction_amouth_per_month').val(result);
            }

            e.preventDefault();
        });
    </script>
@endsection
