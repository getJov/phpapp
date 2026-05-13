document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm || 'Are you sure?')) {
            event.preventDefault();
        }
    });
});

function passwordCriteria(password) {
    return [
        /[a-z]/.test(password),
        /[A-Z]/.test(password),
        /[0-9]/.test(password),
        /[^A-Za-z0-9]/.test(password),
        password.length >= 8,
    ];
}

function passwordMeterState(score) {
    if (score <= 1) {
        return { className: 'is-weak', label: 'Weak' };
    }

    if (score <= 3) {
        return { className: 'is-fair', label: 'Fair' };
    }

    if (score === 4) {
        return { className: 'is-good', label: 'Good' };
    }

    return { className: 'is-strong', label: 'Strong' };
}

document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.togglePassword);

        if (!input) {
            return;
        }

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        button.textContent = isHidden ? 'Hide' : 'Show';
    });
});

document.querySelectorAll('[data-password-input]').forEach((input) => {
    const meter = document.querySelector(`[data-password-meter="${input.id}"]`);
    const bar = meter ? meter.querySelector('.password-meter-bar') : null;
    const label = document.querySelector(`[data-password-meter-label="${input.id}"]`);

    input.addEventListener('input', () => {
        const score = passwordCriteria(input.value).filter(Boolean).length;
        const state = passwordMeterState(score);

        if (meter && bar) {
            meter.className = `password-meter mt-2 ${state.className}`;
            bar.style.width = `${score * 20}%`;
        }

        if (label) {
            label.textContent = input.value
                ? `${state.label}: ${score}/5 LUDS8 criteria met.`
                : 'Use lowercase, uppercase, digit, symbol, and at least 8 characters.';
        }
    });
});
