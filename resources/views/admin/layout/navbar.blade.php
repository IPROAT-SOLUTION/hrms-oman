<nav class="navbar navbar-default navbar-static-top">
    <div class="navbar-header">

        <ul class="nav navbar-top-links navbar-left">
            <li><a href="javascript:void(0)" class="open-close waves-effect waves-light"><i class="ti-menu tiMenu"></i></a>
            </li>

            <li class="dropdown">
                <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#" style="color:#fff"> {{ App::getLocale() }}
                    <div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>
                </a>
                <ul class="dropdown-menu mailbox animated bounceInDown">
                    <li>
                        <div class="drop-title">@lang('common.chose_a_language')</div>
                    </li>
                    <li>
                        <div class="message-center">
                            <a href="{{ url('local/en') }}">
                                <h5>English</h5>
                            </a>
                        </div>
                        <div class="message-center">
                            <a href="{{ url('local/ar') }}" title="Spanish">
                                <h5>Arabic</h5>
                            </a>
                        </div>
                    </li>
                </ul>
                <!-- /.dropdown-messages -->
            </li>
        </ul>

        <ul class="nav navbar-top-links navbar-right pull-right imageIcon">
            <li class="dropdown">
                <?php
                $employeeInfo = employeeInfo();
                $photoSrc = $employeeInfo[0]->photo ? asset('uploads/employeePhoto/' . $employeeInfo[0]->photo) : asset('admin_assets/img/default.png');
                ?>
                <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#">
                    <img src="{!! $photoSrc !!}" alt="user-img" width="36" height="34" class="img-custom">
                    <span class="hidden-xs" style="color: #fff !important; padding-right: 4px">
                        <span class="text-capitalize">{!! ucwords($employeeInfo[0]->user_name) !!}</span>
                    </span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-user animated stripMove imageDropdown">
                    <li><a href="{{ url('profile') }}"><i class="ti-user"></i> @lang('common.my_profile')</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="{{ url('changePassword') }}"><i class="ti-settings"></i> @lang('common.change_password')</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="javascript:void(0);" onclick="logoutWithAjax()"><i class="fa fa-power-off"></i>
                            @lang('common.logout')</a></li>
                </ul>
            </li>
        </ul>

    </div>
</nav>
<script>
    function logoutWithAjax() {
        var actionTo = "{{ URL::to('/logout') }}";
        $.ajax({
            type: 'GET',
            url: actionTo,
            success: function(response) {
                $.toast({
                    heading: 'success',
                    text: 'Logout successfully!',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'success',
                    hideAfter: 12000,
                    stack: 6
                });

                sessionStorage.clear();
                setTimeout(function() {
                    window.location.href = "{{ url('login') }}";
                }, 1000);

            }
        });
    }
</script>