document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('adoptionForm');

    form.addEventListener('submit', function (e) {
        let isValid = true;
        const groups = form.querySelectorAll('.input-group');

        // Reset all error states
        groups.forEach(function (group) {
            group.classList.remove('has-error');
        });

        // Validate name
        const jmeno = document.getElementById('jmeno');
        if (jmeno.value.trim() === '') {
            jmeno.closest('.input-group').classList.add('has-error');
            isValid = false;
        }

        // Validate email
        const email = document.getElementById('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value.trim())) {
            email.closest('.input-group').classList.add('has-error');
            isValid = false;
        }

        // Validate select
        const druh = document.getElementById('druh');
        if (druh.value === '') {
            druh.closest('.input-group').classList.add('has-error');
            isValid = false;
        }

        // Validate checkbox
        const souhlas = form.querySelector('input[name="souhlas"]');
        if (!souhlas.checked) {
            souhlas.closest('.input-group').classList.add('has-error');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = form.querySelector('.has-error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Live removal of error state on input
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(function (input) {
        input.addEventListener('input', function () {
            const group = this.closest('.input-group');
            if (group) {
                group.classList.remove('has-error');
            }
        });
        input.addEventListener('change', function () {
            const group = this.closest('.input-group');
            if (group) {
                group.classList.remove('has-error');
            }
        });
    });
});
