document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('adoptionForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let isValid = true;
        const groups = form.querySelectorAll('.input-group');

        // Reset all error states
        groups.forEach(function (group) {
            group.classList.remove('has-error');
        });

        // Validate name
        const jmeno = document.getElementById('jmeno');
        if (jmeno && jmeno.value.trim() === '') {
            jmeno.closest('.input-group').classList.add('has-error');
            isValid = false;
        }

        // Validate email
        const email = document.getElementById('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email.value.trim())) {
            email.closest('.input-group').classList.add('has-error');
            isValid = false;
        }

        // Validate select (duvod or druh)
        var selects = form.querySelectorAll('select[required]');
        selects.forEach(function (sel) {
            if (sel.value === '') {
                sel.closest('.input-group').classList.add('has-error');
                isValid = false;
            }
        });

        // Validate checkbox
        const souhlas = form.querySelector('input[name="souhlas"]');
        if (souhlas && !souhlas.checked) {
            souhlas.closest('.input-group').classList.add('has-error');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            var firstError = form.querySelector('.has-error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Live removal of error state on input
    var inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(function (input) {
        input.addEventListener('input', function () {
            var group = this.closest('.input-group');
            if (group) {
                group.classList.remove('has-error');
            }
        });
        input.addEventListener('change', function () {
            var group = this.closest('.input-group');
            if (group) {
                group.classList.remove('has-error');
            }
        });
    });
});