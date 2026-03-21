// DOM Elements
const loginForm = document.getElementById('loginForm');
const otpForm = document.getElementById('otpForm');
const loginMessage = document.getElementById('loginMessage');
const otpMessage = document.getElementById('otpMessage');
const backToLogin = document.getElementById('backToLogin');

// Login Form Submission
loginForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const btn = this.querySelector('.btn');

    btn.disabled = true;
    btn.innerHTML = 'Signing In...';
    loginMessage.style.display = 'none';

    fetch('actions/login_process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.requires_2fa) {
                // Transition to OTP form
                loginForm.style.display = 'none';
                otpForm.style.display = 'block';
                otpMessage.style.color = '#10b981';
                otpMessage.innerHTML = data.message;
                otpMessage.style.display = 'block';
            } else {
                // Direct login success
                loginMessage.style.color = '#10b981';
                loginMessage.innerHTML = 'Login successful! Redirecting...';
                loginMessage.style.display = 'block';
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1000);
            }
        } else {
            loginMessage.style.color = '#ef4444';
            loginMessage.innerHTML = data.message;
            loginMessage.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = 'Sign In';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        loginMessage.style.color = '#ef4444';
        loginMessage.innerHTML = 'An error occurred. Please try again.';
        loginMessage.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = 'Sign In';
    });
});

// OTP Form Submission
otpForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const btn = this.querySelector('.btn');

    btn.disabled = true;
    btn.innerHTML = 'Verifying...';
    otpMessage.style.display = 'none';

    fetch('actions/verify_otp.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            otpMessage.style.color = '#10b981';
            otpMessage.innerHTML = 'Code verified! Redirecting...';
            otpMessage.style.display = 'block';
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 1000);
        } else {
            otpMessage.style.color = '#ef4444';
            otpMessage.innerHTML = data.message;
            otpMessage.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = 'Verify Code';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        otpMessage.style.color = '#ef4444';
        otpMessage.innerHTML = 'Verification failed. Try again.';
        otpMessage.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = 'Verify Code';
    });
});

// Back to Login Link
backToLogin.addEventListener('click', function (e) {
    e.preventDefault();
    otpForm.style.display = 'none';
    loginForm.style.display = 'block';
    loginForm.querySelector('.btn').disabled = false;
    loginForm.querySelector('.btn').innerHTML = 'Sign In';
});
