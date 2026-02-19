import "./bootstrap";

// Template Scripts (Depend on global jQuery and Plugins)
import "./template/settings.js";
import "./template/custom.js";
import "./template/dlabnav-init.js";
import "./template/demo.js";
import "./template/styleSwitcher.js";
import "./template/dashboard/dashboard-1.js";

// Global AJAX Setup
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

function submitAjaxForm({
    formId,
    url,
    method = "POST",
    modalId = null,
    table = null,
    successMessage = "Saved successfully",
    buttonText = "Save",
    usePut = false,
    resetSelect2 = true,
    useSweetAlert = false
}) {

    const form = document.getElementById(formId);
    let formData = new FormData(form);

    if (usePut) {
        formData.append('_method', 'PUT');
    }

    $('#globalLoader').fadeIn(150);

    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,

        beforeSend: function () {
            $(`#${formId}`).find('button[type="submit"]')
                .prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i>');
        },

        success: function (response) {

            if (response.success) {

                if (modalId) {
                    $(`#${modalId}`).modal('hide');
                }

                form.reset();

                // Reset Select2
                if (resetSelect2) {
                    $(`#${formId} .select2`).val(null).trigger('change');
                }

                if (table) {
                    table.ajax.reload(null, false);
                }

                if (useSweetAlert) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message ?? successMessage,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    toastr.success(response.message ?? successMessage);
                }
            }
        },

        error: function (xhr) {

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                Object.values(errors).forEach(err => {
                    toastr.error(err[0]);
                });
            } else {
                toastr.error("Something went wrong");
            }
        },

        complete: function () {
            $('#globalLoader').fadeOut(150);

            $(`#${formId}`).find('button[type="submit"]')
                .prop('disabled', false)
                .html(buttonText);
        }
    });
}
