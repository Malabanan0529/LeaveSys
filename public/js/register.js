function setupPasswordToggle(btnId, inputId) {
    $(btnId).click(function() {
        const input = $(inputId);
        const btn = $(this);
        const isPassword = input.attr('type') === 'password';
        
        input.attr('type', isPassword ? 'text' : 'password');
        
        if (isPassword) {
            btn.html(`
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                    <line x1="2" x2="22" y1="2" y2="22"/>
                </svg>
            `);
        } else {
            btn.html(`
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            `);
        }
    });
}

setupPasswordToggle('#togglePassword', '#password');
setupPasswordToggle('#toggleConfirmPassword', '#confirm_password');

$('#registerForm').submit(function(e) {
    e.preventDefault();
    const btn = $(this).find('button[type="submit"]');
    const originalText = btn.text();
    
    const password = $('#password').val();
    const confirm = $('#confirm_password').val();

    if (password !== confirm) {
        $('#msg').removeClass('text-green-400').addClass('text-red-400').text('Passwords do not match');
        $('#confirm_password').addClass('border-red-500').focus();
        setTimeout(() => $('#confirm_password').removeClass('border-red-500'), 2000);
        return;
    }
    btn.prop('disabled', true).addClass('opacity-75').text('Creating...');
    $('#msg').removeClass('text-green-400').addClass('text-red-400').text('');

    $.post('/api/register', $(this).serialize(), function(resp) {
        if(resp.status === 'success') {
            $('#msg').removeClass('text-red-400').addClass('text-emerald-400').text('Success! Redirecting...');
            setTimeout(() => window.location.href = '/', 1500);
        } else {
            $('#msg').text(resp.message);
            btn.prop('disabled', false).removeClass('opacity-75').text(originalText);
        }
    }, 'json').fail(function() {
        $('#msg').text('Connection error. Please try again.');
        btn.prop('disabled', false).removeClass('opacity-75').text(originalText);
    });
});