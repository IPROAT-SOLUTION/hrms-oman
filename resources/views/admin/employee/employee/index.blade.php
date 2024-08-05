@extends('admin.master')

@section('content')

@section('title')
    @lang('employee.employee_list')
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

            <a href="{{ route('employee.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                    class="fa fa-plus-circle" aria-hidden="true"></i> @lang('employee.add_employee')</a>

        </div>

    </div>



    <div class="row">

        <div class="col-sm-12">

            <div class="panel panel-info">

                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')
                    <a href="{{ route('sync.t_usr') }}" class="pull-right fa fa-refresh btn btn-sm bg-white text-dark"
                        style="display: none">
                        Sync Employee </a>
                </div>

                <div class="panel-wrapper collapse in" aria-expanded="true">

                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;
                                <strong>{{ session()->get('error') }}</strong>

                            </div>
                        @endif
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

                        <div class="border"
                            style="border: 1px solid #EFEEEF;border-radius:4px;margin:12px;padding:12px">
                            <a class="pull-right" href="{{ route('templates.employeeTemplate') }}">
                                <div id="template1" class="btn btn-info btn-sm template1" value="Template"
                                    type="submit">
                                    <i class="fa fa-download" aria-hidden="true"></i><span>
                                        Template</span>
                                </div>
                            </a>
                            <div class="row hidden-xs hidden-sm">
                                <p class="border" style="margin-left:18px">
                                    <span><i class="fa fa-upload"></i></span>
                                    <span style="margin-left: 4px"> Import employee info excel file.Default
                                        Password(demo1234)</span>
                                </p>
                                <form action="{{ route('employee.import') }}" method="post"
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
                                                    Upload</button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- <br> --}}

                        {{-- <div class="row">
                            <div class="pull-right" style="padding-right:32px;">
                                <a href="{{ route('employee.export') }}"> <button class="btn btn-success btn-sm"><span>
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                        </span>Export Employee Details</button></a>
                            </div>
                        </div> --}}

                        <div class="data" style="margin: 8px;padding:8px">

                            @include('admin.employee.employee.pagination')

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
    $(function() {
        var table = $('#employeeTable').DataTable({
            autoFill: true,
            ordering: false,
            processing: false,
            colReorder: false,
            keys: true,
            select: true,
            select: {
                style: 'multi',
            },
            // state save to load faster
            bStateSave: true,
            fnStateSave: function(settings, data) {
                localStorage.setItem("dataTables_state", JSON.stringify(data));
            },
            fnStateLoad: function(settings) {
                return JSON.parse(localStorage.getItem("dataTables_state"));
            },
            dom: 'lBfrtip',
            buttons: [{
                text: 'CSV',
                className: 'dt-button buttons-custom-csv buttons-html5',
                action: function(e, dt, node, config) {
                    downloadExcel();
                }
            }],

            initComplete: function(settings, json) {
                $("#employeeTable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        function downloadExcel() {
            window.location.href = "{{ route('employee.export') }}";
        };
    });
</script>
@endsection
