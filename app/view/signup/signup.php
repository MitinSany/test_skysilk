<script type="text/javascript">
    $(function () {
        $('#register-email, #register-firstname, #register-lastname, #register-passwd').keyup(inputsRegisterChange);
        $('#btn-signup').click(btnSignupClick);

        function inputsRegisterChange() {
            var inputEmail = $('#register-email').val().length;
            var inputFirstname = $('#register-firstname').val().length;
            var inputLastname = $('#register-lastname').val().length;
            var inputPasswd = $('#register-passwd').val().length;
            var btnSignup = $('#btn-signup');
            if (inputEmail > 0 && inputFirstname > 0 && inputLastname > 0 && inputPasswd > 0) {
                btnSignup.removeClass('disabled');
            } else {
                btnSignup.addClass('disabled');
            }
        }

        function showSignupErrorMsg(message) {
            $('#signupbox #signupalert').text(message).show();
        }

        function hideSignupErrorMsg() {
            $('#signupbox #signupalert').hide().text('');
        }

        function btnSignupClick() {
            $.ajax({
                type: "POST",
                url: 'signup',
                data: {
                    email: $('#register-email').val(),
                    firstname: $('#register-firstname').val(),
                    lastname: $('#register-lastname').val(),
                    password: $('#register-passwd').val()
                },
                success: function (data) {
                    if (data.success === false) {
                        showSignupErrorMsg(data.message);
                        return false;
                    }

                    hideSignupErrorMsg();
                    location.href = data.location;
                }
            });
        }
    });
</script>

<div class="container">
    <div id="signupbox" style="margin-top:50px"
         class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Sign Up</div>
            </div>
            <div class="panel-body">
                <form id="signupform" class="form-horizontal" role="form">
                    <input type="hidden" id="csrfToken" name="csrfToken" value="<?= $param['csrfToken']; ?>">
                    <div id="signupalert" style="display:none" class="alert alert-danger">
                        <p>Error:</p>
                        <span></span>
                    </div>


                    <div class="form-group">
                        <label for="email" class="col-md-3 control-label">Email</label>
                        <div class="col-md-9">
                            <input type="text" id="register-email" class="form-control" name="email" placeholder="Email Address">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="firstname" class="col-md-3 control-label">First Name</label>
                        <div class="col-md-9">
                            <input type="text" id="register-firstname" class="form-control" name="firstname" placeholder="First Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-md-3 control-label">Last Name</label>
                        <div class="col-md-9">
                            <input type="text" id="register-lastname" class="form-control" name="lastname" placeholder="Last Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-md-3 control-label">Password</label>
                        <div class="col-md-9">
                            <input type="password" id="register-passwd" class="form-control" name="passwd" placeholder="Password">
                        </div>
                    </div>

                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-md-offset-3 col-md-9">
                            <a id="btn-signup" href="#" class="btn btn-info disabled">Sign Up </a>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12 control">
                            <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%">
                                Already have an account?
                                <a href="../login">
                                    Sign In Here
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
