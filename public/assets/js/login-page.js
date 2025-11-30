// document.getElementById('login-form').addEventListener('submit', function (event) {
//     event.preventDefault();

//     const loginButton = document.getElementById('login-button');
//     const loginText = document.getElementById('login-text');
//     const loginSpinner = document.getElementById('login-spinner');

//     loginButton.disabled = true;
//     loginText.style.display = 'none';
//     loginSpinner.style.display = 'inline-block';

//     grecaptcha.ready(function () {
//         grecaptcha.execute('6Leqv_8rAAAAAE7ceyMhBU-HKY9RW8QOzzc2QxZi', { action: 'login' }).then(function (token) {
//             document.getElementById('g-recaptcha-response').value = token;
//             document.getElementById('login-form').submit();
            
//         });
//     });
// });
