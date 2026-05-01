
$(document).ready(function () {

    $('#login').click(function () {
        if ($('#user').val() == "") {
            alert('User name is missing', 'Validation error');
            return false;
        }

        if ($('#password').val() == "") {
            alert('Password is missing', 'Validation error');
            return false;
        }
        payload = {
            'user': $('#user').val(),
            'password': $('#password').val()
        }
        axiosPost(payload, "/api/public/login", validalogin);


    });
});
async function validalogin(result) {


    if (result.estatus == false) {
        alert('Invalid credentials ', 'Login Error');
    }
    else {
        alert('Welcome ' + result.user);
        setTimeout(function () {
            window.location.href = '/';
        }, 800);
    }
}
