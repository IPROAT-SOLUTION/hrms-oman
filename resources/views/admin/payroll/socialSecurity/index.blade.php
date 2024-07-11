@extends('admin.master')
@section('content')
@section('title')
@lang('socialSecurity.socialSecurity_list')
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
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('socialSecurity.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('socialSecurity.add_socialSecurity')</a>
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
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                        </div>
                        @endif
                        @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                        </div>
                        @endif
                        @if (session()->has('warning'))
                        <div class="alert alert-warning alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('warning') }}</strong>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myDataTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('socialSecurity.year')</th>
                                        <th>@lang('socialSecurity.gross_salary')</th>
                                        <th>@lang('socialSecurity.nationality')</th>
                                        <th>@lang('socialSecurity.percentage')</th>
                                        <th>@lang('socialSecurity.employer_percentage')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                    <tr class="{!! $value->social_security_id !!}">
                                        <td style="width: 100px;">{!! ++$sl !!}</td>
                                        <td>{!! $value->year !!}</td>
                                        <td>{!! number_format($value->gross_salary, 2 , '.', '') !!}</td>
                                        <td>{!! $value->nationality == 0 ? 'Omanis' : 'Expats' !!}</td>
                                        <td>{!! number_format($value->percentage, 2, '.','') . '%' !!}</td>
                                        <td>{!! number_format($value->employer_contribution, 2, '.','') . '%' !!}</td>
                                        <td style="width: 100px;">
                                            <a href="{!! route('socialSecurity.edit', $value->social_security_id) !!}" class="btn btn-success btn-xs btnColor">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                            <a href="{!! route('socialSecurity.delete', $value->social_security_id) !!}" data-token="{!! csrf_token() !!}" data-id="{!! $value->social_security_id !!}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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