@extends('admin.master')
@section('content')
@section('title')
    @lang('advancededuction.advance_deduction_log') 
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
            <a href="{{ route('advanceDeduction.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('advancededuction.add_advancededuction')</a>
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
                        @if (session()->has('warning'))
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('warning') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myDataTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('employee.name')</th>
                                        <th>@lang('advancededuction.advance_amount')</th>
                                        <th>@lang('advancededuction.reason')</th>
                                        <th>@lang('advancededuction.payment_type')</th>
                                        <th>@lang('advancededuction.paid_amount')</th>
                                        <th>@lang('advancededuction.created_updated_deleted_by')</th>
                                        <th>@lang('advancededuction.created_at')</th>


                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($results as $value)
                                        <tr class="{!! $value->advance_deduction_id !!} text-center">
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td class="text-left">{!! $value->employee->first_name . ' ' . $value->employee->last_name !!}</td>
                                            <td>{!! $value->advance_amount !!}</td>
                                            <td>{!! $value->reason !!}</td>
                                            <td>{!! $value->payment_type == 0 ? 'Bank' : 'Cash' !!}</td>
                                            <td>{!! $value->paid_amount !!}</td>
                                            @if ($value->created_by)
                                                <td>{!! $value->created_by!!}</td>
                                            @elseif($value->updated_by)
                                                <td>{!! $value->updated_by !!}</td>
                                            @else
                                                <td>{!! $value->deleted_by !!}</td>
                                            @endif
                                            <td>{!! date('d M Y', strtotime($value->created_at)) !!}</td>


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
<script>
    $(function() {
        $('.data').on('click', '.pagination a', function(e) {
            getData($(this).attr('href').split('page=')[1]);
            e.preventDefault();
        });
        $(".employee_name").bind("keyup change", function(e) {
            getData(1);
        })
    });

    function getData(page) {
        var employee_name = $('.employee_name').val();

        $.ajax({
            url: '?page=' + page + "&employee_name=" + employee_name,
            datatype: "html",
        }).done(function(data) {
            $('.data').html(data);
        }).fail(function() {
            alert('No response from server');
        });
    }
</script>
@endsection
