(function () {
    "use strict";

    /* Login: check whether the identifier belongs to a pending/rejected
       application before sending the core login form. This gives applicants a
       clear Turkish message without changing Kanboard's authentication flow. */
    var loginForm = document.querySelector(".ghd-login-form");
    var loginStatus = document.getElementById("ghd-login-status");
    var bypassLoginCheck = false;

    if (loginForm && loginStatus) {
        loginForm.addEventListener("submit", function (event) {
            if (bypassLoginCheck) {
                return;
            }

            var usernameField = loginForm.querySelector('[name="username"]');
            var statusUrl = loginForm.getAttribute("data-status-url");

            if (!usernameField || !statusUrl || !usernameField.value.trim()) {
                return;
            }

            event.preventDefault();
            loginForm.classList.add("is-checking");
            loginStatus.hidden = true;
            loginStatus.className = "ghd-login-status";

            fetch(statusUrl, {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: "identifier=" + encodeURIComponent(usernameField.value.trim())
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (result) {
                    var status = result.data && result.data.status;

                    if (status === "pending" || status === "rejected") {
                        loginStatus.textContent = result.data.message;
                        loginStatus.className = "ghd-login-status is-" + status;
                        loginStatus.hidden = false;
                        loginForm.classList.remove("is-checking");
                        return;
                    }

                    bypassLoginCheck = true;
                    loginForm.submit();
                })
                .catch(function () {
                    /* Status lookup is a convenience; never block a normal login
                       because the lookup endpoint is unavailable. */
                    bypassLoginCheck = true;
                    loginForm.submit();
                });
        });
    }

    /* Show the selected document name next to the upload box. */
    document.querySelectorAll(".ghd-upload-box input[type=file]").forEach(function (input) {
        input.addEventListener("change", function () {
            var box = input.closest(".ghd-upload-box");
            var target = box ? box.parentElement.querySelector("[data-upload-name]") : null;

            if (!target) {
                return;
            }

            if (input.files && input.files.length > 0) {
                target.textContent = "Seçilen belge: " + input.files[0].name;
                target.hidden = false;
            } else {
                target.textContent = "";
                target.hidden = true;
            }
        });
    });

    /* Keep radio cards visually selected and pre-select a matching document
       type unless the applicant has already chosen one manually. */
    var documentType = document.getElementById("document_type");
    var documentTypeTouched = false;

    if (documentType) {
        documentType.addEventListener("change", function () {
            documentTypeTouched = true;
        });
    }

    document.querySelectorAll(".ghd-radio-card input[type=radio]").forEach(function (input) {
        input.addEventListener("change", function () {
            document.querySelectorAll(".ghd-radio-card").forEach(function (card) {
                card.classList.remove("is-selected");
            });

            var card = input.closest(".ghd-radio-card");

            if (card) {
                card.classList.add("is-selected");
            }

            if (documentType && !documentTypeTouched) {
                documentType.value = input.value === "graduate" ? "diploma" : "student_certificate";
            }
        });
    });

    /* Destructive admin actions ask for confirmation. */
    document.querySelectorAll("[data-confirm]").forEach(function (element) {
        element.addEventListener("click", function (event) {
            if (!window.confirm(element.getAttribute("data-confirm"))) {
                event.preventDefault();
            }
        });
    });
})();
