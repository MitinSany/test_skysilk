<script type="text/javascript">
    $(function () {
        $('#btn-login').click(btnLoginClick);
        $('#login-username, #login-password').keyup(inputsLoginChange);

        function btnLoginClick() {
            $.ajax({
                type: "POST",
                url: 'login',
                data: {
                    login: $('#login-username').val(),
                    password: $('#login-password').val(),
                    csrfToken: $('#csrfToken').val()
                },
                success: function (data) {
                    if (data.success === false) {
                        showLoginErrorMsg(data.message);
                        return false;
                    }

                    hideLoginErrorMsg();
                    location.href = data.location;
                }
            });
        }

        function inputsLoginChange() {
            var inputLogin = $('#login-username').val().length;
            var inputPassword = $('#login-password').val().length;
            var btnLogin = $('#btn-login');
            if (inputLogin > 0 && inputPassword > 0) {
                btnLogin.removeClass('disabled');
            } else {
                btnLogin.addClass('disabled');
            }
        }

        function showLoginErrorMsg(message) {
            $('#loginbox #login-alert').text(message).show();
        }

        function hideLoginErrorMsg() {
            $('#loginbox #login-alert').hide().text('');
        }
    });
</script>

<div class="container">
    <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Sign In</div>
            </div>

            <div style="padding-top:30px" class="panel-body">

                <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

                <form id="loginform" class="form-horizontal" role="form">
                    <input type="hidden" id="csrfToken" name="csrfToken" value="<?= $param['csrfToken']; ?>">
                    <div style="margin-bottom: 25px" class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="login-username" type="text" class="form-control" name="username"
                               placeholder="email">
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="login-password" type="password" class="form-control" name="password"
                               placeholder="password">
                    </div>
                    <div style="margin-top:10px" class="form-group">
                        <!-- Button -->

                        <div class="col-sm-12 controls">
                            <a id="btn-login" href="#" class="btn btn-success disabled">Login </a>

                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12 control">
                            <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%">
                                Don't have an account!
                                <a href="signup">
                                    Sign Up Here
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
