"use strict";
toastr.options = {
    positionClass: toastPosition,
    timeOut: parseFloat(toastTimeOut) * 1000,
    showDuration: "300",
    hideDuration: "1000",
    extendedTimeOut: "1000",
    progressBar: true,
    closeButton: true,
};
$(document).on("click", ".delete", function (e) {
    e.preventDefault();
    var id = $(this).data("id");
    var type = $(this).data("type");
    var reload = $(this).data("reload"); // Get the value of data-reload attribute
    if (typeof reload !== "undefined" && reload === true) {
        reload = true;
    } else {
        reload = false;
    }
    var tableID = $(this).data("table") || "table";
    var destroy =
        type == "users"
            ? "delete_user"
            : type == "contract-type"
            ? "delete-contract-type"
            : type == "project-media" || type == "task-media"
            ? "delete-media"
            : type == "expense-type"
            ? "delete-expense-type"
            : type == "milestone"
            ? "delete-milestone"
            : "destroy";
    type =
        type == "contract-type"
            ? "contracts"
            : type == "project-media"
            ? "projects"
            : type == "task-media"
            ? "tasks"
            : type == "expense-type"
            ? "expenses"
            : type == "milestone"
            ? "projects"
            : type;
    $("#deleteModal").modal("show"); // show the confirmation modal
    $("#deleteModal").off("click", "#confirmDelete");
    $("#deleteModal").on("click", "#confirmDelete", function (e) {
        $("#confirmDelete").html(label_please_wait).attr("disabled", true);
        $.ajax({
            url: baseUrl + "/" + type + "/" + destroy + "/" + id,
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
            },
            success: function (response) {
                $("#confirmDelete").html(label_yes).attr("disabled", false);
                $("#deleteModal").modal("hide");
                if (response.error == false) {
                    if (reload) {
                        location.reload();
                    } else {
                        toastr.success(response.message);
                        if (tableID) {
                            $("#" + tableID).bootstrapTable("refresh");
                        }
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (data) {
                $("#confirmDelete").html(label_yes).attr("disabled", false);
                $("#deleteModal").modal("hide");
                toastr.error(label_something_went_wrong);
            },
        });
    });
});

$(document).on("click", ".delete-selected", function (e) {
    e.preventDefault();
    var table = $(this).data("table");
    var type = $(this).data("type");
    var reload = $(this).data("reload");
    var destroy =
        type == "users"
            ? "delete_multiple_user"
            : type == "contract-types"
            ? "delete-multiple-contract-type"
            : type == "project-media" || type == "task-media"
            ? "delete-multiple-media"
            : type == "expense-types"
            ? "delete-multiple-expense-type"
            : type == "milestones"
            ? "delete-multiple-milestone"
            : "destroy_multiple";
    type =
        type == "contract-types"
            ? "contracts"
            : type == "project-media"
            ? "projects"
            : type == "task-media"
            ? "tasks"
            : type == "expense-types"
            ? "expenses"
            : type == "milestones"
            ? "projects"
            : type;
    var selections = $("#" + table).bootstrapTable("getSelections");
    var selectedIds = selections.map(function (row) {
        return row.id; // Replace 'id' with the field containing the unique ID
    });
    if (selectedIds.length > 0) {
        $("#confirmDeleteSelectedModal").modal("show"); // show the confirmation modal
        $("#confirmDeleteSelectedModal").off(
            "click",
            "#confirmDeleteSelections"
        );
        $("#confirmDeleteSelectedModal").on(
            "click",
            "#confirmDeleteSelections",
            function (e) {
                $("#confirmDeleteSelections")
                    .html(label_please_wait)
                    .attr("disabled", true);
                $.ajax({
                    url: baseUrl + "/" + type + "/" + destroy,
                    data: {
                        ids: selectedIds,
                    },
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
                    },
                    success: function (response) {
                        $("#confirmDeleteSelections")
                            .html(label_yes)
                            .attr("disabled", false);
                        $("#confirmDeleteSelectedModal").modal("hide");
                        $("#" + table).bootstrapTable("refresh");
                        if (type == "settings/languages") {
                            location.reload();
                        } else {
                            if (reload) {
                                if (response.hasOwnProperty("message")) {
                                    if (response.error == false) {
                                        toastr.success(response["message"]);
                                        setTimeout(function () {
                                            location.reload();
                                        }, parseFloat(toastTimeOut) * 1000);
                                    } else {
                                        toastr.error(response["message"]);
                                    }
                                } else {
                                    location.reload();
                                }
                            } else {
                                if (response.error == false) {
                                    toastr.success(response.message);
                                } else {
                                    toastr.error(response.message);
                                }
                            }
                        }
                    },
                    error: function (data) {
                        $("#confirmDeleteSelections")
                            .html(label_yes)
                            .attr("disabled", false);
                        $("#confirmDeleteSelectedModal").modal("hide");
                        toastr.error(label_something_went_wrong);
                    },
                });
            }
        );
    } else {
        toastr.error(label_please_select_records_to_delete);
    }
});

$(document).ready(function () {
    // Handle delete selected notes or todos
    $("#delete-selected").on("click", function () {
        const itemType = $(this).data("type");
        const selectedIds = $(".selected-items:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        if (selectedIds.length > 0) {
            $("#confirmDeleteSelectedModal").modal("show"); // show the confirmation modal
            $("#confirmDeleteSelectedModal").off(
                "click",
                "#confirmDeleteSelections"
            );
            $("#confirmDeleteSelectedModal").on(
                "click",
                "#confirmDeleteSelections",
                function (e) {
                    $("#confirmDeleteSelections")
                        .html(label_please_wait)
                        .attr("disabled", true);
                    $.ajax({
                        url: baseUrl + "/" + itemType + "/destroy_multiple", // Adjust URL based on item type
                        data: {
                            ids: selectedIds,
                        },
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('input[name="_token"]').attr(
                                "value"
                            ),
                        },
                        success: function (response) {
                            $("#confirmDeleteSelections")
                                .html(label_yes)
                                .attr("disabled", false);
                            $("#confirmDeleteSelectedModal").modal("hide");
                            location.reload();
                        },
                        error: function (data) {
                            $("#confirmDeleteSelections")
                                .html(label_yes)
                                .attr("disabled", false);
                            $("#confirmDeleteSelectedModal").modal("hide");
                            toastr.error(label_something_went_wrong);
                        },
                    });
                }
            );
        } else {
            toastr.error(label_please_select_records_to_delete);
        }
    });
});

$("#select-all").on("click", function () {
    $(".selected-items").prop("checked", this.checked);
});

$(document).on("click", "#deleteAccount", function (e) {
    e.preventDefault();
    $("#deleteAccountModal").modal("show"); // show the confirmation modal
    $("#deleteAccountModal").off("click", "#confirmDeleteAccount");
    $("#deleteAccountModal").on("click", "#confirmDeleteAccount", function (e) {
        $("#confirmDeleteAccount")
            .html(label_please_wait)
            .attr("disabled", true);
        $.ajax({
            url: baseUrl + "/account/destroy",
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
            },
            success: function (response) {
                $("#confirmDeleteAccount")
                    .html(label_yes)
                    .attr("disabled", false);
                $("#deleteAccountModal").modal("hide");
                if (!response.error) {
                    toastr.success(response["message"]);
                    setTimeout(function () {
                        location.reload();
                    }, parseFloat(toastTimeOut) * 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (data) {
                $("#confirmDeleteAccount")
                    .html(label_yes)
                    .attr("disabled", false);
                $("#deleteAccountModal").modal("hide");
                toastr.error(label_something_went_wrong);
            },
        });
    });
});

function update_status(e) {
    var id = e["id"];
    var name = e["name"];
    var status;
    var is_checked = $("input[name=" + name + "]:checked");

    if (is_checked.length >= 1) {
        status = 1;
    } else {
        status = 0;
    }
    $.ajax({
        url: baseUrl + "/todos/update_status",
        type: "POST", // Use POST method
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        data: {
            _method: "PUT", // Specify the desired method
            id: id,
            status: status,
        },
        success: function (response) {
            if (response.error == false) {
                location.reload();
            } else {
                toastr.error(response.message);
            }
        },
    });
}

$(document).on("click", ".edit-todo", function () {
    var id = $(this).data("id");
    $("#edit_todo_modal").modal("show");
    $.ajax({
        url: baseUrl + "/todos/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            $("#todo_id").val(response.todo.id);
            $("#todo_title").val(response.todo.title);
            $("#todo_priority").val(response.todo.priority);
            $("#todo_description").val(response.todo.description);
        },
    });
});

$(document).on("click", ".edit-note", function () {
    var id = $(this).data("id");
    $("#edit_note_modal").modal("show");
    var classes = $("#note_color").attr("class").split(" ");
    var currentColorClass = classes.filter(function (className) {
        return className.startsWith("select-");
    })[0];
    $.ajax({
        url: baseUrl + "/notes/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            $("#note_id").val(response.note.id);
            $("#note_title").val(response.note.title);
            $("#note_color")
                .val(response.note.color)
                .removeClass(currentColorClass)
                .addClass("select-bg-label-" + response.note.color);
            var description =
                response.note.description !== null
                    ? response.note.description
                    : "";
            $("#edit_note_modal").find("#note_description").val(description);
        },
    });
});

$(document).on("click", ".edit-status", function () {
    var id = $(this).data("id");
    $("#edit_status_modal").modal("show");
    var classes = $("#status_color").attr("class").split(" ");
    var currentColorClass = classes.filter(function (className) {
        return className.startsWith("select-");
    })[0];
    $.ajax({
        url: baseUrl + "/status/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            $("#status_id").val(response.status.id);
            $("#status_title").val(response.status.title);
            $("#status_color")
                .val(response.status.color)
                .removeClass(currentColorClass)
                .addClass("select-bg-label-" + response.status.color);

            var modalForm = $("#edit_status_modal").find("form");
            var usersSelect = modalForm.find(
                '.js-example-basic-multiple[name="role_ids[]"]'
            );

            usersSelect.val(response.roles);
            usersSelect.trigger("change"); // Trigger change event to update select2
        },
    });
});

$(document).on("click", ".edit-tag", function () {
    var id = $(this).data("id");
    $("#edit_tag_modal").modal("show");
    var classes = $("#tag_color").attr("class").split(" ");
    var currentColorClass = classes.filter(function (className) {
        return className.startsWith("select-");
    })[0];
    $.ajax({
        url: baseUrl + "/tags/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            $("#tag_id").val(response.tag.id);
            $("#tag_title").val(response.tag.title);
            $("#tag_color")
                .val(response.tag.color)
                .removeClass(currentColorClass)
                .addClass("select-bg-label-" + response.tag.color);
        },
    });
});

$(document).on("click", ".edit-leave-request", function () {
    var id = $(this).data("id");
    $("#edit_leave_request_modal").modal("show");
    $.ajax({
        url: baseUrl + "/leave-requests/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            var formattedFromDate = moment(response.lr.from_date).format(
                js_date_format
            );
            var formattedToDate = moment(response.lr.to_date).format(
                js_date_format
            );
            var fromDateSelect = $("#edit_leave_request_modal").find(
                "#update_start_date"
            );
            var toDateSelect = $("#edit_leave_request_modal").find(
                "#update_end_date"
            );
            var reasonSelect = $("#edit_leave_request_modal").find(
                '[name="reason"]'
            );
            var commentSelect = $("#edit_leave_request_modal").find(
                '[name="comment"]'
            );
            var totalDaysSelect = $("#edit_leave_request_modal").find(
                "#update_total_days"
            );
            $("#lr_id").val(response.lr.id);
            $("#leaveUser").val(
                response.lr.user.first_name + " " + response.lr.user.last_name
            );
            fromDateSelect.val(formattedFromDate);
            toDateSelect.val(formattedToDate);
            initializeDateRangePicker("#update_start_date,#update_end_date");

            var start_date = moment(fromDateSelect.val(), js_date_format);
            var end_date = moment(toDateSelect.val(), js_date_format);
            var total_days = end_date.diff(start_date, "days") + 1;
            totalDaysSelect.val(total_days);

            if (response.lr.from_time && response.lr.to_time) {
                $("#updatePartialLeave")
                    .prop("checked", true)
                    .trigger("change");
                var fromTimeSelect = $("#edit_leave_request_modal").find(
                    '[name="from_time"]'
                );
                var toTimeSelect = $("#edit_leave_request_modal").find(
                    '[name="to_time"]'
                );
                fromTimeSelect.val(response.lr.from_time);
                toTimeSelect.val(response.lr.to_time);
            } else {
                $("#updatePartialLeave")
                    .prop("checked", false)
                    .trigger("change");
            }
            if (response.lr.visible_to_all) {
                $("#edit_leave_request_modal")
                    .find(".leaveVisibleToAll")
                    .prop("checked", true)
                    .trigger("change");
            } else {
                $("#edit_leave_request_modal")
                    .find(".leaveVisibleToAll")
                    .prop("checked", false)
                    .trigger("change");
                var visibleToSelect = $("#edit_leave_request_modal").find(
                    '.users_select[name="visible_to_ids[]"]'
                );

                if (
                    response.lr.visible_to_users &&
                    response.lr.visible_to_users.length > 0
                ) {
                    // Iterate through the users and add them to the select element
                    response.lr.visible_to_users.forEach(function (user) {
                        var userOption = new Option(
                            user.first_name + " " + user.last_name,
                            user.id,
                            true,
                            true
                        );
                        visibleToSelect.append(userOption);
                    });

                    // Trigger select2 to update the selected values
                    visibleToSelect.trigger("change");
                }
            }
            reasonSelect.val(response.lr.reason);
            commentSelect.val(response.lr.comment);
            $("input[name=status][value=" + response.lr.status + "]").prop(
                "checked",
                true
            );
        },
    });
});

$(document).on("click", ".edit-contract-type", function () {
    var id = $(this).data("id");
    $("#edit_contract_type_modal").modal("show");
    $.ajax({
        url: baseUrl + "/contracts/get-contract-type/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            $("#update_contract_type_id").val(response.ct.id);
            $("#contract_type").val(response.ct.type);
        },
    });
});

$(document).on("click", ".edit-contract", function () {
    var id = $(this).data("id");
    $("#edit_contract_modal").modal("show");
    $.ajax({
        url: baseUrl + "/contracts/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            if (response.error == false) {
                var formattedStartDate = moment(
                    response.contract.start_date
                ).format(js_date_format);
                var formattedEndDate = moment(
                    response.contract.end_date
                ).format(js_date_format);
                $("#contract_id").val(response.contract.id);
                $("#title").val(response.contract.title);
                $("#value").val(response.contract.value);
                var clientOption = new Option(
                    response.contract.client.first_name +
                        " " +
                        response.contract.client.last_name,
                    response.contract.client.id,
                    true,
                    true
                );
                $("#client_id").append(clientOption).trigger("change");
                var projectOption = new Option(
                    response.contract.project.title,
                    response.contract.project.id,
                    true,
                    true
                );
                $("#project_id").append(projectOption).trigger("change");
                var contractTypeOption = new Option(
                    response.contract.contract_type.type,
                    response.contract.contract_type.id,
                    true,
                    true
                );
                $("#contract_type_id")
                    .append(contractTypeOption)
                    .trigger("change");
                var description =
                    response.contract.description !== null
                        ? response.contract.description
                        : "";
                $("#update_contract_description").val(description);
                $("#update_start_date").val(formattedStartDate);
                $("#update_end_date").val(formattedEndDate);
                initializeDateRangePicker(
                    "#update_start_date, #update_end_date"
                );
            } else {
                location.reload();
            }
        },
    });
});
$(document).on("click", ".edit-expense-type", function () {
    var id = $(this).data("id");
    $("#edit_expense_type_modal").modal("show");
    $.ajax({
        url: baseUrl + "/expenses/get-expense-type/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            $("#update_expense_type_id").val(response.et.id);
            $("#expense_type_title").val(response.et.title);
            $("#expense_type_description").val(response.et.description);
        },
    });
});

$(document).on("click", ".edit-expense", function () {
    var id = $(this).data("id");
    $("#edit_expense_modal").modal("show");
    $.ajax({
        url: baseUrl + "/expenses/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            var formattedExpDate = moment(response.exp.expense_date).format(
                js_date_format
            );
            $("#update_expense_id").val(response.exp.id);
            $("#expense_title").val(response.exp.title);
            if (response.exp.expense_type) {
                if (response.exp.expense_type.title) {
                    var expenseTypeOption = new Option(
                        response.exp.expense_type.title,
                        response.exp.expense_type.id,
                        true, // Default selected
                        true // Selected
                    );
                    $("#expense_type_id")
                        .empty()
                        .append(expenseTypeOption)
                        .trigger("change");
                }
            }
            if (response.exp.user && response.exp.user.id) {
                var userOption = new Option(
                    response.exp.user.first_name +
                        " " +
                        response.exp.user.last_name, // Text for the option
                    response.exp.user.id, // Value for the option
                    true, // Default selected
                    true // Selected
                );
                $("#expense_user_id")
                    .empty()
                    .append(userOption)
                    .trigger("change");
            }

            $("#expense_amount").val(response.exp.amount);
            $("#update_expense_date").val(formattedExpDate);
            $("#expense_note").val(response.exp.note);
        },
    });
});

$(document).on("click", ".edit-language", function () {
    var id = $(this).data("id");
    $("#edit_language_modal").modal("show");
    $.ajax({
        url: baseUrl + "/settings/languages/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            $("#language_id").val(response.language.id);
            $("#language_title").val(response.language.name);
        },
    });
});

$(document).on("click", ".edit-payment", function () {
    var id = $(this).data("id");
    $("#edit_payment_modal").modal("show");
    $.ajax({
        url: baseUrl + "/payments/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            var formattedExpDate = moment(response.payment.payment_date).format(
                js_date_format
            );
            $("#update_payment_id").val(response.payment.id);
            // Update payment_user_id with user details
            if (response.payment.user && response.payment.user.id) {
                var userOption = new Option(
                    response.payment.user.first_name +
                        " " +
                        response.payment.user.last_name,
                    response.payment.user.id,
                    true,
                    true
                );
                $("#payment_user_id")
                    .empty()
                    .append(userOption)
                    .trigger("change");
            }

            // Update payment_invoice_id with invoice details
            if (response.payment.invoice && response.payment.invoice.id) {
                var invoiceOption = new Option(
                    label_invoice_id_prefix + "" + response.payment.invoice.id,
                    response.payment.invoice.id,
                    true,
                    true
                );
                $("#payment_invoice_id")
                    .empty()
                    .append(invoiceOption)
                    .trigger("change");
            }

            // Update payment_pm_id with payment method details
            if (
                response.payment.payment_method &&
                response.payment.payment_method.title
            ) {
                var pmOption = new Option(
                    response.payment.payment_method.title,
                    response.payment.payment_method.id,
                    true,
                    true
                );
                $("#payment_pm_id").empty().append(pmOption).trigger("change");
            }
            $("#payment_amount").val(response.payment.amount);
            $("#update_payment_date").val(formattedExpDate);
            $("#payment_note").val(response.payment.note);
        },
    });
});
function initializeDateRangePicker(inputSelector) {
    var modalsToCheck = [
        "#create_project_modal",
        "#edit_project_modal",
        "#create_task_modal",
        "#edit_task_modal",
        "#create_milestone_modal",
        "#edit_milestone_modal",
    ];
    $(inputSelector).each(function () {
        var $input = $(this);
        var isEmpty = $input.val() === ""; // Check if the input is empty
        var parentElModalId = "";
        var isInsideModal = modalsToCheck.some(function (modalId) {
            return $input.closest(modalId).length > 0;
        });
        if ($(inputSelector).closest(".modal").length) {
            // Get the ID of the closest modal
            var $modal = $input.closest(".modal"); // Define $modal
            parentElModalId = $modal.attr("id"); // Use $modal to get ID
        }
        var daterangepickerOptions = {
            alwaysShowCalendars: true,
            showCustomRangeLabel: true,
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: !isInsideModal,
            locale: {
                cancelLabel: "Clear",
                format: js_date_format,
            },
        };

        if (parentElModalId != "") {
            daterangepickerOptions.parentEl = "#" + parentElModalId;
        }

        // Conditionally add startDate
        if (!isEmpty) {
            daterangepickerOptions.startDate = moment(
                $input.val(),
                js_date_format
            );
        }

        $input.daterangepicker(daterangepickerOptions);

        // Handle autoUpdateInput behavior
        if (isEmpty) {
            $input.val(""); // Clear the input if it's initially empty
        }

        // Manually update input value on date selection
        $input.on("apply.daterangepicker", function (ev, picker) {
            $(this).val(picker.startDate.format(js_date_format));
        });
    });
}

$(document).on("click", "#set-as-default", function (e) {
    e.preventDefault();
    var lang = $(this).data("lang");
    $("#default_language_modal").modal("show"); // show the confirmation modal
    $("#default_language_modal").on("click", "#confirm", function () {
        $("#default_language_modal")
            .find("#confirm")
            .html(label_please_wait)
            .attr("disabled", true);
        $.ajax({
            url: baseUrl + "/settings/languages/set-default",
            type: "PUT",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
            },
            data: {
                lang: lang,
            },
            success: function (response) {
                $("#default_language_modal")
                    .find("#confirm")
                    .html(label_yes)
                    .attr("disabled", false);
                if (response.error == false) {
                    location.reload();
                } else {
                    toastr.error(response.message);
                    $("#default_language_modal").modal("hide");
                }
            },
        });
    });
});

$(document).on("click", "#set-default-view", function (e) {
    e.preventDefault();
    var type = $(this).data("type");
    var view = $(this).data("view");
    var url = baseUrl + "/save-" + type + "-view-preference";
    $("#set_default_view_modal").modal("show");
    $("#set_default_view_modal").off("click", "#confirm");
    $("#set_default_view_modal").on("click", "#confirm", function () {
        $("#set_default_view_modal")
            .find("#confirm")
            .html(label_please_wait)
            .attr("disabled", true);
        $.ajax({
            url: url,
            type: "PUT",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
            },
            data: {
                type: type,
                view: view,
            },
            success: function (response) {
                $("#set_default_view_modal")
                    .find("#confirm")
                    .html(label_yes)
                    .attr("disabled", false);
                if (response.error == false) {
                    $("#set-default-view")
                        .text(label_default_view)
                        .removeClass("bg-secondary")
                        .addClass("bg-primary");
                    $("#set_default_view_modal").modal("hide");
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
        });
    });
});

$(document).on("click", "#remove-participant", function (e) {
    e.preventDefault();
    $("#leaveWorkspaceModal").modal("show"); // show the confirmation modal
    $("#leaveWorkspaceModal").on("click", "#confirm", function () {
        $("#leaveWorkspaceModal")
            .find("#confirm")
            .html(label_please_wait)
            .attr("disabled", true);
        $.ajax({
            url: baseUrl + "/workspaces/remove_participant",
            type: "GET",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
            },
            success: function (response) {
                $("#leaveWorkspaceModal")
                    .find("#confirm")
                    .html(label_yes)
                    .attr("disabled", false);
                location.reload();
            },
            error: function (data) {
                $("#leaveWorkspaceModal")
                    .find("#confirm")
                    .html(label_yes)
                    .attr("disabled", false);
                location.reload();
            },
        });
    });
});
function resetDateFields($form) {
    var currentDate = moment(new Date()).format(js_date_format); // Get current date
    var modalsToCheck = [
        "#create_project_modal",
        "#edit_project_modal",
        "#create_task_modal",
        "#edit_task_modal",
        "#create_milestone_modal",
        "#edit_milestone_modal",
    ];
    $form.find("input").each(function () {
        var $this = $(this);
        if ($this.data("daterangepicker")) {
            // Destroy old instance
            $this.data("daterangepicker").remove();

            var isInsideModal = modalsToCheck.some(function (modalId) {
                return $form.closest(modalId).length > 0;
            });

            // Get the ID of the closest modal
            var $modal = $this.closest(".modal"); // Define $modal
            var parentElModalId = $modal.attr("id"); // Use $modal to get ID
            // Reinitialize with new value
            if (!isInsideModal) {
                $this.val(currentDate);
            }

            var daterangepickerOptions = {
                alwaysShowCalendars: true,
                showCustomRangeLabel: true,
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: !isInsideModal,
                locale: {
                    cancelLabel: "Clear",
                    format: js_date_format,
                },
                parentEl: "#" + parentElModalId,
            };

            if (!isInsideModal) {
                daterangepickerOptions.startDate = moment(
                    currentDate,
                    js_date_format
                );
            }

            $this.daterangepicker(daterangepickerOptions);

            // Clear the input if inside modal
            if (isInsideModal) {
                $this.val("");
            }

            // Manually update input value on date selection
            $this.on("apply.daterangepicker", function (ev, picker) {
                $(this).val(picker.startDate.format(js_date_format));
            });
        }
    });
}

$(document).ready(function () {
    var idsToProcess = [
        "#start_date",
        "#end_date",
        "#update_start_date",
        "#update_end_date",
        "#lr_end_date",
        "#meeting_end_date",
        "#expense_date",
        "#update_expense_date",
        "#payment_date",
        "#update_payment_date",
        "#update_milestone_start_date",
        "#update_milestone_end_date",
        "#task_start_date",
        "#task_end_date",
    ];
    var modalsToCheck = [
        "#create_project_modal",
        "#edit_project_modal",
        "#create_task_modal",
        "#edit_task_modal",
        "#create_milestone_modal",
        "#edit_milestone_modal",
    ];
    var daterangepickerOptions = {
        alwaysShowCalendars: true,
        showCustomRangeLabel: true,
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true,
        locale: {
            cancelLabel: "Clear",
            format: js_date_format,
        },
    };
    idsToProcess.forEach(function (id) {
        if ($(id).length) {
            var isInsideModal = false;
            var parentElModalId = "";

            if ($(id).closest(".modal").length) {
                // Get the ID of the closest modal
                var $modal = $(id).closest(".modal"); // Define $modal
                parentElModalId = $modal.attr("id"); // Use $modal to get ID
            }

            modalsToCheck.forEach(function (modalId) {
                if ($(id).closest(modalId).length > 0) {
                    isInsideModal = true;
                }
            });

            // Append parentEl to daterangepickerOptions if inside a modal
            if (parentElModalId != "") {
                daterangepickerOptions.parentEl = "#" + parentElModalId;
            }
            if (
                isInsideModal ||
                ($(id).attr("data-defaultDate") !== undefined &&
                    $(id).attr("data-defaultDate") === "false")
            ) {
                daterangepickerOptions.autoUpdateInput = false;
            }
            // Set default value if empty and not inside a modal and either the data-defaultDate attribute is not specified or it is set to 'true'
            if (
                $(id).val() == "" &&
                !isInsideModal &&
                ($(id).attr("data-defaultDate") == undefined ||
                    $(id).attr("data-defaultDate") == "true")
            ) {
                $(id).val(moment().format(js_date_format));
            }
            $(id).daterangepicker(daterangepickerOptions);

            $(id).on("apply.daterangepicker", function (ev, picker) {
                $(this).val(picker.startDate.format(js_date_format));
            });

            $(id).on("cancel.daterangepicker", function () {
                $(this).val("");
            });
        }
    });

    // Define the IDs you want to process
    var idsToProcess = ["#dob", "#doj"];
    var minDateStr = "01/01/1950";
    var minDate = moment(minDateStr, "DD/MM/YYYY");
    var maxDate = moment(); // Current date

    // Loop through the IDs
    idsToProcess.forEach(function (id) {
        if ($(id).length) {
            $(id).daterangepicker({
                alwaysShowCalendars: true,
                showCustomRangeLabel: true,
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                minDate: minDate,
                maxDate: maxDate, // Set max date to the current date
                locale: {
                    cancelLabel: "Clear",
                    format: js_date_format,
                },
            });

            $(id).on("apply.daterangepicker", function (ev, picker) {
                $(this).val(picker.startDate.format(js_date_format));
            });
        }
    });
});

$(document).ready(function () {
    $(
        "#report_start_date_between,#report_end_date_between,#filter_date_range,#ie_date_between,#ms_date_between,#start_date_between,#end_date_between,#project_date_between,#project_start_date_between,#project_end_date_between,#task_date_between,#task_start_date_between,#task_end_date_between,#lr_date_between,#lr_start_date_between,#lr_end_date_between,#contract_date_between,#contract_start_date_between,#contract_end_date_between,#timesheet_date_between,#timesheet_start_date_between,#timesheet_end_date_between,#meeting_date_between,#meeting_start_date_between,#meeting_end_date_between,#activity_log_between_date,#notification_between_date,#expense_from_date_between,#payment_date_between"
    ).daterangepicker({
        alwaysShowCalendars: true,
        showCustomRangeLabel: true,
        singleDatePicker: false,
        showDropdowns: true,
        autoUpdateInput: false,
        locale: {
            cancelLabel: "Clear",
            format: js_date_format,
        },
    });
    $(
        "#report_start_date_between,#report_end_date_between,#filter_date_range,#ie_date_between,#ms_date_between,#start_date_between,#end_date_between,#project_date_between,#project_start_date_between,#project_end_date_between,#task_date_between,#task_start_date_between,#task_end_date_between,#lr_date_between,#lr_start_date_between,#lr_end_date_between,#contract_date_between,#contract_start_date_between,#contract_end_date_between,#timesheet_date_between,#timesheet_start_date_between,#timesheet_end_date_between,#meeting_date_between,#meeting_start_date_between,#meeting_end_date_between,#activity_log_between_date,#notification_between_date,#expense_from_date_between,#payment_date_between"
    ).on("apply.daterangepicker", function (ev, picker) {
        $(this).val(
            picker.startDate.format(js_date_format) +
                " " +
                label_to +
                " " +
                picker.endDate.format(js_date_format)
        );
    });
});

if ($("#project_start_date_between").length) {
    $("#project_start_date_between").on(
        "apply.daterangepicker",
        function (ev, picker) {
            var startDate = picker.startDate.format("YYYY-MM-DD");
            var endDate = picker.endDate.format("YYYY-MM-DD");

            $("#project_start_date_from").val(startDate);
            $("#project_start_date_to").val(endDate);

            $("#projects_table").bootstrapTable("refresh");
        }
    );

    $("#project_start_date_between").on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $("#project_start_date_from").val("");
            $("#project_start_date_to").val("");
            $("#project_start_date_between").val("");
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            picker.updateElement();
            $("#projects_table").bootstrapTable("refresh");
        }
    );

    $("#project_end_date_between").on(
        "apply.daterangepicker",
        function (ev, picker) {
            var startDate = picker.startDate.format("YYYY-MM-DD");
            var endDate = picker.endDate.format("YYYY-MM-DD");

            $("#project_end_date_from").val(startDate);
            $("#project_end_date_to").val(endDate);

            $("#projects_table").bootstrapTable("refresh");
        }
    );
    $("#project_end_date_between").on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $("#project_end_date_from").val("");
            $("#project_end_date_to").val("");
            $("#project_end_date_between").val("");
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            picker.updateElement();
            $("#projects_table").bootstrapTable("refresh");
        }
    );
}

if ($("#task_start_date_between").length) {
    $("#task_start_date_between").on(
        "apply.daterangepicker",
        function (ev, picker) {
            var startDate = picker.startDate.format("YYYY-MM-DD");
            var endDate = picker.endDate.format("YYYY-MM-DD");

            $("#task_start_date_from").val(startDate);
            $("#task_start_date_to").val(endDate);

            $("#task_table").bootstrapTable("refresh");
        }
    );

    $("#task_start_date_between").on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $("#task_start_date_from").val("");
            $("#task_start_date_to").val("");
            $("#task_start_date_between").val("");
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            picker.updateElement();
            $("#task_table").bootstrapTable("refresh");
        }
    );

    $("#task_end_date_between").on(
        "apply.daterangepicker",
        function (ev, picker) {
            var startDate = picker.startDate.format("YYYY-MM-DD");
            var endDate = picker.endDate.format("YYYY-MM-DD");

            $("#task_end_date_from").val(startDate);
            $("#task_end_date_to").val(endDate);

            $("#task_table").bootstrapTable("refresh");
        }
    );
    $("#task_end_date_between").on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $("#task_end_date_from").val("");
            $("#task_end_date_to").val("");
            $("#task_end_date_between").val("");
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            picker.updateElement();
            $("#task_table").bootstrapTable("refresh");
        }
    );
}

if ($("#timesheet_start_date_between").length) {
    $("#timesheet_start_date_between").on(
        "apply.daterangepicker",
        function (ev, picker) {
            var startDate = picker.startDate.format("YYYY-MM-DD");
            var endDate = picker.endDate.format("YYYY-MM-DD");

            $("#timesheet_start_date_from").val(startDate);
            $("#timesheet_start_date_to").val(endDate);

            $("#timesheet_table").bootstrapTable("refresh");
        }
    );

    $("#timesheet_start_date_between").on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $("#timesheet_start_date_from").val("");
            $("#timesheet_start_date_to").val("");
            $("#timesheet_start_date_between").val("");
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            picker.updateElement();
            $("#timesheet_table").bootstrapTable("refresh");
        }
    );

    $("#timesheet_end_date_between").on(
        "apply.daterangepicker",
        function (ev, picker) {
            var startDate = picker.startDate.format("YYYY-MM-DD");
            var endDate = picker.endDate.format("YYYY-MM-DD");

            $("#timesheet_end_date_from").val(startDate);
            $("#timesheet_end_date_to").val(endDate);

            $("#timesheet_table").bootstrapTable("refresh");
        }
    );
    $("#timesheet_end_date_between").on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $("#timesheet_end_date_from").val("");
            $("#timesheet_end_date_to").val("");
            $("#timesheet_end_date_between").val("");
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            picker.updateElement();
            $("#timesheet_table").bootstrapTable("refresh");
        }
    );
}

if ($("#meeting_start_date_between").length) {
    $("#meeting_start_date_between").on(
        "apply.daterangepicker",
        function (ev, picker) {
            var startDate = picker.startDate.format("YYYY-MM-DD");
            var endDate = picker.endDate.format("YYYY-MM-DD");

            $("#meeting_start_date_from").val(startDate);
            $("#meeting_start_date_to").val(endDate);

            $("#meetings_table").bootstrapTable("refresh");
        }
    );

    $("#meeting_start_date_between").on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $("#meeting_start_date_from").val("");
            $("#meeting_start_date_to").val("");
            $("#meeting_start_date_between").val("");
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            picker.updateElement();
            $("#meetings_table").bootstrapTable("refresh");
        }
    );

    $("#meeting_end_date_between").on(
        "apply.daterangepicker",
        function (ev, picker) {
            var startDate = picker.startDate.format("YYYY-MM-DD");
            var endDate = picker.endDate.format("YYYY-MM-DD");

            $("#meeting_end_date_from").val(startDate);
            $("#meeting_end_date_to").val(endDate);

            $("#meetings_table").bootstrapTable("refresh");
        }
    );
    $("#meeting_end_date_between").on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $("#meeting_end_date_from").val("");
            $("#meeting_end_date_to").val("");
            $("#meeting_end_date_between").val("");
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            picker.updateElement();
            $("#meetings_table").bootstrapTable("refresh");
        }
    );
}

$(
    "textarea#footer_text,textarea#contract_description,textarea#update_contract_description,textarea.description"
).tinymce({
    height: 250,
    menubar: false,
    plugins: [
        "link",
        "a11ychecker",
        "advlist",
        "advcode",
        "advtable",
        "autolink",
        "checklist",
        "export",
        "lists",
        "link",
        "image",
        "charmap",
        "preview",
        "anchor",
        "searchreplace",
        "visualblocks",
        "powerpaste",
        "fullscreen",
        "formatpainter",
        "insertdatetime",
        "media",
        "table",
        "help",
        "wordcount",
        "emoticons",
        "code",
    ],
    toolbar:
        "link | undo redo | a11ycheck casechange blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist checklist outdent indent | removeformat | code blockquote emoticons table help",
});

$(
    "textarea#privacy_policy,textarea#terms_conditions,textarea#about_us"
).tinymce({
    height: 400,
    menubar: false,
    plugins: [
        "link",
        "a11ychecker",
        "advlist",
        "advcode",
        "advtable",
        "autolink",
        "checklist",
        "export",
        "lists",
        "link",
        "image",
        "charmap",
        "preview",
        "anchor",
        "searchreplace",
        "visualblocks",
        "powerpaste",
        "fullscreen",
        "formatpainter",
        "insertdatetime",
        "media",
        "table",
        "help",
        "wordcount",
        "emoticons",
        "code",
    ],
    toolbar:
        "link | undo redo | a11ycheck casechange blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist checklist outdent indent | removeformat | code blockquote emoticons table help",
});

document.addEventListener("focusin", function (e) {
    if (
        e.target.closest(
            ".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root"
        ) !== null
    ) {
        e.stopImmediatePropagation();
    }
});

$(document).on("submit", ".form-submit-event", function (e) {
    e.preventDefault();
    if ($("#net_payable").length > 0) {
        var net_payable = $("#net_payable").text();
        $("#net_pay").val(net_payable);
    }
    var formData = new FormData(this);
    var currentForm = $(this);
    var submit_btn = $(this).find("#submit_btn");
    var btn_html = submit_btn.html();
    var btn_val = submit_btn.val();
    var redirect_url = currentForm.find('input[name="redirect_url"]').val();
    redirect_url =
        typeof redirect_url !== "undefined" && redirect_url ? redirect_url : "";
    var button_text =
        btn_html != "" || btn_html != "undefined" ? btn_html : btn_val;
    var tableInput = currentForm.find('input[name="table"]');
    var tableID = tableInput.length ? tableInput.val() : "table";
    if (currentForm.closest("#edit_contract_modal").length > 0) {
        // Ensure Dropzone is initialized for #contract-dropzone
        if (Dropzone.instances.length > 0) {
            var dropzoneInstance = Dropzone.forElement("#contract-dropzone");
            if (dropzoneInstance.getAcceptedFiles().length > 0) {
                dropzoneInstance.getAcceptedFiles().forEach(function (file) {
                    formData.append("signed_pdf", file);
                });
            }
        }
    }
    $.ajax({
        type: "POST",
        url: $(this).attr("action"),
        data: formData,
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        beforeSend: function () {
            submit_btn.html(label_please_wait);
            submit_btn.attr("disabled", true);
        },
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (result) {
            submit_btn.html(button_text);
            submit_btn.attr("disabled", false);
            if (result["error"] == true) {
                toastr.error(result["message"]);
            } else {
                var modalWithClass = $(".modal.fade.show");
                var idOfModal = modalWithClass.attr("id");
                $("#" + idOfModal).modal("hide");
                if ($(".empty-state").length > 0) {
                    if (result.hasOwnProperty("message")) {
                        toastr.success(result["message"]);
                        setTimeout(
                            handleRedirection,
                            parseFloat(toastTimeOut) * 1000
                        );
                    } else {
                        handleRedirection();
                    }
                } else {
                    if (currentForm.find('input[name="dnr"]').length > 0) {
                        if (modalWithClass.length > 0) {
                            $("#" + tableID).bootstrapTable("refresh");
                            currentForm[0].reset();
                            var partialLeaveCheckbox = $("#partialLeave");
                            if (partialLeaveCheckbox.length) {
                                partialLeaveCheckbox.trigger("change");
                            }
                            resetDateFields(currentForm);
                            if (idOfModal == "create_status_modal") {
                                var dropdownSelector = modalWithClass.find(
                                    'select[name="status_id"]'
                                );
                                if (dropdownSelector.length) {
                                    var newItem = result.status;
                                    var newOption = $("<option></option>")
                                        .attr("value", newItem.id)
                                        .attr("data-color", newItem.color)
                                        .attr("selected", true)
                                        .text(
                                            newItem.title +
                                                " (" +
                                                newItem.color +
                                                ")"
                                        );
                                    $(dropdownSelector).append(newOption);

                                    var openModalId = dropdownSelector
                                        .closest(".modal.fade.show")
                                        .attr("id");

                                    // List of all possible modal IDs
                                    var modalIds = [
                                        "#create_project_modal",
                                        "#edit_project_modal",
                                        "#create_task_modal",
                                        "#edit_task_modal",
                                    ];

                                    // Iterate through each modal ID
                                    modalIds.forEach(function (modalId) {
                                        // If the modal ID is not the open one
                                        if (modalId !== "#" + openModalId) {
                                            // Find the select element within the modal
                                            var otherModalSelector = $(
                                                modalId
                                            ).find('select[name="status_id"]');

                                            // Create a new option without 'selected' attribute
                                            var otherOption = $(
                                                "<option></option>"
                                            )
                                                .attr("value", newItem.id)
                                                .attr(
                                                    "data-color",
                                                    newItem.color
                                                )
                                                .text(
                                                    newItem.title +
                                                        " (" +
                                                        newItem.color +
                                                        ")"
                                                );

                                            // Append the option to the select element in the modal
                                            otherModalSelector.append(
                                                otherOption
                                            );
                                        }
                                    });
                                }
                            }
                            if (idOfModal == "create_priority_modal") {
                                var dropdownSelector = modalWithClass.find(
                                    'select[name="priority_id"]'
                                );
                                if (dropdownSelector.length) {
                                    var newItem = result.priority;
                                    var newOption = $("<option></option>")
                                        .attr("value", newItem.id)
                                        .attr(
                                            "class",
                                            "badge bg-label-" + newItem.color
                                        )
                                        .attr("selected", true)
                                        .text(
                                            newItem.title +
                                                " (" +
                                                newItem.color +
                                                ")"
                                        );
                                    $(dropdownSelector).append(newOption);

                                    var openModalId = dropdownSelector
                                        .closest(".modal.fade.show")
                                        .attr("id");

                                    // List of all possible modal IDs
                                    var modalIds = [
                                        "#create_project_modal",
                                        "#edit_project_modal",
                                        "#create_task_modal",
                                        "#edit_task_modal",
                                    ];

                                    // Iterate through each modal ID
                                    modalIds.forEach(function (modalId) {
                                        // If the modal ID is not the open one
                                        if (modalId !== "#" + openModalId) {
                                            // Find the select element within the modal
                                            var otherModalSelector = $(
                                                modalId
                                            ).find(
                                                'select[name="priority_id"]'
                                            );

                                            // Create a new option without 'selected' attribute
                                            var otherOption = $(
                                                "<option></option>"
                                            )
                                                .attr("value", newItem.id)
                                                .attr(
                                                    "class",
                                                    "badge bg-label-" +
                                                        newItem.color
                                                )
                                                .text(
                                                    newItem.title +
                                                        " (" +
                                                        newItem.color +
                                                        ")"
                                                );

                                            // Append the option to the select element in the modal
                                            otherModalSelector.append(
                                                otherOption
                                            );
                                        }
                                    });
                                }
                            }
                            if (idOfModal == "create_tag_modal") {
                                var dropdownSelector = modalWithClass.find(
                                    'select[name="tag_ids[]"]'
                                );
                                if (dropdownSelector.length) {
                                    var newItem = result.tag;
                                    var newOption = $("<option></option>")
                                        .attr("value", newItem.id)
                                        .attr("data-color", newItem.color)
                                        .attr("selected", true)
                                        .text(newItem.title);
                                    $(dropdownSelector).append(newOption);
                                    $(dropdownSelector).trigger("change");

                                    var openModalId = dropdownSelector
                                        .closest(".modal.fade.show")
                                        .attr("id");

                                    // List of all possible modal IDs
                                    var modalIds = [
                                        "#create_project_modal",
                                        "#edit_project_modal",
                                    ];

                                    // Iterate through each modal ID
                                    modalIds.forEach(function (modalId) {
                                        // If the modal ID is not the open one
                                        if (modalId !== "#" + openModalId) {
                                            // Find the select element within the modal
                                            var otherModalSelector = $(
                                                modalId
                                            ).find('select[name="tag_ids[]"]');

                                            // Create a new option without 'selected' attribute
                                            var otherOption = $(
                                                "<option></option>"
                                            )
                                                .attr("value", newItem.id)
                                                .attr(
                                                    "data-color",
                                                    newItem.color
                                                )
                                                .text(newItem.title);

                                            // Append the option to the select element in the modal
                                            otherModalSelector.append(
                                                otherOption
                                            );
                                        }
                                    });
                                }
                            }
                            if (idOfModal == "create_item_modal") {
                                var dropdownSelector = $("#item_id");
                                if (dropdownSelector.length) {
                                    var newItem = result.item;
                                    var newOption = $("<option></option>")
                                        .attr("value", newItem.id)
                                        .attr("selected", true)
                                        .text(newItem.title);
                                    $(dropdownSelector).append(newOption);
                                    $(dropdownSelector).trigger("change");
                                }
                            }
                            if (idOfModal === "create_contract_type_modal") {
                                var dropdownSelector = modalWithClass.find(
                                    'select[name="contract_type_id"]'
                                );
                                if (dropdownSelector.length) {
                                    var newItem = result.ct;
                                    var newOption = $("<option></option>")
                                        .attr("value", newItem.id)
                                        .attr("selected", true)
                                        .text(newItem.type);

                                    // Append and select the new option in the current modal
                                    dropdownSelector.append(newOption);
                                    var openModalId = dropdownSelector
                                        .closest(".modal.fade.show")
                                        .attr("id");
                                    var otherModalId =
                                        openModalId === "create_contract_modal"
                                            ? "#edit_contract_modal"
                                            : "#create_contract_modal";
                                    var otherModalSelector = $(
                                        otherModalId
                                    ).find('select[name="contract_type_id"]');

                                    // Create a new option for the other modal without 'selected' attribute
                                    var otherOption = $("<option></option>")
                                        .attr("value", newItem.id)
                                        .text(newItem.type);

                                    // Append the option to the other modal
                                    otherModalSelector.append(otherOption);
                                }
                            }

                            if (idOfModal == "create_pm_modal") {
                                var dropdownSelector = $(
                                    'select[name="payment_method_id"]'
                                );
                                if (dropdownSelector.length) {
                                    var newItem = result.pm;
                                    var newOption = $("<option></option>")
                                        .attr("value", newItem.id)
                                        .attr("selected", true)
                                        .text(newItem.title);
                                    $(dropdownSelector).append(newOption);
                                }
                            }

                            if (idOfModal == "create_allowance_modal") {
                                var dropdownSelector = $(
                                    'select[name="allowance_id"]'
                                );
                                if (dropdownSelector.length) {
                                    var newItem = result.allowance;
                                    var newOption = $("<option></option>")
                                        .attr("value", newItem.id)
                                        .attr("selected", true)
                                        .text(newItem.title);
                                    $(dropdownSelector)
                                        .append(newOption)
                                        .trigger("change");
                                }
                            }

                            if (idOfModal == "create_deduction_modal") {
                                var dropdownSelector = $(
                                    'select[name="deduction_id"]'
                                );
                                if (dropdownSelector.length) {
                                    var newItem = result.deduction;
                                    var newOption = $("<option></option>")
                                        .attr("value", newItem.id)
                                        .attr("selected", true)
                                        .text(newItem.title);
                                    $(dropdownSelector)
                                        .append(newOption)
                                        .trigger("change");
                                }
                            }
                        }
                        toastr.success(result["message"]);
                        currentForm.find(".error-message").html("");
                    } else {
                        if (result.hasOwnProperty("message")) {
                            toastr.success(result["message"]);
                            setTimeout(
                                handleRedirection,
                                parseFloat(toastTimeOut) * 1000
                            );
                        } else {
                            handleRedirection();
                        }
                    }
                }
            }
        },
        error: function (xhr, status, error) {
            submit_btn.html(button_text);
            submit_btn.attr("disabled", false);
            if (xhr.status === 422) {
                // Handle validation errors here
                var response = xhr.responseJSON; // Assuming you're returning JSON

                // You can access validation errors from the response object
                var errors = response.errors;
                if (errors["country_code"]) {
                    errors["phone"] = errors["country_code"];
                    delete errors["country_code"];
                }
                // Example: Display the first validation error message
                toastr.error(label_please_correct_errors);
                // Assuming you have a list of all input fields with error messages
                var inputFields = currentForm.find(
                    "input[name], select[name], textarea[name]"
                );
                inputFields = $(inputFields.toArray().reverse());

                // Iterate through all input fields
                inputFields.each(function () {
                    var inputField = $(this);
                    var fieldName = inputField.attr("name");
                    var errorMessageElement = $(
                        '<span class="text-danger error-message"></span>'
                    );

                    if (errors && errors[fieldName]) {
                        if (
                            inputField.attr("type") !== "radio" &&
                            inputField.attr("type") !== "hidden"
                        ) {
                            // Remove existing error messages
                            if (
                                inputField.hasClass("select2-hidden-accessible")
                            ) {
                                inputField
                                    .parent()
                                    .find(".text-danger.error-message")
                                    .remove();
                                inputField
                                    .siblings(".select2")
                                    .after(errorMessageElement);
                            } else if (
                                inputField.closest(".input-group-merge")
                                    .length > 0
                            ) {
                                var inputGroup =
                                    inputField.closest(".input-group-merge");
                                inputGroup
                                    .next(".text-danger.error-message")
                                    .remove();
                                inputGroup.after(errorMessageElement);
                            } else if (
                                inputField.closest(".input-group").length > 0
                            ) {
                                var inputGroup =
                                    inputField.closest(".input-group");
                                inputGroup
                                    .next(".text-danger.error-message")
                                    .remove();
                                inputGroup.after(errorMessageElement);
                            } else if (
                                inputField.is("textarea#privacy_policy")
                            ) {
                                // Handle textarea with id privacy_policy
                                inputField
                                    .next(".text-danger.error-message")
                                    .remove();
                                inputField
                                    .parent()
                                    .find(".mt-2")
                                    .first()
                                    .before(errorMessageElement);
                            } else {
                                inputField
                                    .next(".text-danger.error-message")
                                    .remove();
                                inputField.after(errorMessageElement);
                            }
                        }

                        // If there is a validation error message for this field, display it
                        if (errors[fieldName][0].includes("required")) {
                            errorMessageElement.text("This field is required.");
                        } else {
                            errorMessageElement.text(errors[fieldName]);
                        }
                        inputField[0].scrollIntoView({
                            behavior: "smooth",
                            block: "start",
                        });
                        inputField.focus();
                    } else {
                        // If there is no validation error message, clear the existing message
                        var existingErrorMessage = inputField.next(
                            ".text-danger.error-message"
                        );
                        if (inputField.hasClass("select2-hidden-accessible")) {
                            existingErrorMessage = inputField
                                .parent()
                                .find(".text-danger.error-message");
                        } else if (
                            inputField.closest(".input-group-merge").length > 0
                        ) {
                            var inputGroup =
                                inputField.closest(".input-group-merge");
                            existingErrorMessage = inputGroup.next(
                                ".text-danger.error-message"
                            );
                        } else if (
                            inputField.closest(".input-group").length > 0
                        ) {
                            var inputGroup = inputField.closest(".input-group");
                            existingErrorMessage = inputGroup.next(
                                ".text-danger.error-message"
                            );
                        }

                        if (existingErrorMessage.length > 0) {
                            existingErrorMessage.remove();
                        }
                    }
                });
            } else {
                var response = xhr.responseJSON;
                if (response && response.message && response.exception) {
                    var errorMessage = response.message;
                    var match = errorMessage.match(
                        /Access denied for user '([^']+)'@/
                    );
                    if (match) {
                        var dbUser = match[1];
                        var customErrorMessage =
                            "Please try changing the password for database user " +
                            dbUser +
                            " or recreate the database.";
                        toastr.error(customErrorMessage);
                    } else {
                        // Check if it's an SQL error and extract relevant part
                        var sqlErrorPattern = /SQLSTATE\[[0-9]+\]: [^\(]+/;
                        var nonSqlErrorPattern =
                            /\b(?!SQLSTATE\[[0-9]+\]): [^\r\n]+/;

                        if (sqlErrorPattern.test(errorMessage)) {
                            var shortErrorMessage =
                                errorMessage.match(sqlErrorPattern)[0];
                            toastr.error(shortErrorMessage);
                        } else if (nonSqlErrorPattern.test(errorMessage)) {
                            var shortErrorMessage =
                                errorMessage.match(nonSqlErrorPattern)[0];
                            toastr.error(shortErrorMessage);
                        } else {
                            toastr.error("An unexpected error occurred.");
                        }
                    }
                } else {
                    toastr.error("An unexpected error occurred.");
                }
            }
        },
    });
    function handleRedirection() {
        if (redirect_url === "") {
            window.location.reload(); // Reload the current page
        } else {
            window.location.href = redirect_url; // Redirect to specified URL
        }
    }
});

// Click event handler for the favorite icon
$(document).on("click", ".favorite-icon", function () {
    var icon = $(this);
    var entityId = icon.data("id"); // ID of the entity (e.g., project, task)
    var entityType = icon.data("type") || "projects"; // Default to 'projects' if no type is provided
    var isFavorite = icon.attr("data-favorite");
    isFavorite = isFavorite == 1 ? 0 : 1;
    var dataTitle = icon.data("bs-original-title");
    var temp = dataTitle !== undefined ? "data-bs-original-title" : "title";

    // Send an AJAX request to update the favorite status
    $.ajax({
        url: baseUrl + "/" + entityType + "/update-favorite/" + entityId,
        type: "PATCH",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            is_favorite: isFavorite,
        },
        success: function (response) {
            if (response.error == false) {
                if (isFavorite == 0) {
                    icon.removeClass("bxs-star");
                    icon.addClass("bx-star");
                    icon.attr(temp, add_favorite); // Update the tooltip text
                } else {
                    icon.removeClass("bx-star");
                    icon.addClass("bxs-star");
                    icon.attr(temp, remove_favorite); // Update the tooltip text
                }
                icon.attr("data-favorite", isFavorite);
                if (isFavorite == 0) {
                    toastr.success(label_removed_from_favorite_successfully);
                } else {
                    toastr.success(label_marked_as_favorite_successfully);
                }
            } else {
                toastr.error(response.message);
            }
        },
        error: function () {
            // Handle errors if necessary
            toastr.error(label_err_try_again);
        },
    });
});

// Click event handler for the pinned icon
$(document).on("click", ".pinned-icon", function () {
    var icon = $(this);
    var entityId = icon.data("id"); // ID of the entity (e.g., project, task)
    var entityType = icon.data("type") || "projects"; // Default to 'projects' if no type is provided
    var isPinned = icon.attr("data-pinned");
    isPinned = isPinned == 1 ? 0 : 1;
    var requireReload =
        icon.data("require_reload") !== undefined
            ? icon.data("require_reload")
            : 1;
    var dataTitle = icon.data("bs-original-title");
    var temp = dataTitle !== undefined ? "data-bs-original-title" : "title";

    // Send an AJAX request to update the pinned status
    $.ajax({
        url: baseUrl + "/" + entityType + "/update-pinned/" + entityId,
        type: "PATCH",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            is_pinned: isPinned,
        },
        success: function (response) {
            if (response.error == false) {
                if (isPinned == 0) {
                    icon.removeClass("bxs-pin");
                    icon.addClass("bx-pin");
                    icon.attr(temp, label_click_pin); // Update the tooltip text
                } else {
                    icon.removeClass("bx-pin");
                    icon.addClass("bxs-pin");
                    icon.attr(temp, label_click_unpin); // Update the tooltip text
                }
                if (requireReload) {
                    // Show success message
                    toastr.success(response.message);
                    setTimeout(function () {
                        location.reload();
                    }, parseFloat(toastTimeOut) * 1000);
                } else {
                    icon.attr("data-pinned", isPinned);
                    if (isPinned == 0) {
                        toastr.success(label_unpinned_successfully);
                    } else {
                        toastr.success(label_pinned_successfully);
                    }
                    // Check if 'data-table' attribute is provided, otherwise default to 'projects_table'
                    var tableId = icon.data("table") || entityType + "_table";
                    if ($("#" + tableId).length) {
                        // Check if the table exists
                        $("#" + tableId).bootstrapTable("refresh"); // Refresh the table
                    }
                }
            } else {
                toastr.error(response.message);
            }
        },
        error: function () {
            // Handle errors if necessary
            toastr.error(label_err_try_again);
        },
    });
});

$(document).on("click", ".duplicate", function (e) {
    e.preventDefault();
    var id = $(this).data("id");
    var type = $(this).data("type");
    var reload = $(this).data("reload"); // Get the value of data-reload attribute
    if (typeof reload !== "undefined" && reload === true) {
        reload = true;
    } else {
        reload = false;
    }
    var tableID = $(this).data("table") || "table";
    $("#duplicateModal").modal("show"); // show the confirmation modal
    $("#duplicateModal").off("click", "#confirmDuplicate");
    if (type != "estimates-invoices" && type != "payslips") {
        $("#duplicateModal").find("#titleDiv").removeClass("d-none");
        var title = $(this).data("title");
        $("#duplicateModal").find("#updateTitle").val(title);
    } else {
        $("#duplicateModal").find("#titleDiv").addClass("d-none");
    }
    // Show or hide selection div based on data-type being 'workspaces'
    if (type === "workspaces") {
        $("#duplicateModal").find("#selectionDiv").removeClass("d-none"); // Show the selection div
    } else {
        $("#duplicateModal").find("#selectionDiv").addClass("d-none"); // Hide the selection div
    }
    $("#duplicateModal").on("click", "#confirmDuplicate", function (e) {
        e.preventDefault();
        var title = $("#duplicateModal").find("#updateTitle").val();
        const selectedOptions = $(".duplicate-option:checked")
            .map(function () {
                return $(this).val();
            })
            .get();
        $("#confirmDuplicate").html(label_please_wait).attr("disabled", true);
        $.ajax({
            url:
                baseUrl +
                "/" +
                type +
                "/duplicate/" +
                id +
                "?reload=" +
                reload +
                "&title=" +
                title +
                "&options=" +
                selectedOptions,
            type: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#confirmDuplicate").html(label_yes).attr("disabled", false);
                $("#duplicateModal").modal("hide");
                if (response.error == false) {
                    if (reload) {
                        if (response.message) {
                            // Show success message
                            toastr.success(response.message);
                            setTimeout(function () {
                                location.reload();
                            }, parseFloat(toastTimeOut) * 1000);
                        } else {
                            location.reload();
                        }
                    } else {
                        toastr.success(response.message);
                        if (tableID) {
                            $("#" + tableID).bootstrapTable("refresh");
                        }
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (data) {
                $("#confirmDuplicate").html(label_yes).attr("disabled", false);
                $("#duplicateModal").modal("hide");
                toastr.error(label_something_went_wrong);
            },
        });
    });
});

$("#duplicateProjects").on("change", function () {
    const projectTasksCheckbox = $("#duplicateProjectTasks");
    if ($(this).is(":checked")) {
        projectTasksCheckbox.prop("disabled", false);
    } else {
        projectTasksCheckbox.prop("disabled", true).prop("checked", false);
    }
});

$("#deduction_type").on("change", function (e) {
    if ($("#deduction_type").val() == "amount") {
        $("#amount_div").removeClass("d-none");
        $("#percentage_div").addClass("d-none");
    } else if ($("#deduction_type").val() == "percentage") {
        $("#amount_div").addClass("d-none");
        $("#percentage_div").removeClass("d-none");
    } else {
        $("#amount_div").addClass("d-none");
        $("#percentage_div").addClass("d-none");
    }
});

$("#update_deduction_type").on("change", function (e) {
    if ($("#update_deduction_type").val() == "amount") {
        $("#update_amount_div").removeClass("d-none");
        $("#update_percentage_div").addClass("d-none");
    } else if ($("#update_deduction_type").val() == "percentage") {
        $("#update_amount_div").addClass("d-none");
        $("#update_percentage_div").removeClass("d-none");
    } else {
        $("#update_amount_div").addClass("d-none");
        $("#update_percentage_div").addClass("d-none");
    }
});

$("#tax_type").on("change", function (e) {
    if ($("#tax_type").val() == "amount") {
        $("#amount_div").removeClass("d-none");
        $("#percentage_div").addClass("d-none");
    } else if ($("#tax_type").val() == "percentage") {
        $("#amount_div").addClass("d-none");
        $("#percentage_div").removeClass("d-none");
    } else {
        $("#amount_div").addClass("d-none");
        $("#percentage_div").addClass("d-none");
    }
});

$("#update_tax_type").on("change", function (e) {
    if ($("#update_tax_type").val() == "amount") {
        $("#update_amount_div").removeClass("d-none");
        $("#update_percentage_div").addClass("d-none");
    } else if ($("#update_tax_type").val() == "percentage") {
        $("#update_amount_div").addClass("d-none");
        $("#update_percentage_div").removeClass("d-none");
    } else {
        $("#update_amount_div").addClass("d-none");
        $("#update_percentage_div").addClass("d-none");
    }
});

if (document.getElementById("system-update-dropzone")) {
    if (!$("#system-update").hasClass("dropzone")) {
        var systemDropzone = new Dropzone("#system-update-dropzone", {
            url: $("#system-update").attr("action"),
            paramName: "update_file",
            autoProcessQueue: false,
            parallelUploads: 1,
            maxFiles: 1,
            acceptedFiles: ".zip",
            timeout: 360000,
            autoDiscover: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Pass the CSRF token as a header
            },
            addRemoveLinks: true,
            dictRemoveFile: "x",
            dictMaxFilesExceeded: label_only_one_file_can_be_uploaded_at_a_time,
            dictResponseError: "Error",
            uploadMultiple: true,
            dictDefaultMessage:
                '<p><input type="button" value="' +
                label_select +
                '" class="btn btn-primary" /><br> ' +
                label_or +
                " <br> " +
                label_drag_and_drop_update_zip_file_here +
                "</p>",
        });

        systemDropzone.on("addedfile", function (file) {
            var i = 0;
            if (this.files.length) {
                var _i, _len;
                for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) {
                    if (
                        this.files[_i].name === file.name &&
                        this.files[_i].size === file.size &&
                        this.files[_i].lastModifiedDate.toString() ===
                            file.lastModifiedDate.toString()
                    ) {
                        this.removeFile(file);
                        i++;
                    }
                }
            }
        });

        systemDropzone.on("error", function (file, response) {
            // Remove the file
            systemDropzone.removeFile(file);
            // Re-enable the submit button and reset its text
            $("#system_update_btn")
                .attr("disabled", false)
                .text(label_update_the_system);
            var errorMessage = label_err_try_again;
            if (typeof response === "string") {
                errorMessage = response; // Use the response text if it's a string
            } else if (response.message) {
                errorMessage = response.message; // Use response.message if it exists
            }
            toastr.error(errorMessage);
        });

        systemDropzone.on("success", function (file, response) {
            $("#system_update_btn")
                .attr("disabled", false)
                .text(label_update_the_system);
            if (response.error) {
                // Remove the file
                systemDropzone.removeFile(file);
                // Re-enable the submit button and reset its text
                // Show the error message
                toastr.error(response.message);
            } else {
                // Show success message
                toastr.success(response.message);
                setTimeout(function () {
                    location.reload();
                }, parseFloat(toastTimeOut) * 1000);
            }
        });

        $("#system_update_btn").on("click", function (e) {
            e.preventDefault();
            var queuedFiles = systemDropzone.getQueuedFiles();
            if (queuedFiles.length > 0) {
                $("#system_update_btn")
                    .attr("disabled", true)
                    .text(label_please_wait);
                systemDropzone.processQueue();
            } else {
                toastr.error(label_no_files_chosen);
            }
        });
    }
}

if (document.getElementById("media-upload-dropzone")) {
    var is_error = false;
    var mediaDropzone = new Dropzone("#media-upload-dropzone", {
        url: $("#media-upload").attr("action"),
        paramName: "media_files",
        autoProcessQueue: false,
        timeout: 0,
        autoDiscover: false,
        maxFilesize: allowedMaxFilesize,
        maxFiles: maxFilesAllowed,
        parallelUploads: maxFilesAllowed,
        acceptedFiles: allowedFileTypes,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Pass the CSRF token as a header
        },
        addRemoveLinks: true,
        dictRemoveFile: "x",
        dictResponseError: "Error",
        uploadMultiple: true,
        dictDefaultMessage:
            '<p><input type="button" value="' +
            label_select +
            '" class="btn btn-primary" /><br> ' +
            label_or +
            " <br> " +
            label_drag_and_drop_files_here +
            " <br> " +
            label_allowed_max_upload_size +
            ": " +
            allowedMaxFilesizeFormatted +
            "<br> " +
            label_max_files_allowed +
            ": " +
            maxFilesAllowed +
            "</p>",
    });
    allowedFileTypes = allowedFileTypes.split(",");
    mediaDropzone.on("addedfile", function (file) {
        var removedFiles = 0;

        // Check if the number of files exceeds the maxFiles limit
        if (this.files.length > maxFilesAllowed) {
            this.removeFile(file); // Remove the extra file
            toastr.error(
                label_max_files_count_allowed.replace(":count", maxFilesAllowed)
            );
            return; // Exit to prevent further processing
        }
        // Check if file type is allowed
        var fileExtension = getFileExtension(file.name);
        if (!allowedFileTypes.includes(fileExtension)) {
            mediaDropzone.removeFile(file);
            removedFiles++;
        }

        // Show a message if a file was removed
        if (removedFiles > 0) {
            toastr.error(label_file_type_not_allowed + ": " + file.name);
        }
    });

    mediaDropzone.on("error", function (file, response) {
        // console.log(response);
    });

    mediaDropzone.on("sending", function (file, xhr, formData) {
        var id = $("#media_type_id").val();
        formData.append("id", id);
    });

    mediaDropzone.on("queuecomplete", function () {
        $("#upload_media_btn").attr("disabled", false).text(label_upload);
        if (mediaDropzone.files.length > 0) {
            var lastFileResponse =
                mediaDropzone.files[mediaDropzone.files.length - 1].xhr
                    .responseText;
            var response = JSON.parse(lastFileResponse);

            if (!response.error) {
                if ($("#add_media_modal").length) {
                    $("#add_media_modal").modal("hide");
                }

                if ($("#project_media_table").length) {
                    $("#project_media_table").bootstrapTable("refresh");
                }

                if ($("#task_media_table").length) {
                    $("#task_media_table").bootstrapTable("refresh");
                }
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        }
        mediaDropzone.removeAllFiles();
    });
    $("#upload_media_btn").on("click", function (e) {
        e.preventDefault();
        if (mediaDropzone.getQueuedFiles().length > 0) {
            if (is_error == false) {
                $("#upload_media_btn")
                    .attr("disabled", true)
                    .text(label_please_wait);
                mediaDropzone.processQueue();
            }
        } else {
            toastr.error(label_no_files_chosen);
        }
    });

    // Clear Dropzone files when the modal is closed
    $("#add_media_modal").on("hide.bs.modal", function () {
        mediaDropzone.removeAllFiles();
        $("#upload_media_btn").attr("disabled", false).text(label_upload);
    });
}

if (document.getElementById("contract-dropzone")) {
    var contractDropzone = new Dropzone("#contract-dropzone", {
        url: $("#edit_contract_modal")
            .find(".form-submit-event")
            .attr("action"),
        paramName: "signed_pdf",
        autoProcessQueue: false,
        parallelUploads: 1,
        maxFilesize: 10, // Maximum file size in MB
        maxFiles: 1, // Only allow one file to be uploaded
        acceptedFiles: ".pdf", // Only accept PDF files
        timeout: 360000,
        autoDiscover: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Pass CSRF token as header
        },
        addRemoveLinks: true,
        dictRemoveFile: "x",
        dictMaxFilesExceeded: label_only_one_file_can_be_uploaded_at_a_time,
        dictResponseError: "Error",
        uploadMultiple: false, // Allow only one file upload at a time
        dictDefaultMessage:
            '<p><input type="button" value="' +
            label_select +
            '" class="btn btn-primary" /><br> ' +
            label_or +
            " <br> " +
            label_drag_and_drop_file_here +
            "</p>",
    });
}

if (document.getElementById("bulk-upload-dropzone")) {
    var bulkUploadDropzone = new Dropzone("#bulk-upload-dropzone", {
        url: $("#bulk-upload-dropzone").closest("form").attr("action"), // Uses the form's action URL
        paramName: "bulk_file", // The name of the file input field
        autoProcessQueue: false, // Don't auto-submit
        parallelUploads: 1, // Only upload one file at a time
        maxFiles: 1, // Allow only one file at a time
        acceptedFiles: ".csv,.xlsx,.xls", // Only accept CSV or Excel files
        timeout: 360000,
        autoDiscover: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Pass the CSRF token as a header
        },
        addRemoveLinks: true,
        dictRemoveFile: "x",
        dictMaxFilesExceeded: label_only_one_file_can_be_uploaded_at_a_time,
        dictResponseError: "Error",
        uploadMultiple: false, // Don't allow multiple files
        dictDefaultMessage:
            '<p><input type="button" value="' +
            label_select +
            '" class="btn btn-primary" /><br>' +
            label_or +
            "<br>" +
            label_drag_and_drop_file_here +
            "</p>",
    });

    // On added file
    bulkUploadDropzone.on("addedfile", function (file) {
        var i = 0;
        if (this.files.length) {
            var _i, _len;
            for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) {
                if (
                    this.files[_i].name === file.name &&
                    this.files[_i].size === file.size &&
                    this.files[_i].lastModifiedDate.toString() ===
                        file.lastModifiedDate.toString()
                ) {
                    this.removeFile(file);
                    i++;
                }
            }
        }
    });

    // On error
    bulkUploadDropzone.on("error", function (file, response) {
        bulkUploadDropzone.removeFile(file);
        $("#submit_btn").attr("disabled", false).text(label_upload); // Re-enable the button
        $("#validation-errors").html("");
        if (
            response.validation_errors &&
            Object.keys(response.validation_errors).length > 0
        ) {
            toastr.error(label_please_correct_errors); // Show error message
            // Loop through the validation errors
            Object.values(response.validation_errors).forEach(function (error) {
                if (error.trim() !== "") {
                    // Ignore empty strings
                    $("#validation-errors").append("<p>" + error + "</p>");
                }
            });
        } else {
            if (response.message) {
                toastr.error(response.message); // Show error message
            } else {
                toastr.error(label_something_went_wrong); // Show error message
            }
        }
    });

    // On success
    bulkUploadDropzone.on("success", function (file, response) {
        $("#submit_btn").attr("disabled", false).text(label_upload); // Re-enable the button
        $("#validation-errors").html("");
        bulkUploadDropzone.removeFile(file); // Remove the file
        if (response.error) {
            toastr.error(response.message); // Show error message
        } else {
            toastr.success(response.message); // Show success message
        }
    });

    // On submit button click
    $("#submit_btn").on("click", function (e) {
        e.preventDefault();
        var queuedFiles = bulkUploadDropzone.getQueuedFiles();
        if (queuedFiles.length > 0) {
            $("#submit_btn").attr("disabled", true).text(label_please_wait); // Disable the button
            bulkUploadDropzone.processQueue(); // Start uploading
        } else {
            toastr.error(label_no_file_chosen); // Error message if no file is added
        }
    });
}

function getFileExtension(filename) {
    return "." + filename.split(".").pop().toLowerCase();
}

$(document).on("click", ".admin-login", function (e) {
    e.preventDefault();
    $("#email").val("admin@gmail.com");
    $("#password").val("123456");
});
$(document).on("click", ".member-login", function (e) {
    e.preventDefault();
    $("#email").val("member@gmail.com");
    $("#password").val("123456");
});
$(document).on("click", ".client-login", function (e) {
    e.preventDefault();
    $("#email").val("client@gmail.com");
    $("#password").val("123456");
});

// Row-wise Select/Deselect All
$(".row-permission-checkbox").change(function () {
    var module = $(this).data("module");
    var isChecked = $(this).prop("checked");
    $(`.permission-checkbox[data-module="${module}"]`).prop(
        "checked",
        isChecked
    );
});

$("#selectAllColumnPermissions").change(function () {
    var isChecked = $(this).prop("checked");
    $(".permission-checkbox").prop("checked", isChecked);
    if (isChecked) {
        $(".row-permission-checkbox").prop("checked", true).trigger("change"); // Check all row permissions when select all is checked
    } else {
        $(".row-permission-checkbox").prop("checked", false).trigger("change"); // Uncheck all row permissions when select all is unchecked
    }
    checkAllPermissions(); // Check all permissions
});

// Select/Deselect All for Rows
$("#selectAllPermissions").change(function () {
    var isChecked = $(this).prop("checked");
    $(".row-permission-checkbox").prop("checked", isChecked).trigger("change");
});

// Function to check/uncheck all permissions for a module
function checkModulePermissions(module) {
    var allChecked = true;
    $('.permission-checkbox[data-module="' + module + '"]').each(function () {
        if (!$(this).prop("checked")) {
            allChecked = false;
        }
    });
    $("#selectRow" + module).prop("checked", allChecked);
}

// Function to check if all permissions are checked and select/deselect "Select all" checkbox
function checkAllPermissions() {
    var allPermissionsChecked = true;
    $(".permission-checkbox").each(function () {
        if (!$(this).prop("checked")) {
            allPermissionsChecked = false;
        }
    });
    $("#selectAllColumnPermissions").prop("checked", allPermissionsChecked);
}

// Event handler for individual permission checkboxes
$(".permission-checkbox").on("change", function () {
    var module = $(this).data("module");
    checkModulePermissions(module);
    checkAllPermissions();
});

// Event handler for "Select all" checkbox
$("#selectAllColumnPermissions").on("change", function () {
    var isChecked = $(this).prop("checked");
    $(".permission-checkbox").prop("checked", isChecked);
});

// Initial check for permissions on page load
$(".row-permission-checkbox").each(function () {
    var module = $(this).data("module");
    checkModulePermissions(module);
});
checkAllPermissions();

$(document).ready(function () {
    $(".fixed-table-toolbar").each(function () {
        var $toolbar = $(this);
        var $data_type = $toolbar
            .closest(".table-responsive")
            .find("#data_type");
        var $data_table = $toolbar
            .closest(".table-responsive")
            .find("#data_table");
        var $multi_select = $toolbar
            .closest(".table-responsive")
            .find("#multi_select");
        var $save_column_visibility = $toolbar
            .closest(".table-responsive")
            .find("#save_column_visibility");

        if ($data_type.length > 0) {
            var data_type = $data_type.val();
            var data_table = $data_table.val() || "table";
            var multi_select = $multi_select.length > 0 ? 1 : 0;
            var multi_select_value = $multi_select.val() || null;
            var data_reload =
                $toolbar
                    .closest(".table-responsive")
                    .find("#data_reload")
                    .val() || 0;
            var action_class =
                "action_delete_" +
                (["project-media", "task-media"].includes(data_type)
                    ? "media"
                    : data_type.replace("-", "_"));
            var showDelete =
                data_type !== "report" &&
                data_table !== "birthdays_table" &&
                data_table !== "wa_table"
                    ? 1
                    : 0;
            // Create the "Delete selected" button
            if (showDelete) {
                var $deleteButton = $(
                    '<div class="columns columns-left btn-group float-left ' +
                        action_class +
                        '">' +
                        '<button type="button" class="btn btn-outline-danger float-left delete-selected" data-type="' +
                        data_type +
                        '" data-table="' +
                        data_table +
                        '" data-reload="' +
                        data_reload +
                        '">' +
                        '<i class="bx bx-trash"></i> ' +
                        label_delete_selected +
                        "</button>" +
                        "</div>"
                );

                // Add the "Delete selected" button before the first element in the toolbar
                $toolbar.prepend($deleteButton);
            }

            if (multi_select) {
                // Use multi_select value for clear button class if available, else use data_type
                var clearButtonClass = multi_select_value
                    ? "clear-" + multi_select_value + "-filters"
                    : "clear-" + data_type + "-filters";

                // Create the "Clear Filters" button
                var $clearFiltersButton = $(
                    '<div class="columns columns-left btn-group float-left">' +
                        '<button type="button" class="btn btn-outline-secondary ' +
                        clearButtonClass +
                        '">' +
                        '<i class="bx bx-x-circle"></i> ' +
                        label_clear_filters +
                        "</button>" +
                        "</div>"
                );
            }
            if (showDelete) {
                $deleteButton.after($clearFiltersButton);
            } else {
                $toolbar.prepend($clearFiltersButton);
            }

            if ($save_column_visibility.length > 0) {
                // Extract data-type and data-table from $save_column_visibility if they exist
                var saveType =
                    $save_column_visibility.data("type") || data_type;
                var saveTable =
                    $save_column_visibility.data("table") || data_table;

                var $savePreferencesButton = $(
                    '<div class="columns columns-left btn-group float-left">' +
                        '<button type="button" class="btn btn-outline-primary save-column-visibility" data-type="' +
                        saveType +
                        '" data-table="' +
                        saveTable +
                        '">' +
                        '<i class="bx bx-save"></i> ' +
                        label_save_column_visibility +
                        "</button>" +
                        "</div>"
                );

                // Add the Save Preferences button to the toolbar in the appropriate location
                if ($deleteButton) {
                    $deleteButton.after($savePreferencesButton);
                } else if ($clearFiltersButton) {
                    $clearFiltersButton.after($savePreferencesButton);
                } else {
                    $toolbar.prepend($savePreferencesButton);
                }
            }
        }
    });
});

$("#media_storage_type").on("change", function (e) {
    if ($("#media_storage_type").val() == "s3") {
        $(".aws-s3-fields").removeClass("d-none");
    } else {
        $(".aws-s3-fields").addClass("d-none");
    }
});

$(document).on("click", ".edit-milestone", function () {
    var id = $(this).data("id");
    $.ajax({
        url: baseUrl + "/projects/get-milestone/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            var formattedStartDate = response.ms.start_date
                ? moment(response.ms.start_date).format(js_date_format)
                : "";
            var formattedEndDate = response.ms.end_date
                ? moment(response.ms.end_date).format(js_date_format)
                : "";

            $("#milestone_id").val(response.ms.id);
            $("#milestone_title").val(response.ms.title);
            if (formattedStartDate) {
                $("#update_milestone_start_date").val(formattedStartDate);
            }
            if (formattedEndDate) {
                $("#update_milestone_end_date").val(formattedEndDate);
            }
            $("#milestone_status").val(response.ms.status);
            $("#milestone_cost").val(response.ms.cost);
            var description =
                response.ms.description !== null ? response.ms.description : "";
            $("#edit_milestone_modal")
                .find("#milestone_description")
                .val(description);
            $("#milestone_progress").val(response.ms.progress);
            $(".milestone-progress").text(response.ms.progress + "%");
        },
    });
});

$(document).on("click", "#mark-all-notifications-as-read", function (e) {
    e.preventDefault();
    $("#mark_all_notifications_as_read_modal").modal("show"); // show the confirmation modal
    $("#mark_all_notifications_as_read_modal").on(
        "click",
        "#confirmMarkAllAsRead",
        function () {
            $("#confirmMarkAllAsRead")
                .html(label_please_wait)
                .attr("disabled", true);
            $.ajax({
                url: baseUrl + "/notifications/mark-all-as-read",
                type: "PUT",
                headers: {
                    "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
                },
                success: function (response) {
                    location.reload();
                    // $('#confirmMarkAllAsRead').html(label_yes).attr('disabled', false);
                },
            });
        }
    );
});

$(document).on("click", ".update-notification-status", function (e) {
    var notificationId = $(this).data("id");
    var needConfirm = $(this).data("needconfirm") || false;
    if (needConfirm) {
        // Show the confirmation modal
        $("#update_notification_status_modal").modal("show");

        // Attach click event handler to the confirmation button
        $("#update_notification_status_modal").off(
            "click",
            "#confirmNotificationStatus"
        );
        $("#update_notification_status_modal").on(
            "click",
            "#confirmNotificationStatus",
            function () {
                $("#confirmNotificationStatus")
                    .html(label_please_wait)
                    .attr("disabled", true);
                performUpdate(notificationId, needConfirm);
            }
        );
    } else {
        // If confirmation is not needed, directly perform the update and handle response
        performUpdate(notificationId);
    }
});

function performUpdate(notificationId, needConfirm = "") {
    $.ajax({
        url: baseUrl + "/notifications/update-status",
        type: "PUT",
        data: { id: notificationId, needConfirm: needConfirm },
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        success: function (response) {
            if (needConfirm) {
                $("#confirmNotificationStatus")
                    .html(label_yes)
                    .attr("disabled", false);
                if (response.error == false) {
                    toastr.success(response.message);
                    $("#table").bootstrapTable("refresh");
                    // Redirect after successful update
                } else {
                    toastr.error(response.message);
                }
                $("#update_notification_status_modal").modal("hide");
            } else {
                var redirectUrl = determineRedirectUrl(
                    response.notification.type,
                    response.notification.type_id,
                    response.notification.action
                );
                window.location.href = redirectUrl;
            }
        },
    });
}

function determineRedirectUrl(type, typeId, action) {
    var redirectUrl = "";
    switch (type) {
        case "project":
            redirectUrl = baseUrl + "/projects/information/" + typeId;
            break;
        case "task":
            redirectUrl = baseUrl + "/tasks/information/" + typeId;
            break;
        case "project_comment_mention":
            redirectUrl =
                baseUrl +
                "/projects/information/" +
                typeId +
                "#navs-top-discussions";
            break;
        case "task_comment_mention":
            redirectUrl =
                baseUrl +
                "/tasks/information/" +
                typeId +
                "#navs-top-discussions";
            break;
        case "workspace":
            redirectUrl = baseUrl + "/workspaces";
            break;
        case "leave_request":
            if (action === "team_member_on_leave_alert") {
                redirectUrl = baseUrl + "/notifications";
            } else {
                redirectUrl = baseUrl + "/leave-requests";
            }
            break;
        case "meeting":
            redirectUrl = baseUrl + "/meetings";
            break;
        default:
            redirectUrl = baseUrl + "/";
    }
    return redirectUrl;
}
if (
    typeof manage_notifications !== "undefined" &&
    manage_notifications == "true"
) {
    function updateUnreadNotifications() {
        // Make an AJAX request to fetch the count and HTML of unread notifications
        $.ajax({
            url: baseUrl + "/notifications/get-unread-notifications",
            type: "GET",
            dataType: "json",
            success: function (data) {
                const unreadNotificationsCount = data.count;
                const unreadNotificationsHtml = data.html;

                $("#unreadNotificationsCount").text(unreadNotificationsCount);
                $("#unreadNotificationsCount").toggleClass(
                    "d-none",
                    unreadNotificationsCount === 0
                );

                // Update the notifications list with the new HTML
                $("#unreadNotificationsContainer").html(
                    unreadNotificationsHtml
                );
            },
            error: function (xhr, status, error) {
                console.error("Error fetching unread notifications:", error);
            },
        });
    }

    // Call the updateUnreadNotifications function initially
    // updateUnreadNotifications();

    // Update the unread notifications every 30 seconds
    // setInterval(updateUnreadNotifications, 30000);
}

$(
    "textarea#email_verify_email,textarea#email_account_creation,textarea#email_forgot_password,textarea#email_project_assignment,textarea#email_task_assignment,textarea#email_workspace_assignment,textarea#email_meeting_assignment,textarea#email_leave_request_creation,textarea#email_leave_request_status_updation,textarea#email_project_status_updation,textarea#email_task_status_updation,textarea#email_team_member_on_leave_alert,textarea#email_birthday_wish,#email_work_anniversary_wish,textarea#email_task_reminder,textarea#email_recurring_task"
).tinymce({
    height: 821,
    menubar: true,
    plugins: [
        "link",
        "a11ychecker",
        "advlist",
        "advcode",
        "advtable",
        "autolink",
        "checklist",
        "export",
        "lists",
        "link",
        "image",
        "charmap",
        "preview",
        "anchor",
        "searchreplace",
        "visualblocks",
        "powerpaste",
        "fullscreen",
        "formatpainter",
        "insertdatetime",
        "media",
        "table",
        "help",
        "wordcount",
        "emoticons",
        "code",
    ],
    toolbar: false,
    // toolbar: 'link | undo redo | a11ycheck casechange blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist checklist outdent indent | removeformat | code blockquote emoticons table help'
});

// Handle click event on toolbar items
$(".tox-tbtn").click(function () {
    // Get the current editor instance
    var editor = tinyMCE.activeEditor;

    // Close any open toolbar dropdowns
    tinymce.ui.Factory.each(function (ctrl) {
        if (ctrl.type === "toolbarbutton" && ctrl.settings.toolbar) {
            if (ctrl !== this && ctrl.settings.toolbar === "toolbox") {
                ctrl.panel.hide();
            }
        }
    }, editor);

    // Execute the action associated with the clicked toolbar item
    editor.execCommand("mceInsertContent", false, "Clicked!");
});

$(document).on("click", ".restore-default", function (e) {
    e.preventDefault();
    var form = $(this).closest("form");

    var type = form.find('input[name="type"]').val();
    var name = form.find('input[name="name"]').val();
    var textarea = type + "_" + name;

    $("#restore_default_modal").modal("show"); // show the confirmation modal
    $("#restore_default_modal").off("click", "#confirmRestoreDefault");
    $("#restore_default_modal").on(
        "click",
        "#confirmRestoreDefault",
        function () {
            $("#confirmRestoreDefault")
                .html(label_please_wait)
                .attr("disabled", true);
            $.ajax({
                url: baseUrl + "/settings/get-default-template",
                type: "POST",
                data: { type: type, name: name },
                headers: {
                    "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
                },
                dataType: "json",
                success: function (response) {
                    $("#confirmRestoreDefault")
                        .html(label_yes)
                        .attr("disabled", false);
                    $("#restore_default_modal").modal("hide");
                    if (response.error == false) {
                        tinymce.get(textarea).setContent(response.content);
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
            });
        }
    );
});

$(document).on("click", ".sms-restore-default", function (e) {
    e.preventDefault();
    var form = $(this).closest("form");

    var type = form.find('input[name="type"]').val();
    var name = form.find('input[name="name"]').val();
    var textarea = type + "_" + name;

    $("#restore_default_modal").modal("show"); // show the confirmation modal
    $("#restore_default_modal").off("click", "#confirmRestoreDefault");
    $("#restore_default_modal").on(
        "click",
        "#confirmRestoreDefault",
        function () {
            $("#confirmRestoreDefault")
                .html(label_please_wait)
                .attr("disabled", true);
            $.ajax({
                url: baseUrl + "/settings/get-default-template",
                type: "POST",
                data: { type: type, name: name },
                headers: {
                    "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
                },
                dataType: "json",
                success: function (response) {
                    $("#confirmRestoreDefault")
                        .html(label_yes)
                        .attr("disabled", false);
                    $("#restore_default_modal").modal("hide");
                    if (response.error == false) {
                        $("#" + textarea).val(response.content);
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
            });
        }
    );
});

$(document).ready(function () {
    // Shared function to calculate total days
    function calculateTotalDays(
        startDateSelector,
        endDateSelector,
        totalDaysSelector
    ) {
        var start_date = moment($(startDateSelector).val(), js_date_format);
        var end_date = moment($(endDateSelector).val(), js_date_format);

        if (start_date.isValid() && end_date.isValid()) {
            var total_days = end_date.diff(start_date, "days") + 1;
            $(totalDaysSelector).val(total_days);
        }
    }

    // Function to bind event listeners for date inputs
    function bindDateChangeListeners(
        startDateSelector,
        endDateSelector,
        totalDaysSelector
    ) {
        $(startDateSelector + ", " + endDateSelector)
            .off("change")
            .on("change", function () {
                calculateTotalDays(
                    startDateSelector,
                    endDateSelector,
                    totalDaysSelector
                );
            });

        $(startDateSelector).on("apply.daterangepicker", function () {
            calculateTotalDays(
                startDateSelector,
                endDateSelector,
                totalDaysSelector
            );
        });

        $(endDateSelector).on("apply.daterangepicker", function () {
            calculateTotalDays(
                startDateSelector,
                endDateSelector,
                totalDaysSelector
            );
        });
    }

    // Initial binding for create modal
    if ($("#total_days").length) {
        bindDateChangeListeners("#start_date", "#lr_end_date", "#total_days");
    }

    // Initial binding for update modal
    if ($("#update_total_days").length) {
        bindDateChangeListeners(
            "#update_start_date",
            "#update_end_date",
            "#update_total_days"
        );
    }

    // Function to reset form and rebind event listeners when modal is hidden
    function resetModalForm(modal) {
        var modalId = $(modal).attr("id");
        var $form = $(modal).find("form"); // Find the form inside the modal
        $form.trigger("reset"); // Reset the form
        if ($(modal).find("#total_days").length) {
            bindDateChangeListeners(
                "#start_date",
                "#lr_end_date",
                "#total_days"
            );
        }

        if ($(modal).find("#update_total_days").length) {
            bindDateChangeListeners(
                "#update_start_date",
                "#update_end_date",
                "#update_total_days"
            );
        }
        $form.find(".error-message").html("");
        var partialLeaveCheckbox = $("#partialLeave");
        if (partialLeaveCheckbox.length) {
            partialLeaveCheckbox.trigger("change");
        }
        var leaveVisibleToAllCheckbox = $form.find(".leaveVisibleToAll");
        if (leaveVisibleToAllCheckbox.length) {
            leaveVisibleToAllCheckbox.trigger("change");
        }
        var defaultColor =
            modalId == "create_note_modal" || modalId == "edit_note_modal"
                ? "success"
                : "primary";
        var colorSelect = $form.find('select[name="color"]');
        if (colorSelect.length) {
            var classes = colorSelect.attr("class").split(" ");
            var currentColorClass = classes.filter(function (className) {
                return className.startsWith("select-");
            })[0];
            colorSelect
                .removeClass(currentColorClass)
                .addClass("select-bg-label-" + defaultColor);
        }

        var selectPriority = $form.find('select[name="priority_id"]');
        if (selectPriority.length) {
            var classes = selectPriority.attr("class").split(" ");
            var currentClass = classes.filter(function (className) {
                return className.startsWith("bg-label");
            })[0];
            selectPriority
                .removeClass(currentClass)
                .addClass("bg-label-secondary");
        }

        $form
            .find(
                ".js-example-basic-multiple, .users_select, .clients_select, .projects_select, .contract_types_select, .invoices_select"
            )
            .val(null) // Set value to null
            .trigger("change"); // Trigger change event to update Select2

        $("#create_task_modal, #edit_task_modal")
            .find('select[name="user_id[]"]')
            .val(null) // Set value to null
            .trigger("change"); // Trigger change event to update Select2

        if ($('.selectTaskProject[name="project"]').length) {
            $form
                .find($('.selectTaskProject[name="project"]'))
                .trigger("change");
        }
        if ($('.statusDropdown[name="status_id"]').length) {
            $form
                .find($('.statusDropdown[name="status_id"]'))
                .trigger("change");
        }
        if ($('.priorityDropdown[name="priority_id"]').length) {
            $form
                .find($('.priorityDropdown[name="priority_id"]'))
                .trigger("change");
        }
        if ($("#users_associated_with_project").length) {
            $("#users_associated_with_project").text("");
        }
        if ($("#task_update_users_associated_with_project").length) {
            $("#task_update_users_associated_with_project").text("");
        }
        $(modal)
            .find('input[type="checkbox"]')
            .each(function () {
                $(this).prop("checked", false).trigger("change"); // Reset checkbox and trigger change event
            });
        if (Dropzone.instances.length > 0) {
            Dropzone.instances.forEach(function (dz) {
                dz.removeAllFiles(true); // Clear all files
            });
        }
        resetDateFields($form); // Pass the form as an argument to resetDateFields()
    }

    // Reset form and rebind event listeners when modal is hidden
    $(".modal").on("hidden.bs.modal", function () {
        resetModalForm(this);
    });
});

$(document).ready(function () {
    // Listen for changes on the project select element within the modal
    $('.selectTaskProject[name="project"]').on("change", function (e) {
        var projectId = $(this).val();
        var currentModal = $(this).closest(".modal"); // Adjust the selector to match your modal structure
        var usersSelect = currentModal.find('select[name="user_id[]"]');
        var modalId = currentModal.attr("id");
        if (projectId) {
            $.ajax({
                url: baseUrl + "/projects/get/" + projectId,
                type: "GET",
                success: function (response) {
                    currentModal
                        .find("#users_associated_with_project")
                        .html(
                            "(" +
                                label_users_associated_with_project +
                                " <strong>" +
                                response.project.title +
                                "</strong>)"
                        );

                    usersSelect.empty(); // Clear existing options
                    // Check if task_accessibility is 'project_users'
                    if (response.users && response.users.length > 0) {
                        // Iterate through users and append options
                        response.users.forEach(function (user) {
                            var userOption = new Option(
                                user.first_name + " " + user.last_name,
                                user.id,
                                false,
                                false
                            ); // Unselected initially
                            usersSelect.append(userOption);
                        });

                        // Set task users or default to authUserId based on task accessibility
                        if (
                            response.project.task_accessibility ==
                            "project_users"
                        ) {
                            var taskUsers = response.users.map(
                                (user) => user.id
                            );
                            usersSelect.val(taskUsers);
                        } else {
                            if (
                                guard != "client" &&
                                modalId == "create_task_modal"
                            ) {
                                usersSelect.val(authUserId);
                            }
                        }

                        usersSelect.trigger("change");
                    } else {
                        // Handle case when no users are returned
                        if (
                            guard != "client" &&
                            modalId == "create_task_modal"
                        ) {
                            usersSelect.val(authUserId);
                        }
                        usersSelect.trigger("change");
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                },
            });
        }
    });
});

$(document).on("click", ".edit-task", function () {
    var id = $(this).data("id");
    $("#edit_task_modal").modal("show");
    $.ajax({
        url: baseUrl + "/tasks/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            var formattedStartDate = response.task.start_date
                ? moment(response.task.start_date).format(js_date_format)
                : "";
            var formattedEndDate = response.task.due_date
                ? moment(response.task.due_date).format(js_date_format)
                : "";

            $("#task_update_users_associated_with_project").html(
                "(" +
                    label_users_associated_with_project +
                    " <strong>" +
                    response.project.title +
                    "</strong>)"
            );
            $("#id").val(response.task.id);
            $("#title").val(response.task.title);
            $("#task_status_id").val(response.task.status_id).trigger("change");
            $("#priority_id").val(response.task.priority_id).trigger("change");

            if (formattedStartDate) {
                $("#update_start_date").val(formattedStartDate);
            }
            if (formattedEndDate) {
                $("#update_end_date").val(formattedEndDate);
            }
            initializeDateRangePicker("#update_start_date, #update_end_date");
            $("#update_project_title").val(response.project.title);
            var description =
                response.task.description !== null
                    ? response.task.description
                    : "";
            $("#edit_task_modal").find("#task_description").val(description);
            $("#taskNote").val(response.task.note);

            var usersSelect = $("#edit_task_modal").find(
                'select[name="user_id[]"]'
            );

            // Clear existing options
            usersSelect.empty();

            // Check if response.project.users exists and has users
            if (
                response.project &&
                response.project.users &&
                response.project.users.length > 0
            ) {
                // Add users from response.project.users to the select options
                response.project.users.forEach(function (user) {
                    var userOption = new Option(
                        user.first_name + " " + user.last_name,
                        user.id,
                        false,
                        false
                    ); // Unselected initially
                    usersSelect.append(userOption);
                });
            }

            // Now handle the selection of users based on response.task.users
            if (
                response.task &&
                response.task.users &&
                response.task.users.length > 0
            ) {
                var selectedTaskUsers = response.task.users.map(function (
                    user
                ) {
                    return user.id; // Get the user IDs from task
                });

                // Set the selected values
                usersSelect.val(selectedTaskUsers);
            } else {
                // Optionally handle the case when there are no task users
                usersSelect.val(null); // Clear the selection or set a default value
            }

            usersSelect.trigger("change"); // Trigger change to reflect selection

            if (response.task.client_can_discuss == 1) {
                $("#edit_task_modal")
                    .find("#updateClientCanDiscussTask")
                    .prop("checked", true);
            } else {
                $("#edit_task_modal")
                    .find("#updateClientCanDiscussTask")
                    .prop("checked", false);
            }

            if (response.task.recurring_task) {
                // Enable recurring task switch
                $("#edit-recurring-task-switch").prop("checked", true);
                $("#edit-recurring-task-settings").removeClass("d-none");

                // Set frequency
                $("#edit-recurrence-frequency")
                    .val(response.task.recurring_task.frequency)
                    .trigger("change");

                // Prefill frequency-specific fields based on frequency
                switch (response.task.recurring_task.frequency) {
                    case "weekly":
                        $("#edit-recurrence-day-of-week").val(
                            response.task.recurring_task.day_of_week || ""
                        );
                        break;
                    case "monthly":
                    case "yearly":
                        $("#edit-recurrence-day-of-month").val(
                            response.task.recurring_task.day_of_month || ""
                        );
                        break;
                    case "yearly":
                        $("#edit-recurrence-month-of-year").val(
                            response.task.recurring_task.month_of_year || ""
                        );
                        break;
                }

                // Prefill starts from and occurrences
                $("#edit-recurrence-starts-from").val(
                    response.task.recurring_task.starts_from
                        ? moment(
                              response.task.recurring_task.starts_from
                          ).format("YYYY-MM-DD")
                        : ""
                );
                $("#edit-recurrence-occurrences").val(
                    response.task.recurring_task.number_of_occurrences || ""
                );
            } else {
                // Disable recurring task if no recurring task exists
                $("#edit-recurring-task-switch").prop("checked", false);
                $("#edit-recurring-task-settings").addClass("d-none");
            }

            if (response.task?.reminders?.length > 0) {
                const reminder = response.task.reminders[0];

                $("#edit-reminder-switch").prop(
                    "checked",
                    reminder.is_active === 1
                );

                // Show reminder settings if active
                if (reminder.is_active === 1) {
                    $("#edit-reminder-settings").removeClass("d-none");
                } else {
                    $("#edit-reminder-settings").addClass("d-none");
                }

                // Set frequency type
                $("#edit-frequency-type")
                    .val(reminder.frequency_type)
                    .trigger("change");

                // Handle visibility and values for day selectors based on frequency type
                switch (reminder.frequency_type) {
                    case "weekly":
                        $("#edit-day-of-week-group").removeClass("d-none");
                        $("#edit-day-of-month-group").addClass("d-none");
                        $("#edit-day-of-week").val(reminder.day_of_week || "");
                        break;
                    case "monthly":
                        $("#edit-day-of-month-group").removeClass("d-none");
                        $("#edit-day-of-week-group").addClass("d-none");
                        $("#edit-day-of-month").val(
                            reminder.day_of_month || ""
                        );
                        break;
                    default: // daily
                        $("#edit-day-of-week-group").addClass("d-none");
                        $("#edit-day-of-month-group").addClass("d-none");
                }

                // Set time of day
                // Convert the time string to correct format for input type="time"
                const timeOfDay = reminder.time_of_day.slice(0, 5); // Get HH:mm from HH:mm:ss
                $("#edit-time-of-day").val(timeOfDay);
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
    });
});

$(document).on("click", ".edit-project", function () {
    var id = $(this).data("id");
    $("#edit_project_modal").modal("show");
    $.ajax({
        url: baseUrl + "/projects/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            var formattedStartDate = response.project.start_date
                ? moment(response.project.start_date).format(js_date_format)
                : "";
            var formattedEndDate = response.project.end_date
                ? moment(response.project.end_date).format(js_date_format)
                : "";

            var $modal = $("#edit_project_modal"); // Cache the modal element for better performance

            $modal.find("#project_id").val(response.project.id);
            $modal.find("#project_title").val(response.project.title);
            $modal
                .find("#project_status_id")
                .val(response.project.status_id)
                .trigger("change");
            $modal
                .find("#project_priority_id")
                .val(response.project.priority_id)
                .trigger("change");
            $modal.find("#project_budget").val(response.project.budget);
            $modal.find("#update_start_date").val(formattedStartDate);
            $modal.find("#update_end_date").val(formattedEndDate);

            initializeDateRangePicker(
                $modal.find("#update_start_date, #update_end_date")
            ); // Initialize date range picker

            $modal
                .find("#task_accessibility")
                .val(response.project.task_accessibility);
            $modal.find("#projectNote").val(response.project.note);
            var description =
                response.project.description !== null
                    ? response.project.description
                    : "";
            $modal.find("#project_description").val(description);

            var usersSelect = $modal.find(".users_select");
            var clientsSelect = $modal.find(".clients_select");

            // Clear existing options
            usersSelect.empty();
            clientsSelect.empty();

            // Handle multi-select for users
            if (response.users && response.users.length > 0) {
                response.users.forEach(function (user) {
                    var userOption = new Option(
                        user.first_name + " " + user.last_name,
                        user.id,
                        true,
                        true
                    );
                    usersSelect.append(userOption);
                });
                usersSelect.trigger("change");
            } else {
                usersSelect.val(null).trigger("change"); // Handle case when no users are present
            }

            // Handle multi-select for clients
            if (response.clients && response.clients.length > 0) {
                response.clients.forEach(function (client) {
                    var clientOption = new Option(
                        client.first_name + " " + client.last_name,
                        client.id,
                        true,
                        true
                    );
                    clientsSelect.append(clientOption);
                });
                clientsSelect.trigger("change");
            } else {
                clientsSelect.val(null).trigger("change"); // Handle case when no clients are present
            }

            var tagsSelect = $modal.find('[name="tag_ids[]"]');

            if (response.tags && response.tags.length > 0) {
                // Clear existing tags in the dropdown
                tagsSelect.empty();

                response.tags.forEach(function (tag) {
                    // Create a new option element
                    var tagOption = new Option(tag.title, tag.id, true, true);

                    // Add data-color attribute
                    // $(tagOption).attr('data-color', tag.color || 'default-color');

                    // Append the option to the select element
                    tagsSelect.append(tagOption);
                });

                tagsSelect.trigger("change"); // Update Select2
            } else {
                tagsSelect.val(null).trigger("change"); // Handle case when no tags are present
            }
            if (response.project.client_can_discuss == 1) {
                $("#edit_project_modal")
                    .find("#updateClientCanDiscussProject")
                    .prop("checked", true);
            } else {
                $("#edit_project_modal")
                    .find("#updateClientCanDiscussProject")
                    .prop("checked", false);
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
    });
});

$(document).on("click", ".edit-priority", function () {
    var id = $(this).data("id");
    $("#edit_priority_modal").modal("show");
    var classes = $("#priority_color").attr("class").split(" ");
    var currentColorClass = classes.filter(function (className) {
        return className.startsWith("select-");
    })[0];
    $.ajax({
        url: baseUrl + "/priority/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            $("#priority_id").val(response.priority.id);
            $("#priority_title").val(response.priority.title);
            $("#priority_color")
                .val(response.priority.color)
                .removeClass(currentColorClass)
                .addClass("select-bg-label-" + response.priority.color);
        },
    });
});

$(document).on("click", ".edit-workspace", function () {
    var id = $(this).data("id");
    $("#editWorkspaceModal").modal("show");
    var $modal = $("#editWorkspaceModal");
    $.ajax({
        url: baseUrl + "/workspaces/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            $("#workspace_id").val(response.workspace.id);
            $("#workspace_title").val(response.workspace.title);

            var usersSelect = $modal.find(".users_select");
            var clientsSelect = $modal.find(".clients_select");

            // Clear existing options
            usersSelect.empty();
            clientsSelect.empty();

            // Handle multi-select for users
            if (
                response.workspace.users &&
                response.workspace.users.length > 0
            ) {
                response.workspace.users.forEach(function (user) {
                    var userOption = new Option(
                        user.first_name + " " + user.last_name,
                        user.id,
                        true,
                        true
                    );
                    usersSelect.append(userOption);
                });
                usersSelect.trigger("change");
            } else {
                usersSelect.val(null).trigger("change"); // Handle case when no users are present
            }

            // Handle multi-select for clients
            if (
                response.workspace.clients &&
                response.workspace.clients.length > 0
            ) {
                response.workspace.clients.forEach(function (client) {
                    var clientOption = new Option(
                        client.first_name + " " + client.last_name,
                        client.id,
                        true,
                        true
                    );
                    clientsSelect.append(clientOption);
                });
                clientsSelect.trigger("change");
            } else {
                clientsSelect.val(null).trigger("change"); // Handle case when no clients are present
            }

            if (response.workspace.is_primary == 1) {
                $("#editWorkspaceModal")
                    .find("#updatePrimaryWorkspace")
                    .prop("checked", true)
                    .prop("disabled", true);
            } else {
                $("#editWorkspaceModal")
                    .find("#updatePrimaryWorkspace")
                    .prop("checked", false)
                    .prop("disabled", false);
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
    });
});

function setDefaultWorkspace(workspaceId, isDefault) {
    const isDefaultNumeric = isDefault ? 1 : 0;
    $.ajax({
        url: baseUrl + "/workspaces/" + workspaceId + "/default",
        type: "patch",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        data: {
            is_default: isDefaultNumeric,
        },
        success: function (response) {
            if (response.error == false) {
                toastr.success(response.message);
                $("#table").bootstrapTable("refresh");
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr, status, error) {
            toastr.error(
                "An error occurred while updating the default workspace."
            );
        },
    });
}

$(document).on("click", ".edit-meeting", function () {
    var id = $(this).data("id");
    $("#editMeetingModal").modal("show");
    $.ajax({
        url: baseUrl + "/meetings/get/" + id,
        type: "get",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        dataType: "json",
        success: function (response) {
            var formattedStartDate = moment(response.meeting.start_date).format(
                js_date_format
            );
            var formattedEndDate = moment(response.meeting.end_date).format(
                js_date_format
            );
            var startDateInput = $("#editMeetingModal").find(
                '[name="start_date"]'
            );
            var endDateInput = $("#editMeetingModal").find('[name="end_date"]');
            $("#meeting_id").val(response.meeting.id);
            $("#meeting_title").val(response.meeting.title);
            startDateInput.val(formattedStartDate);
            endDateInput.val(formattedEndDate);
            $("#meeting_start_time").val(response.meeting.start_time);
            $("#meeting_end_time").val(response.meeting.end_time);

            var usersSelect = $("#editMeetingModal").find(".users_select");
            var clientsSelect = $("#editMeetingModal").find(".clients_select");

            // Clear existing options
            usersSelect.empty();
            clientsSelect.empty();

            // Handle multi-select for users
            if (response.meeting.users && response.meeting.users.length > 0) {
                response.meeting.users.forEach(function (user) {
                    var userOption = new Option(
                        user.first_name + " " + user.last_name,
                        user.id,
                        true,
                        true
                    );
                    usersSelect.append(userOption);
                });
                usersSelect.trigger("change");
            } else {
                usersSelect.val(null).trigger("change"); // Handle case when no users are present
            }

            // Handle multi-select for clients
            if (
                response.meeting.clients &&
                response.meeting.clients.length > 0
            ) {
                response.meeting.clients.forEach(function (client) {
                    var clientOption = new Option(
                        client.first_name + " " + client.last_name,
                        client.id,
                        true,
                        true
                    );
                    clientsSelect.append(clientOption);
                });
                clientsSelect.trigger("change");
            } else {
                clientsSelect.val(null).trigger("change"); // Handle case when no clients are present
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
    });
});

$(document).on("change", "#statusSelect", function (e) {
    e.preventDefault();
    var id = $(this).data("id");
    var statusId = this.value;
    var type = $(this).data("type") || "project";
    var reload = $(this).data("reload") || false;
    var select = $(this);
    var originalStatusId = select.data("original-status-id");
    var originalColorClass = select.data("original-color-class");
    var classes = select.attr("class").split(" ");
    var currentColorClass = classes.filter(function (className) {
        return className.startsWith("select-");
    })[0];
    var selectedOption = select.find("option:selected");
    var selectedOptionClasses = selectedOption.attr("class").split(" ");
    var newColorClass = "select-" + selectedOptionClasses[1];
    select.removeClass(currentColorClass).addClass(newColorClass);

    $.ajax({
        url: baseUrl + "/" + type + "s/get/" + id,
        type: "GET",
        success: function (response) {
            if (response.error == false) {
                $("#confirmUpdateStatusModal").modal("show");
                $("#confirmUpdateStatusModal").off(
                    "click",
                    "#confirmUpdateStatus"
                );
                if (type == "task" && response.task) {
                    $("#statusNote").val(response.task.note);
                    originalStatusId = response.task.status_id;
                } else if (type == "project" && response.project) {
                    $("#statusNote").val(response.project.note);
                    originalStatusId = response.project.status_id;
                }

                $("#confirmUpdateStatusModal").on(
                    "click",
                    "#confirmUpdateStatus",
                    function (e) {
                        $("#confirmUpdateStatus")
                            .html(label_please_wait)
                            .attr("disabled", true);

                        $.ajax({
                            type: "POST",
                            url: baseUrl + "/update-" + type + "-status",
                            headers: {
                                "X-CSRF-TOKEN": $('input[name="_token"]').val(), // Use .val() instead of .attr('value')
                            },
                            data: {
                                id: id,
                                statusId: statusId,
                                note: $("#statusNote").val(),
                            },
                            success: function (response) {
                                $("#confirmUpdateStatus")
                                    .html(label_yes)
                                    .attr("disabled", false);
                                if (response.error == false) {
                                    setTimeout(function () {
                                        if (reload) {
                                            window.location.reload();
                                        }
                                    }, parseFloat(toastTimeOut) * 1000);
                                    $("#confirmUpdateStatusModal").modal(
                                        "hide"
                                    );
                                    var tableSelector =
                                        type == "project"
                                            ? "projects_table"
                                            : "task_table";
                                    var $table = $("#" + tableSelector);

                                    if ($table.length) {
                                        $table.bootstrapTable("refresh");
                                    }

                                    if ($("#activity_log_table").length) {
                                        $("#activity_log_table").bootstrapTable(
                                            "refresh"
                                        );
                                    }
                                    select.attr(
                                        "data-original-status-id",
                                        statusId
                                    );
                                    toastr.success(response.message);
                                } else {
                                    select
                                        .removeClass(newColorClass)
                                        .addClass(originalColorClass);
                                    select.val(originalStatusId);
                                    toastr.error(response.message);
                                }
                            },
                            error: function (xhr, status, error) {
                                $("#confirmUpdateStatus")
                                    .html(label_yes)
                                    .attr("disabled", false);
                                select
                                    .removeClass(newColorClass)
                                    .addClass(originalColorClass);
                                select.val(originalStatusId);
                                toastr.error("Something Went Wrong");
                            },
                        });
                    }
                );
            } else {
                $("#confirmUpdateStatus")
                    .html(label_yes)
                    .attr("disabled", false);
                select.val(originalStatusId);
                toastr.error(response.message);
            }
        },
        error: function (xhr, status, error) {
            toastr.error("Something Went Wrong");
        },
    });

    $("#confirmUpdateStatusModal").off(
        "click",
        ".btn-close, #declineUpdateStatus"
    );
    $("#confirmUpdateStatusModal").on(
        "click",
        ".btn-close, #declineUpdateStatus",
        function (e) {
            select.val(originalStatusId);
            select.removeClass(newColorClass).addClass(currentColorClass);
        }
    );
});

$(document).on("change", "#prioritySelect", function (e) {
    e.preventDefault();
    var id = $(this).data("id");
    var priorityId = this.value;
    var type = $(this).data("type") || "project";
    var reload = $(this).data("reload") || false;
    var select = $(this);
    var originalPriorityId = select.data("original-priority-id") || "";
    var originalColorClass = select.data("original-color-class");
    var classes = select.attr("class").split(" ");
    var currentColorClass = classes.filter(function (className) {
        return className.startsWith("select-");
    })[0];
    var selectedOption = select.find("option:selected");
    var selectedOptionClasses = selectedOption.attr("class").split(" ");
    var newColorClass = "select-" + selectedOptionClasses[1];
    select.removeClass(currentColorClass).addClass(newColorClass);

    $("#confirmUpdatePriorityModal").modal("show"); // show the confirmation modal
    $("#confirmUpdatePriorityModal").off("click", "#confirmUpdatePriority");

    $("#confirmUpdatePriorityModal").on(
        "click",
        "#confirmUpdatePriority",
        function (e) {
            $("#confirmUpdatePriority")
                .html(label_please_wait)
                .attr("disabled", true);
            $.ajax({
                type: "POST",
                url: baseUrl + "/update-" + type + "-priority",
                headers: {
                    "X-CSRF-TOKEN": $('input[name="_token"]').val(),
                },
                data: {
                    id: id,
                    priorityId: priorityId,
                },
                success: function (response) {
                    $("#confirmUpdatePriority")
                        .html(label_yes)
                        .attr("disabled", false);
                    if (response.error == false) {
                        setTimeout(function () {
                            if (reload) {
                                window.location.reload(); // Reload the current page
                            }
                        }, parseFloat(toastTimeOut) * 1000);
                        $("#confirmUpdatePriorityModal").modal("hide");

                        var tableSelector =
                            type == "project" ? "projects_table" : "task_table";
                        var $table = $("#" + tableSelector);

                        if ($table.length) {
                            $table.bootstrapTable("refresh");
                        }
                        if ($("#activity_log_table").length) {
                            $("#activity_log_table").bootstrapTable("refresh");
                        }
                        select.data("original-priority-id", priorityId);
                        toastr.success(response.message);
                    } else {
                        select
                            .removeClass(newColorClass)
                            .addClass(originalColorClass);
                        select.val(originalPriorityId);
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    $("#confirmUpdatePriority")
                        .html(label_yes)
                        .attr("disabled", false);
                    // Handle error
                    select
                        .removeClass(newColorClass)
                        .addClass(originalColorClass);
                    select.val(originalPriorityId);
                    toastr.error("Something Went Wrong");
                },
            });
        }
    );

    // Handle modal close event
    $("#confirmUpdatePriorityModal").off(
        "click",
        ".btn-close, #declineUpdatePriority"
    );
    $("#confirmUpdatePriorityModal").on(
        "click",
        ".btn-close, #declineUpdatePriority",
        function (e) {
            // Set original priority when modal is closed without confirmation
            select.val(originalPriorityId);
            select.removeClass(newColorClass).addClass(currentColorClass);
        }
    );
});

$(document).on("click", ".quick-view", function (e) {
    e.preventDefault();
    var id = $(this).data("id");
    var type = $(this).data("type") || "task";
    $("#type").val(type);
    $("#typeId").val(id);
    $.ajax({
        url: baseUrl + "/" + type + "s/get/" + id,
        type: "GET",
        success: function (response) {
            if (response.error == false) {
                $("#quickViewModal").modal("show");
                if (type == "task" && response.task) {
                    $("#quickViewTitlePlaceholder").text(response.task.title);
                    $("#quickViewDescPlaceholder").html(
                        response.task.description
                    );
                } else if (type == "project" && response.project) {
                    $("#quickViewTitlePlaceholder").text(
                        response.project.title
                    );
                    $("#quickViewDescPlaceholder").html(
                        response.project.description
                    );
                }
                $("#typePlaceholder").text(
                    type == "task" ? label_task : label_project
                );
                $("#usersTable").bootstrapTable("refresh");
                $("#clientsTable").bootstrapTable("refresh");
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr, status, error) {
            // Handle error
            toastr.error("Something Went Wrong");
        },
    });
});

$("#partialLeave, #updatePartialLeave").on("change", function () {
    var $form = $(this).closest("form"); // Get the closest form element
    var isChecked = $(this).prop("checked");
    if (isChecked) {
        // If the checkbox is checked
        $form
            .find(".leave-from-date-div")
            .removeClass("col-5")
            .addClass("col-3");
        $form.find(".leave-to-date-div").removeClass("col-5").addClass("col-3");
        $form
            .find(".leave-from-time-div, .leave-to-time-div")
            .removeClass("d-none");
    } else {
        // If the checkbox is unchecked, revert the changes
        $form.find('input[name="from_time"]').val("");
        $form.find('input[name="to_time"]').val("");
        $form
            .find(".leave-from-date-div")
            .removeClass("col-3")
            .addClass("col-5");
        $form.find(".leave-to-date-div").removeClass("col-3").addClass("col-5");
        $form
            .find(".leave-from-time-div, .leave-to-time-div")
            .addClass("d-none");
    }
});

$(".leaveVisibleToAll").on("change", function () {
    var $form = $(this).closest("form"); // Get the closest form element
    var isChecked = $(this).prop("checked");
    if (isChecked) {
        // If the checkbox is checked
        $form.find(".leaveVisibleToDiv").addClass("d-none");
        var visibleToSelect = $form.find(
            '.js-example-basic-multiple[name="visible_to_ids[]"]'
        );
        visibleToSelect.val(null).trigger("change");
    } else {
        // If the checkbox is unchecked, revert the changes
        $form.find(".leaveVisibleToDiv").removeClass("d-none");
    }
});
$(document).ready(function () {
    var upcomingBDCalendarInitialized = false;
    var upcomingWACalendarInitialized = false;
    var membersOnLeaveCalendarInitialized = false;

    // Add event listener for tab shown event
    $(".nav-tabs .nav-item").on("shown.bs.tab", function (event) {
        var tabId = $(event.target).attr("data-bs-target");

        if (
            tabId == "#navs-top-upcoming-birthdays-calendar" &&
            !upcomingBDCalendarInitialized
        ) {
            initializeUpcomingBDCalendar();
            upcomingBDCalendarInitialized = true;
        } else if (
            tabId == "#navs-top-upcoming-work-anniversaries-calendar" &&
            !upcomingWACalendarInitialized
        ) {
            initializeUpcomingWACalendar();
            upcomingWACalendarInitialized = true;
        } else if (
            tabId == "#navs-top-members-on-leave-calendar" &&
            !membersOnLeaveCalendarInitialized
        ) {
            initializeMembersOnLeaveCalendar();
            membersOnLeaveCalendarInitialized = true;
        }
    });
});

function initializeUpcomingBDCalendar() {
    var upcomingBDCalendar = document.getElementById(
        "upcomingBirthdaysCalendar"
    );

    // Check if the calendar element exists
    if (upcomingBDCalendar) {
        var BDcalendar = new FullCalendar.Calendar(upcomingBDCalendar, {
            plugins: ["interaction", "dayGrid", "list"],
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,listYear",
            },
            editable: true,
            events: function (fetchInfo, successCallback, failureCallback) {
                // Make AJAX request to fetch dynamic data
                $.ajax({
                    url: baseUrl + "/home/upcoming-birthdays-calendar",
                    type: "GET",
                    data: {
                        startDate: fetchInfo.startStr,
                        endDate: fetchInfo.endStr,
                    },
                    success: function (response) {
                        // Parse and format dynamic data for FullCalendar
                        var events = response.map(function (event) {
                            return {
                                title: event.title,
                                start: event.start,
                                end: event.start,
                                backgroundColor: event.backgroundColor,
                                borderColor: event.borderColor,
                                textColor: event.textColor,
                                userId: event.userId,
                                type: event.type,
                            };
                        });

                        // Invoke success callback with dynamic data
                        successCallback(events);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        // Invoke failure callback if there's an error
                        failureCallback(error);
                    },
                });
            },
            eventClick: function (info) {
                if (
                    info.event.extendedProps &&
                    info.event.extendedProps.userId
                ) {
                    var userId = info.event.extendedProps.userId;

                    // Check if the type is 'client'
                    if (info.event.extendedProps.type === "client") {
                        var url = baseUrl + "/clients/profile/" + userId; // Redirect to client's profile
                    } else {
                        var url = baseUrl + "/users/profile/" + userId; // Redirect to user's profile
                    }

                    window.location.href = url;
                }
            },
            eventMouseEnter: function (info) {
                // Create a tooltip element
                var tooltip = document.createElement("div");
                tooltip.innerHTML = info.event.title; // Set the tooltip content
                tooltip.style.position = "absolute";
                tooltip.style.background = "rgba(0, 0, 0, 0.8)";
                tooltip.style.color = "#fff";
                tooltip.style.padding = "5px";
                tooltip.style.borderRadius = "5px";
                tooltip.style.zIndex = "1000";
                tooltip.style.pointerEvents = "none"; // Prevent mouse events

                // Append the tooltip to the body
                document.body.appendChild(tooltip);

                // Position the tooltip
                var rect = info.el.getBoundingClientRect();
                tooltip.style.left = rect.left + window.scrollX + "px";
                tooltip.style.top = rect.bottom + window.scrollY + "px";

                // Remove tooltip on mouse leave
                info.el.addEventListener(
                    "mouseleave",
                    function () {
                        document.body.removeChild(tooltip);
                    },
                    { once: true }
                );
            },
        });
        BDcalendar.render();
    }
}
function initializeUpcomingWACalendar() {
    var upcomingWACalendar = document.getElementById(
        "upcomingWorkAnniversariesCalendar"
    );
    // Check if the calendar element exists
    if (upcomingWACalendar) {
        var WAcalendar = new FullCalendar.Calendar(upcomingWACalendar, {
            plugins: ["interaction", "dayGrid", "list"],
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,listYear",
            },
            editable: true,
            height: "auto",
            events: function (fetchInfo, successCallback, failureCallback) {
                // Make AJAX request to fetch dynamic data
                $.ajax({
                    url: baseUrl + "/home/upcoming-work-anniversaries-calendar",
                    type: "GET",
                    data: {
                        startDate: fetchInfo.startStr,
                        endDate: fetchInfo.endStr,
                    },
                    success: function (response) {
                        // Parse and format dynamic data for FullCalendar
                        var events = response.map(function (event) {
                            return {
                                title: event.title,
                                start: event.start,
                                end: event.start,
                                backgroundColor: event.backgroundColor,
                                borderColor: event.borderColor,
                                textColor: event.textColor,
                                userId: event.userId,
                                type: event.type,
                            };
                        });

                        // Invoke success callback with dynamic data
                        successCallback(events);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        // Invoke failure callback if there's an error
                        failureCallback(error);
                    },
                });
            },
            eventClick: function (info) {
                if (
                    info.event.extendedProps &&
                    info.event.extendedProps.userId
                ) {
                    var userId = info.event.extendedProps.userId;

                    // Check if the type is 'client'
                    if (info.event.extendedProps.type === "client") {
                        var url = baseUrl + "/clients/profile/" + userId; // Redirect to client's profile
                    } else {
                        var url = baseUrl + "/users/profile/" + userId; // Redirect to user's profile
                    }

                    window.location.href = url;
                }
            },
            eventMouseEnter: function (info) {
                // Create a tooltip element
                var tooltip = document.createElement("div");
                tooltip.innerHTML = info.event.title; // Set the tooltip content
                tooltip.style.position = "absolute";
                tooltip.style.background = "rgba(0, 0, 0, 0.8)";
                tooltip.style.color = "#fff";
                tooltip.style.padding = "5px";
                tooltip.style.borderRadius = "5px";
                tooltip.style.zIndex = "1000";
                tooltip.style.pointerEvents = "none"; // Prevent mouse events

                // Append the tooltip to the body
                document.body.appendChild(tooltip);

                // Position the tooltip
                var rect = info.el.getBoundingClientRect();
                tooltip.style.left = rect.left + window.scrollX + "px";
                tooltip.style.top = rect.bottom + window.scrollY + "px";

                // Remove tooltip on mouse leave
                info.el.addEventListener(
                    "mouseleave",
                    function () {
                        document.body.removeChild(tooltip);
                    },
                    { once: true }
                );
            },
        });
        WAcalendar.render();
    }
}

function initializeMembersOnLeaveCalendar() {
    var membersOnLeaveCalendar = document.getElementById(
        "membersOnLeaveCalendar"
    );
    // Check if the calendar element exists
    if (membersOnLeaveCalendar) {
        var MOLcalendar = new FullCalendar.Calendar(membersOnLeaveCalendar, {
            plugins: ["interaction", "dayGrid", "list"],
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,listYear",
            },
            editable: true,
            displayEventTime: true,
            events: function (fetchInfo, successCallback, failureCallback) {
                // Make AJAX request to fetch dynamic data
                $.ajax({
                    url: baseUrl + "/home/members-on-leave-calendar",
                    type: "GET",
                    data: {
                        startDate: fetchInfo.startStr,
                        endDate: fetchInfo.endStr,
                    },
                    success: function (response) {
                        // Parse and format dynamic data for FullCalendar
                        var events = response.map(function (event) {
                            var eventData = {
                                title: event.title,
                                start: event.start,
                                end: moment(event.end)
                                    .add(1, "days")
                                    .format("YYYY-MM-DD"),
                                backgroundColor: event.backgroundColor,
                                borderColor: event.borderColor,
                                textColor: event.textColor,
                                userId: event.userId,
                            };

                            // Check if the event is partial and has start and end times
                            if (event.startTime && event.endTime) {
                                // Include start and end times directly in the event data
                                eventData.extendedProps = {
                                    startTime: event.startTime,
                                    endTime: event.endTime,
                                };
                            }
                            return eventData;
                        });

                        // Invoke success callback with dynamic data
                        successCallback(events);
                    },

                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        // Invoke failure callback if there's an error
                        failureCallback(error);
                    },
                });
            },
            eventClick: function (info) {
                if (
                    info.event.extendedProps &&
                    info.event.extendedProps.userId
                ) {
                    var userId = info.event.extendedProps.userId;
                    var url = baseUrl + "/users/profile/" + userId;
                    window.location.href = url;
                }
            },
            eventMouseEnter: function (info) {
                // Create a tooltip element
                var tooltip = document.createElement("div");
                tooltip.innerHTML = info.event.title; // Set the tooltip content
                tooltip.style.position = "absolute";
                tooltip.style.background = "rgba(0, 0, 0, 0.8)";
                tooltip.style.color = "#fff";
                tooltip.style.padding = "5px";
                tooltip.style.borderRadius = "5px";
                tooltip.style.zIndex = "1000";
                tooltip.style.pointerEvents = "none"; // Prevent mouse events

                // Append the tooltip to the body
                document.body.appendChild(tooltip);

                // Position the tooltip
                var rect = info.el.getBoundingClientRect();
                tooltip.style.left = rect.left + window.scrollX + "px";
                tooltip.style.top = rect.bottom + window.scrollY + "px";

                // Remove tooltip on mouse leave
                info.el.addEventListener(
                    "mouseleave",
                    function () {
                        document.body.removeChild(tooltip);
                    },
                    { once: true }
                );
            },
        });
        MOLcalendar.render();
    }
}

// Preprocess permissions to avoid redundant checks
var permissionSet = new Set(permissions);

$(document).ready(function () {
    // Loop through classes starting with 'action-'
    $('[class*="action_"]').each(function () {
        // Extract the part of class name after "action-"
        var className = $(this).attr("class");
        var permission = className.substring(
            className.indexOf("action_") + "action_".length
        );
        // Check if the user is not an admin and if the permission does not exist
        if (
            (typeof isAdmin == "undefined" || !isAdmin) &&
            !permissionSet.has(permission)
        ) {
            $(this).addClass("d-none");
        }
    });
});

$(document).on("click", ".save-column-visibility", function (e) {
    e.preventDefault();
    var tableName = $(this).data("table");
    var type = $(this).data("type");
    type = type.replace("-", "_");
    $("#confirmSaveColumnVisibility").modal("show");
    $("#confirmSaveColumnVisibility").off("click", "#confirm");
    $("#confirmSaveColumnVisibility").on("click", "#confirm", function () {
        $("#confirmSaveColumnVisibility")
            .find("#confirm")
            .html(label_please_wait)
            .attr("disabled", true);
        var visibleColumns = [];
        $("#" + tableName)
            .bootstrapTable("getVisibleColumns")
            .forEach((column) => {
                if (!column.checkbox) {
                    visibleColumns.push(column.field);
                }
            });

        // Send preferences to the server
        $.ajax({
            url: baseUrl + "/save-column-visibility",
            type: "POST",
            data: {
                type: type,
                visible_columns: JSON.stringify(visibleColumns),
            },
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
            },
            success: function (response) {
                $("#confirmSaveColumnVisibility")
                    .find("#confirm")
                    .html(label_yes)
                    .attr("disabled", false);
                if (response.error == false) {
                    $("#confirmSaveColumnVisibility").modal("hide");
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (data) {
                $("#confirmSaveColumnVisibility")
                    .find("#confirm")
                    .html(label_yes)
                    .attr("disabled", false);
                $("#confirmSaveColumnVisibility").modal("hide");
                toastr.error(label_something_went_wrong);
            },
        });
    });
});

$(document).on("click", ".viewAssigned", function (e) {
    e.preventDefault();
    var projectsUrl = baseUrl + "/projects/listing";
    var tasksUrl = baseUrl + "/tasks/list";
    var id = $(this).data("id");
    var type = $(this).data("type");
    var user = $(this).data("user");
    projectsUrl = projectsUrl + (id ? "/" + id : "");
    tasksUrl = tasksUrl + (id ? "/" + id : "");
    $("#viewAssignedModal").modal("show");
    var projectsTable = $("#viewAssignedModal").find("#projects_table");
    var tasksTable = $("#viewAssignedModal").find("#task_table");
    if (type === "tasks") {
        $('.nav-link[data-bs-target="#navs-top-view-assigned-tasks"]').tab(
            "show"
        );
        $(
            '.nav-link[data-bs-target="#navs-top-view-assigned-projects"]'
        ).removeClass("active");
        $("#navs-top-view-assigned-projects").removeClass("show active");
        $("#navs-top-view-assigned-tasks").addClass("show active");
    } else {
        $('.nav-link[data-bs-target="#navs-top-view-assigned-projects"]').tab(
            "show"
        );
        $(
            '.nav-link[data-bs-target="#navs-top-view-assigned-tasks"]'
        ).removeClass("active");
        $("#navs-top-view-assigned-tasks").removeClass("show active");
        $("#navs-top-view-assigned-projects").addClass("show active");
    }
    $("#userPlaceholder").text(user);

    $(projectsTable).bootstrapTable("refresh", {
        url: projectsUrl,
    });
    $(tasksTable).bootstrapTable("refresh", {
        url: tasksUrl,
    });
});

$(document).on("click", ".openCreateStatusModal", function (e) {
    e.preventDefault();
    $("#create_status_modal").modal("show");
});

$(document).on("click", ".openCreatePriorityModal", function (e) {
    e.preventDefault();
    $("#create_priority_modal").modal("show");
});

$(document).on("click", ".openCreateTagModal", function (e) {
    e.preventDefault();
    $("#create_tag_modal").modal("show");
});

$(document).on("click", ".openCreateContractTypeModal", function (e) {
    e.preventDefault();
    $("#create_contract_type_modal").modal("show");
});

$(document).on("click", ".openCreatePmModal", function (e) {
    e.preventDefault();
    $("#create_pm_modal").modal("show");
});

$(document).on("click", ".openCreateAllowanceModal", function (e) {
    e.preventDefault();
    $("#create_allowance_modal").modal("show");
});

$(document).on("click", ".openCreateDeductionModal", function (e) {
    e.preventDefault();
    $("#create_deduction_modal").modal("show");
});

function formatTag(tag) {
    alert("test");
    if (!tag.id) {
        return tag.text;
    }
    var color = tag.color;
    return $(
        '<span class="badge bg-label-' + color + '">' + tag.text + "</span>"
    );
}
$(document).ready(function () {
    function formatStatus(status) {
        if (!status.id) {
            return status.text;
        }
        var color = $(status.element).data("color");
        var $status = $(
            '<span class="badge bg-label-' +
                color +
                '">' +
                status.text +
                "</span>"
        );
        return $status;
    }

    function formatPriority(priority) {
        if (!priority.id) {
            return priority.text;
        }
        var color = $(priority.element).data("color");
        var $priority = $(
            '<span class="badge bg-label-' +
                color +
                '">' +
                priority.text +
                "</span>"
        );
        return $priority;
    }

    $(".statusDropdown").each(function () {
        var $this = $(this);
        $this.select2({
            dropdownParent: $this.closest(".modal"),
            templateResult: formatStatus,
            templateSelection: formatStatus,
            escapeMarkup: function (markup) {
                return markup;
            },
            language: {
                noResults: function () {
                    return label_no_results_found;
                },
                searching: function () {
                    return label_searching;
                },
            },
        });
    });

    $(".priorityDropdown").each(function () {
        var $this = $(this);

        $this.select2({
            dropdownParent: $this.closest(".modal"),
            templateResult: formatPriority,
            templateSelection: formatPriority,
            allowClear: true,
            escapeMarkup: function (markup) {
                return markup;
            },
            language: {
                noResults: function () {
                    return label_no_results_found;
                },
                searching: function () {
                    return label_searching;
                },
            },
        });

        // Prevent dropdown from opening when clear button is clicked
        $this
            .on("select2:unselecting", function (e) {
                $(this).data("state", "unselecting");
            })
            .on("select2:open", function (e) {
                if ($(this).data("state") === "unselecting") {
                    $(this).removeData("state");
                    $this.select2("close"); // Close the dropdown immediately
                }
            });
    });
});
$(document).on("change", 'select[name="color"]', function (e) {
    e.preventDefault();
    var select = $(this);
    var classes = $(this).attr("class").split(" ");
    var currentColorClass = classes.filter(function (className) {
        return className.startsWith("select-");
    })[0];
    var selectedOption = $(this).find("option:selected");
    var selectedOptionClasses = selectedOption.attr("class").split(" ");
    var newColorClass = "select-" + selectedOptionClasses[1];
    select.removeClass(currentColorClass).addClass(newColorClass);
});
function toggleChatIframe() {
    var iframeContainer = document.getElementById("chatIframeContainer");
    if (
        iframeContainer.style.display === "none" ||
        iframeContainer.style.display === ""
    ) {
        iframeContainer.style.display = "block";
    } else {
        iframeContainer.style.display = "none";
    }
}

$(document).ready(function () {
    if ($("#selectAllPreferences").length) {
        // Check initial state of checkboxes and update selectAllPreferences checkbox
        updateSelectAll();

        // Select/deselect all checkboxes when the selectAllPreferences checkbox is clicked
        $("#selectAllPreferences").click(function () {
            var isChecked = $(this).prop("checked");
            $('input[name="enabled_notifications[]"]:not(:disabled)').prop(
                "checked",
                isChecked
            );
        });

        // Update the selectAllPreferences checkbox state based on the checkboxes' status
        $('input[name="enabled_notifications[]"]').change(function () {
            updateSelectAll();
        });

        // Function to update selectAllPreferences checkbox based on checkboxes' status
        function updateSelectAll() {
            var allChecked =
                $('input[name="enabled_notifications[]"]:not(:disabled)')
                    .length ===
                $(
                    'input[name="enabled_notifications[]"]:not(:disabled):checked'
                ).length;
            $("#selectAllPreferences").prop("checked", allChecked);
        }
    }
});

// $(window).on('load', function () {

//     // Select the elements and replace the text
//     $('.pagination-info').each(function () {
//         var text = $(this).text();
//         text = text.replace("Showing", label_showing)
//             .replace("to", label_to_for_pagination)
//             .replace("of", label_of)
//             .replace("rows", label_rows);
//         $(this).text(text);
//     });

//     $('.page-list').each(function () {
//         var text = $(this).html();
//         text = text.replace("rows per page", label_rows_per_page);
//         $(this).html(text);
//     });
// });

$("#internal_client").change(function () {
    var isChecked = $(this).prop("checked");

    $("#password, #password_confirmation").val("");
    $("#passDiv, #confirmPassDiv, #statusDiv, #requireEvDiv").toggleClass(
        "d-none",
        isChecked
    );

    $("#client_deactive").prop("checked", true);
    $("#require_ev_" + (isChecked ? "no" : "yes")).prop("checked", true);

    $("#password").next(".error-message").remove();
    $("#password_confirmation").next(".error-message").remove();
});

$("#update_internal_client").change(function () {
    var isChecked = $(this).prop("checked");

    $("#password, #password_confirmation").val("");
    $("#passDiv, #confirmPassDiv, #statusDiv, #requireEvDiv").toggleClass(
        "d-none",
        isChecked
    );

    // Remove .error-message elements next to #password and #password_confirmation
    $("#password").next(".error-message").remove();
    $("#password_confirmation").next(".error-message").remove();
});

$(document).ready(function () {
    $("#previewToast").click(function () {
        var previewToastPosition = $("#toastPosition").val();
        var toastTimeoutInput = $("#toastTimeout");
        var previewToastTimeout = parseFloat(toastTimeoutInput.val());

        // Validate toast timeout is not blank and is a positive number
        if (isNaN(previewToastTimeout) || previewToastTimeout <= 0) {
            toastr.options = {
                positionClass: toastPosition,
                timeOut: parseFloat(toastTimeOut) * 1000,
                showDuration: "300",
                hideDuration: "1000",
                extendedTimeOut: "1000",
                progressBar: true,
                closeButton: true,
            };
            toastr.error("Please enter a valid timeout value in seconds.");
            toastTimeoutInput.focus();
            return;
        }

        // Convert timeout to milliseconds
        previewToastTimeout *= 1000;

        toastr.options = {
            positionClass: previewToastPosition,
            timeOut: previewToastTimeout,
            showDuration: "300",
            hideDuration: "1000",
            extendedTimeOut: "1000",
            progressBar: true,
            closeButton: true,
        };

        toastr.success(
            "This is a preview of your toast message!",
            "Toast Preview"
        );
    });
});

$(document).ready(function () {
    var $canvas = $("#promisor_sign");
    var $resetButton = $("#reset_promisor_sign");

    // Function to resize canvas
    function resizeCanvas() {
        var $modalBody = $canvas.closest(".modal-body");
        var maxWidth = $modalBody.width() - 32; // Subtract padding
        var aspectRatio = $canvas[0].width / $canvas[0].height;

        $canvas.attr("width", maxWidth);
        $canvas.attr("height", maxWidth / aspectRatio);
    }

    // Resize canvas when the modal is shown
    $("#create_contract_sign_modal").on("shown.bs.modal", function () {
        resizeCanvas();
    });

    // Handle canvas reset
    $resetButton.on("click", function () {
        var context = $canvas[0].getContext("2d");
        context.clearRect(0, 0, $canvas[0].width, $canvas[0].height);
    });
});

$(document).on("click", "#testSmsSettingsButton", function (e) {
    e.preventDefault();
    $("#testSmsSettingsModal").modal("show");
});

$("#testSmsSettingsForm").on("submit", function (event) {
    event.preventDefault();
    var recipientNumber = $("#testSmsRecipientNumber").val();
    var recipientCountryCode = $("#testSmsRecipientCountryCode").val();
    var message = $("#testSmsMessage").val();
    // AJAX request
    $.ajax({
        url: baseUrl + "/settings/notifications/test",
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        data: {
            type: "sms",
            recipientCountryCode: recipientCountryCode,
            recipientNumber: recipientNumber,
            message: message,
        },
        dataType: "json",
        beforeSend: function () {
            $("#performTestSmsSettingsButton")
                .prop("disabled", true)
                .html(label_sending);
        },
        success: function (response) {
            // Handle successful response
            $("#performTestSmsSettingsButton")
                .prop("disabled", false)
                .html(label_submit);
            $("#smsTestResponse").removeClass("d-none");
            $("#smsResponseText").text(JSON.stringify(response, null, 2));
        },
        error: function (xhr, status, error) {
            // Handle error
            $("#performTestSmsSettingsButton")
                .prop("disabled", false)
                .html(label_submit);
            $("#smsTestResponse").removeClass("d-none");
            $("#smsResponseText").text("Error: " + xhr.responseText);
        },
    });
});

$("#testSmsSettingsModal").on("hidden.bs.modal", function () {
    $("#smsTestResponse").addClass("d-none");
    $("#smsResponseText").text("");
});

$(document).on("click", "#testWhatsappSettingsButton", function (e) {
    e.preventDefault();
    $("#testWhatsappSettingsModal").modal("show");
});

$("#testWhatsappSettingsForm").on("submit", function (event) {
    event.preventDefault();
    var recipientNumber = $("#testWhatsappRecipientNumber").val();
    var recipientCountryCode = $("#testWhatsappRecipientCountryCode").val();
    var message = $("#testWhatsappMessage").val();
    // AJAX request
    $.ajax({
        url: baseUrl + "/settings/notifications/test",
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        data: {
            type: "whatsapp",
            recipientCountryCode: recipientCountryCode,
            recipientNumber: recipientNumber,
            message: message,
        },
        dataType: "json",
        beforeSend: function () {
            $("#performTestWhatsappSettingsButton")
                .prop("disabled", true)
                .html(label_sending);
        },
        success: function (response) {
            // Handle successful response
            $("#performTestWhatsappSettingsButton")
                .prop("disabled", false)
                .html(label_submit);
            $("#whatsappTestResponse").removeClass("d-none");
            $("#whatsappResponseText").text(JSON.stringify(response, null, 2));
        },
        error: function (xhr, status, error) {
            // Handle error
            $("#performTestWhatsappSettingsButton")
                .prop("disabled", false)
                .html(label_submit);
            $("#whatsappTestResponse").removeClass("d-none");
            $("#whatsappResponseText").text("Error: " + xhr.responseText);
        },
    });
});

$("#testWhatsappSettingsModal").on("hidden.bs.modal", function () {
    $("#whatsappTestResponse").addClass("d-none");
    $("#whatsappResponseText").text("");
});

$(document).on("click", "#testSlackSettingsButton", function (e) {
    e.preventDefault();
    $("#testSlackSettingsModal").modal("show");
});

$("#testSlackSettingsForm").on("submit", function (event) {
    event.preventDefault();
    var recipientEmail = $("#testSlackRecipientEmail").val();
    var message = $("#testSlackMessage").val();
    // AJAX request
    $.ajax({
        url: baseUrl + "/settings/notifications/test",
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
        },
        data: {
            type: "slack",
            recipientEmail: recipientEmail,
            message: message,
        },
        dataType: "json",
        beforeSend: function () {
            $("#performTestSlackSettingsButton")
                .prop("disabled", true)
                .html(label_sending);
        },
        success: function (response) {
            // Handle successful response
            $("#performTestSlackSettingsButton")
                .prop("disabled", false)
                .html(label_submit);
            $("#slackTestResponse").removeClass("d-none");
            $("#slackResponseText").text(JSON.stringify(response, null, 2));
        },
        error: function (xhr, status, error) {
            // Handle error
            $("#performTestSlackSettingsButton")
                .prop("disabled", false)
                .html(label_submit);
            $("#slackTestResponse").removeClass("d-none");
            $("#slackResponseText").text("Error: " + xhr.responseText);
        },
    });
});

$("#testSlackSettingsModal").on("hidden.bs.modal", function () {
    $("#slackTestResponse").addClass("d-none");
    $("#slackResponseText").text("");
});

$(document).ready(function () {
    // Function to validate input
    function validateCurrencyInput() {
        var input = $(this);
        var value = input.val();

        // Check for disallowed characters
        if (/[^0-9.,]/.test(value)) {
            toastr.error(label_currency_restriction);
            value = value.replace(/[^0-9.,]/g, "");
        }

        // Check for multiple decimal points
        var multipleDecimalPoints = value.split(".").length - 1;
        if (multipleDecimalPoints > 1) {
            toastr.error(label_currency_restriction_1);
            // Keep only the first decimal point
            value = value.replace(/(\..*)\./g, "$1");
        }

        input.val(value);
    }

    // Apply validation to all inputs with class "currency"
    $(document).on("input", ".currency", validateCurrencyInput);

    function validateDecimalInput() {
        var input = $(this);
        var value = input.val();

        // Remove any commas
        value = value.replace(/,/g, "");

        // Check for disallowed characters (anything other than digits and decimal point)
        if (/[^0-9.]/.test(value)) {
            toastr.error(label_currency_restriction_2);
            value = value.replace(/[^0-9.]/g, "");
        }

        // Check for multiple decimal points
        var multipleDecimalPoints = value.split(".").length - 1;
        if (multipleDecimalPoints > 1) {
            toastr.error(label_currency_restriction_1);
            // Keep only the first decimal point
            value = value.replace(/(\..*)\./g, "$1");
        }

        input.val(value);
    }

    $(document).on("input", ".decimal-currency", validateDecimalInput);
});

$(document).ready(function () {
    const input = $("#phone")[0]; // Get the actual DOM element for intlTelInput

    // Check if the input element exists and has the data-type="create" attribute
    if (input) {
        var $countryCodeIsoInput = $("#country_iso_code");
        var $countryCodeNumInput = $("#country_code");
        var initialCountryCode = "";

        // Check if the hidden input exists and has a value
        if ($countryCodeIsoInput.length && $countryCodeIsoInput.val()) {
            initialCountryCode = $countryCodeIsoInput.val();
        }

        // Determine whether to set initial country to 'auto' or leave it unset
        var auto = $(input).data("type") === "create" ? "auto" : "";

        // Initialize intlTelInput with the appropriate initial country setting
        const iti = window.intlTelInput(input, {
            initialCountry: initialCountryCode || auto, // Set to 'auto' or initialCountryCode if available
            geoIpLookup: (callback) => {
                fetch("https://ipapi.co/json")
                    .then((res) => res.json())
                    .then((data) => callback(data.country_code))
                    .catch(() => callback("us"));
            },
            utilsScript:
                "https://cdn.jsdelivr.net/npm/intl-tel-input@24.3.4/build/js/utils.js",
            separateDialCode: true,
        });

        $(input).on("countrychange", () => {
            const countryData = iti.getSelectedCountryData();

            if (countryData && countryData.iso2 && countryData.dialCode) {
                // Update the hidden input with the selected country code
                $countryCodeIsoInput.val(countryData.iso2);
                $countryCodeNumInput.val("+" + countryData.dialCode);
            } else {
                // Clear the hidden inputs if the country data is not valid
                $countryCodeIsoInput.val("");
                $countryCodeNumInput.val("");
                iti.setCountry("");
            }
        });

        // Clear and reset country selection when the phone input is cleared
        $(input).on("input", function () {
            if ($(this).val() === "") {
                $countryCodeIsoInput.val("");
                $countryCodeNumInput.val("");
                iti.setCountry("");
            }
        });

        // Add functionality to clear the phone input and reset the country code
        $(".clear-input").on("click", function () {
            $(input).val(""); // Clear the phone input
            $countryCodeIsoInput.val(""); // Clear the hidden country code fields
            $countryCodeNumInput.val("");
            iti.setCountry(""); // Clear the country flag
        });
    }
});

function initSelect2WithAjax(selector, type) {
    $(selector).each(function () {
        if ($(this).length) {
            var $this = $(this);
            var allowClear =
                $this.data("allow-clear") === "false" ? false : true;
            var leaveVisibleToUsers = $this.data("leave-visible-to-users");
            leaveVisibleToUsers =
                leaveVisibleToUsers == undefined
                    ? false
                    : leaveVisibleToUsers === false
                    ? false
                    : true;
            var ignoreAdmins = $this.data("ignore-admins");
            ignoreAdmins =
                ignoreAdmins == undefined
                    ? false
                    : ignoreAdmins === false
                    ? false
                    : true;
            // Check if the 'data-consider-workspace' attribute is defined
            var considerWorkspace = $this.data("consider-workspace");
            // If 'considerWorkspace' is undefined, default to true
            considerWorkspace =
                considerWorkspace == undefined
                    ? true
                    : considerWorkspace === false
                    ? false
                    : true;
            var singleSelect =
                $this.data("single-select") === undefined ||
                $this.data("single-select") === false
                    ? false
                    : true;
            var ajaxOptions = {
                ajax: {
                    url: "/search", // API endpoint to fetch data dynamically
                    dataType: "json",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            type: type, // dynamic type: 'tags', 'statuses', 'priorities'
                            considerWorkspace: considerWorkspace,
                            leaveVisibleToUsers: leaveVisibleToUsers,
                            ignoreAdmins: ignoreAdmins,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results.map(function (item) {
                                // Handle 'color' only for 'tags'
                                // if (type === 'tags') {
                                //     return {
                                //         id: item.id,
                                //         text: item.text,
                                //         color: item.color
                                //     };
                                // }
                                // Default handling for other types
                                return {
                                    id: item.id,
                                    text: item.text,
                                };
                            }),
                        };
                    },
                    cache: true,
                },
                minimumInputLength: 1,
                allowClear: allowClear,
                closeOnSelect: singleSelect,
                language: {
                    inputTooShort: function () {
                        return label_please_type_at_least_1_character;
                    },
                    searching: function () {
                        return label_searching;
                    },
                    noResults: function () {
                        return label_no_results_found;
                    },
                },
            };

            // Apply specific templates if type is 'tags'
            // if (type === 'tags') {
            //     ajaxOptions.templateResult = formatTag;
            //     ajaxOptions.templateSelection = formatTag;
            //     ajaxOptions.escapeMarkup = function (markup) {
            //         return markup; // Prevent escaping of markup
            //     };
            // }

            // Check if the element is inside a modal
            if (
                $this.closest(".modal").length &&
                $this.data("single-select") == true
            ) {
                var modalId = $this.closest(".modal").attr("id"); // Get the ID of the closest .modal
                if (modalId) {
                    ajaxOptions.dropdownParent = $("#" + modalId); // Use the ID to reference the modal
                }
            }
            $this.select2(ajaxOptions);

            $(".cancel-button").on("click", function () {
                $this.select2("close"); // Close the dropdown
            });
        }
    });
}
$(document).ready(function () {
    initSelect2WithAjax(".projects_select", "projects");
    initSelect2WithAjax(".users_select", "users");
    initSelect2WithAjax(".clients_select", "clients");
    initSelect2WithAjax(".tags_select", "tags");
    initSelect2WithAjax(".contract_types_select", "contract_types");
    initSelect2WithAjax(".expense_types_select", "expense_types");
    initSelect2WithAjax(".allowances_select", "allowances");
    initSelect2WithAjax(".deductions_select", "deductions");
    initSelect2WithAjax(".items_select", "items");
    initSelect2WithAjax(".invoices_select", "invoices");
    initSelect2WithAjax(".statuses_filter", "statuses");
    initSelect2WithAjax(".priorities_filter", "priorities");

    $("#create_task_modal, #edit_task_modal")
        .find('select[name="user_id[]"]')
        .each(function () {
            if ($(this).length) {
                $(this).select2({
                    minimumInputLength: 1,
                    allowClear: true,
                    language: {
                        inputTooShort: function () {
                            return label_please_type_at_least_1_character;
                        },
                        searching: function () {
                            return label_searching;
                        },
                        noResults: function () {
                            return label_no_results_found;
                        },
                    },
                });
            }
        });
});

$(document).ready(function () {
    // Function to load users for a specific project
    function loadProjectUsers(projectId) {
        var usersSelect = $("#create_task_modal").find(
            'select[name="user_id[]"]'
        );
        usersSelect.empty(); // Clear any previous options

        if (projectId) {
            $.ajax({
                url: baseUrl + "/projects/get/" + projectId, // Endpoint to get users based on project
                type: "GET",
                success: function (response) {
                    // Add the project users as options
                    if (response.users && response.users.length > 0) {
                        // Iterate through the users and add them to the select element
                        response.users.forEach(function (user) {
                            var userOption = new Option(
                                user.first_name + " " + user.last_name,
                                user.id,
                                false,
                                false
                            );
                            usersSelect.append(userOption);
                        });

                        // If task_accessibility is 'project_users', select the users automatically
                        if (
                            response.project.task_accessibility ===
                            "project_users"
                        ) {
                            var projectUserIds = response.users.map(function (
                                user
                            ) {
                                return user.id;
                            });

                            // Set selected users
                            usersSelect.val(projectUserIds);
                        }

                        // Trigger select2 to update the selected values
                        usersSelect.trigger("change");
                    } else {
                        // Handle case when there are no users
                        usersSelect.val(null).trigger("change");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error loading project users:", error);
                },
            });
        }
    }

    // Check if the project is set via a hidden input (when project is not selectable)
    var projectInput = $('input[name="project"]'); // Cache the selector
    if (projectInput.length) {
        var projectId = projectInput.val();
        if (projectId) {
            loadProjectUsers(projectId); // Load users if the project is pre-selected and not selectable
        }
    }
});

$(document).ready(function () {
    $("#generate-password").on("click", function () {
        function generatePassword(length) {
            var charset =
                "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
            var password = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                password += charset.charAt(Math.floor(Math.random() * n));
            }
            return password;
        }

        // Generate a new random password
        var newPassword = generatePassword(12);

        // Set the generated password in both password and confirm password fields
        $("#password").val(newPassword);
        $("#password_confirmation").val(newPassword);

        // Ensure password is visible after generation
        var passwordField = $("#password");
        var toggleIcon = $(".toggle-password i");

        // Explicitly set the password field type to 'text'
        if (passwordField.attr("type") === "password") {
            passwordField.attr("type", "text"); // Show password
            // Ensure the toggle icon is in 'show' state
            toggleIcon.removeClass("bx-hide").addClass("bx-show");
        }
    });
});

$("#create_project_modal").on("shown.bs.modal", function (event) {
    var currentUrl = window.location.pathname;

    // Check if the current URL contains one of the favorite project routes
    if (
        currentUrl.includes("/kanban/favorite") ||
        currentUrl.includes("/list/favorite") ||
        currentUrl.includes("/favorite")
    ) {
        $("#create_project_modal #is_favorite").val(1); // Set is_favorite to 1 if on a favorite page
    } else {
        $("#create_project_modal #is_favorite").val(0); // Set is_favorite to 0 if not on a favorite page
    }

    var button = $(event.relatedTarget); // Button that triggered the modal
    var statusId = button.data("status-id"); // Extract status ID from data attribute

    // Find the status dropdown
    var $statusDropdown = $(this).find('select[name="status_id"]');

    // Check if the status ID is defined
    if (statusId) {
        // Check if the dropdown contains the option with the given status ID
        if ($statusDropdown.find(`option[value="${statusId}"]`).length) {
            // Set the selected status in the dropdown
            $statusDropdown.val(statusId).trigger("change");
        }
    }
});
$("#create_task_modal").on("shown.bs.modal", function (event) {
    if (window.location.search.includes("favorite=1")) {
        $("#create_task_modal #is_favorite").val(1); // Set is_favorite to 1 if the query parameter favorite=1 is present
    } else {
        $("#create_task_modal #is_favorite").val(0); // Set is_favorite to 0 if the query parameter favorite is not present
    }
});
// Initialize the calendar on page load
$(document).ready(function () {
    var taskCalenderDiv = document.getElementById("taskCalenderDiv");
    if (taskCalenderDiv) {
        calenderView(taskCalenderDiv);
    }
});
// Calendar View For the Tasks
function calenderView(taskCalenderDiv) {
    // Check if the calendar element exists
    var taskcalendar = new FullCalendar.Calendar(taskCalenderDiv, {
        plugins: ["interaction", "dayGrid", "list"],
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,listYear",
        },
        editable: true,
        selectable: true,
        selectHelper: true,
        height: "auto",
        events: function (fetchInfo, successCallback, failureCallback) {
            // Fetch tasks for the current month
            fetchTasks(
                fetchInfo.start,
                fetchInfo.end,
                successCallback,
                failureCallback
            );
        },
        datesSet: function (info) {
            // Fetch tasks when the month changes
            taskcalendar.removeAllEvents();
            taskcalendar.refetchEvents();
        },
        eventClick: function (info) {
            // Show the edit modal
            $("#edit_task_modal").modal("show");

            // AJAX call to fetch the event details (similar to fetching task details)
            $.ajax({
                url: baseUrl + "/tasks/get/" + info.event.id, // Fetch event by ID
                type: "GET",
                headers: {
                    "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"), // CSRF token for security
                },
                dataType: "json",
                success: function (response) {
                    if (response.error == false) {
                        // Format the start and end dates
                        var formattedStartDate = response.task.start_date
                            ? moment(response.task.start_date).format(
                                  js_date_format
                              )
                            : "";
                        var formattedEndDate = response.task.due_date
                            ? moment(response.task.due_date).format(
                                  js_date_format
                              )
                            : "";

                        // Update the modal fields with task data
                        $("#id").val(response.task.id);
                        $("#title").val(response.task.title);
                        $("#project_status_id")
                            .val(response.task.status_id)
                            .trigger("change");
                        $("#priority_id")
                            .val(response.task.priority_id)
                            .trigger("change");
                        $("#update_start_date").val(formattedStartDate);
                        $("#update_end_date").val(formattedEndDate);

                        // Initialize date pickers for start and end dates
                        initializeDateRangePicker(
                            "#update_start_date, #update_end_date"
                        );

                        // Update the project title and task note
                        $("#update_project_title").val(response.project.title);
                        $("#taskNote").val(response.task.note);

                        // Set task description (handling null case)
                        $("#edit_task_modal")
                            .find("#task_description")
                            .val(response.task.description || "");

                        // Populate users associated with the project
                        var usersSelect = $("#edit_task_modal").find(
                            'select[name="user_id[]"]'
                        );
                        usersSelect.empty(); // Clear existing options

                        // Populate users from response
                        if (
                            response.project &&
                            response.project.users &&
                            response.project.users.length > 0
                        ) {
                            response.project.users.forEach(function (user) {
                                var userOption = new Option(
                                    user.first_name + " " + user.last_name,
                                    user.id,
                                    false,
                                    false
                                ); // Unselected initially
                                usersSelect.append(userOption);
                            });
                        }

                        // Set the selected values based on the task's users
                        if (
                            response.task &&
                            response.task.users &&
                            response.task.users.length > 0
                        ) {
                            var selectedTaskUsers = response.task.users.map(
                                function (user) {
                                    return user.id; // Get the user IDs from task
                                }
                            );

                            // Set the selected values in the dropdown
                            usersSelect.val(selectedTaskUsers);
                        } else {
                            // Handle case when there are no task users
                            usersSelect.val(null);
                        }

                        usersSelect.trigger("change"); // Trigger change to reflect selection
                    } else {
                        console.error("Failed to fetch event details");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                },
            });
        },
        dateClick: function (info) {
            var start = moment(info.dateStr).format(js_date_format);
            $("#task_start_date").val(start);
            $("#task_end_date").val(start);
            $("#create_task_modal").modal("show");
        },
        select: function (info) {
            var startDate = moment(info.startStr).format(js_date_format);
            var endDate = moment(info.endStr)
                .subtract(1, "days")
                .format(js_date_format);

            $("#task_start_date").val(startDate);
            $("#task_end_date").val(endDate);
            $("#create_task_modal").modal("show");
        },
        eventDrop: function (info) {
            var id = info.event.id;

            // Show the confirmation modal
            $("#confirmDragTaskModal").modal("show");

            // Remove previous click event to avoid duplication
            $("#confirmDragTaskModal").off("click", "#confirm");

            // When the confirmation button is clicked
            $("#confirmDragTaskModal").on("click", "#confirm", function () {
                $("#confirmDragTaskModal")
                    .find("#confirm")
                    .html(label_please_wait)
                    .attr("disabled", true);

                // Format the start and end dates
                var start = moment(info.event.start).format(js_date_format);
                var end = moment(info.event.end)
                    .subtract(1, "days")
                    .format(js_date_format); // Subtracting one day

                // Handle case where the end date is invalid
                if (end === "Invalid date") {
                    end = start;
                }

                $.ajax({
                    url: baseUrl + "/tasks/update-dates",
                    type: "patch",
                    headers: {
                        "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
                    },
                    data: {
                        id: id,
                        start_date: start,
                        due_date: end,
                    },
                    success: function (response) {
                        $("#confirmDragTaskModal")
                            .find("#confirm")
                            .html(label_yes)
                            .attr("disabled", false);
                        if (response.error == false) {
                            $("#confirmDragTaskModal").modal("hide");
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle error
                        $("#confirmDragTaskModal")
                            .find("#confirm")
                            .html(label_yes)
                            .attr("disabled", false);
                        $("#confirmDragTaskModal").modal("hide");
                        toastr.error(label_something_went_wrong);
                    },
                });
            });

            // Handle cancel event
            $("#confirmDragTaskModal").on("click", "#cancel", function () {
                info.revert(); // Revert the event to its original position
                $("#confirmDragTaskModal").modal("hide");
            });
        },
        eventResize: function (info) {
            var id = info.event.id;

            // Show confirmation modal for resizing
            $("#confirmResizeTaskModal").modal("show");

            $("#confirmResizeTaskModal").off("click", "#confirm");
            $("#confirmResizeTaskModal").on("click", "#confirm", function () {
                $("#confirmResizeTaskModal")
                    .find("#confirm")
                    .html(label_please_wait)
                    .attr("disabled", true);

                // Format the new start and end dates
                var start = moment(info.event.start).format(js_date_format);
                var end = moment(info.event.end)
                    .subtract(1, "days")
                    .format(js_date_format); // Subtracting one day

                $.ajax({
                    url: baseUrl + "/tasks/update-dates",
                    type: "PATCH",
                    headers: {
                        "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
                    },
                    data: {
                        id: id,
                        start_date: start,
                        due_date: end,
                    },
                    success: function (response) {
                        $("#confirmResizeTaskModal")
                            .find("#confirm")
                            .html(label_yes)
                            .attr("disabled", false);
                        if (response.error == false) {
                            $("#confirmResizeTaskModal").modal("hide");
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        $("#confirmResizeTaskModal")
                            .find("#confirm")
                            .html(label_yes)
                            .attr("disabled", false);
                        $("#confirmResizeTaskModal").modal("hide");
                        toastr.error(label_something_went_wrong);
                    },
                });
            });

            // Handle cancellation
            $("#confirmResizeTaskModal").on("click", "#cancel", function () {
                info.revert(); // Revert the event to its original position
                $("#confirmResizeTaskModal").modal("hide");
            });
        },
        eventMouseEnter: function (info) {
            // Create a tooltip element
            var tooltip = document.createElement("div");
            tooltip.innerHTML = info.event.title;
            tooltip.style.position = "absolute";
            tooltip.style.background = "rgba(0, 0, 0, 0.8)";
            tooltip.style.color = "#fff";
            tooltip.style.padding = "5px";
            tooltip.style.borderRadius = "5px";
            tooltip.style.zIndex = "1000";
            tooltip.style.pointerEvents = "none"; // Prevent mouse events

            // Append the tooltip to the body
            document.body.appendChild(tooltip);

            // Position the tooltip
            var rect = info.el.getBoundingClientRect();
            tooltip.style.left = rect.left + window.scrollX + "px";
            tooltip.style.top = rect.bottom + window.scrollY + "px";

            // Remove tooltip on mouse leave
            info.el.addEventListener(
                "mouseleave",
                function () {
                    document.body.removeChild(tooltip);
                },
                { once: true }
            );
        },
    });
    taskcalendar.render();
}
function fetchTasks(startDate, endDate, successCallback, failureCallback) {
    var projectId = $("#projectId").val();
    $.ajax({
        url: baseUrl + "/tasks/get-calendar-data",
        type: "GET",
        data: {
            start: startDate.toISOString(),
            end: endDate.toISOString(),
            projectId: projectId,
            is_favorites: $("#is_favorites").val(),
        },
        success: function (response) {
            // Parse and format dynamic data for FullCalendar
            var events = response.map(function (event) {
                return {
                    id: event.id,
                    tasks_info_url: event.tasks_info_url,
                    title: event.title,
                    start: event.start,
                    end: moment(event.end).add(1, "days").format("YYYY-MM-DD"),
                    backgroundColor: event.backgroundColor,
                    borderColor: event.borderColor,
                    textColor: event.textColor,
                };
            });
            // Invoke success callback with dynamic data
            successCallback(events);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            // Invoke failure callback if there's an error
            failureCallback(error);
        },
    });
}

$(document).ready(function () {
    $("#menu-search").on("input", function () {
        var searchQuery = $(this).val().toLowerCase();
        var menuItems = $(".menu-item");

        if (searchQuery === "") {
            // If search is empty, reset everything
            menuItems.show(); // Show all menu items
            $(".menu-inner > li").removeClass("open"); // Remove 'open' class from all main menus
            return; // Exit the function
        }

        menuItems.each(function () {
            var item = $(this);
            var itemText = item.text().toLowerCase();
            var parentMenu = item.closest("li"); // Get the parent <li>

            // Check if the item text or any submenu text matches the query
            if (itemText.includes(searchQuery)) {
                item.show(); // Show the menu item
                if (item.closest("ul").hasClass("menu-sub")) {
                    // If it's a submenu and its parent menu has submenus, add 'open'
                    parentMenu
                        .closest(".menu-inner > li:has(.menu-sub)")
                        .addClass("open");
                }
            } else {
                item.hide(); // Hide the menu item
            }
        });

        // Loop through parent menus with submenus and open them if any of their submenus are visible
        $(".menu-inner > li:has(.menu-sub)").each(function () {
            var parentMenu = $(this);
            var submenus = parentMenu.find(".menu-sub");
            var anyVisible = submenus.find(".menu-item:visible").length > 0;

            if (anyVisible) {
                parentMenu.addClass("open"); // Add 'open' to parent if any submenu is visible
            } else {
                parentMenu.removeClass("open"); // Remove 'open' if no submenu is visible
            }
        });
    });
});

function addClearButtonFunctionality() {
    $(".custom-search-input").each(function () {
        const $inputField = $(this); // Current input field

        // Listen for input changes on the input field
        $inputField.on("input", function () {
            var searchQuery = $(this).val().toLowerCase();

            // Check if the search query is not empty
            if (searchQuery) {
                // Add the clear button if it doesn't already exist
                if ($inputField.siblings(".clear-search").length === 0) {
                    // Create and append the clear button
                    const clearButtonHtml =
                        '<span class="input-group-text cursor-pointer clear-search"><i class="bx bx-x"></i></span>';
                    $inputField.after(clearButtonHtml);
                }
            } else {
                // Remove the clear button if the input is empty
                $inputField.siblings(".clear-search").remove();
            }
        });
    });

    // Clear button click functionality
    $(document).on("click", ".clear-search", function () {
        const $inputField = $(this).prev(".custom-search-input"); // Get the associated input field
        $inputField.val("").trigger("input"); // Clear the input and trigger the input event
        $(this).remove(); // Remove the clear button
    });
}

$(document).ready(function () {
    // Apply clear button functionality to all search inputs
    addClearButtonFunctionality();
});

// Mention in the text area
function initializeMentionTextarea($textarea) {
    // Extract mention id and type from the data attributes of the textarea
    const mentionID = $textarea.data("mention-id");
    const mentionType = $textarea.data("mention-type");
    // Check if the textarea element exists
    if ($textarea.length === 0) {
        console.error("Textarea not found.");
        return;
    }
    // Initialize Tribute.js with the provided textarea
    const tribute = new Tribute({
        values: function (text, cb) {
            // Fetch users based on the search term and mention info
            $.ajax({
                url: baseUrl + "/users/get-mentions",
                method: "GET",
                data: {
                    search: text,
                    mention_id: mentionID,
                    mention_type: mentionType,
                },
                success: function (response) {
                    const mappedUsers = response.map((user) => ({
                        key: user.id, // Use 'id' as key
                        value: user.first_name + " " + user.last_name,
                    }));
                    cb(mappedUsers); // Provide the data to Tribute.js callback
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching users:", error);
                },
            });
        },
        selectTemplate: function (item) {
            return `@${item.original.value}`; // What gets inserted when selected
        },
        lookup: "value", // Attribute used for lookup
        menuItemTemplate: function (item) {
            return `${item.original.value}`; // How items appear in the dropdown
        },
    });
    // Attach Tribute.js to the textarea
    tribute.attach($textarea[0]);
}
function stripHtml(content) {
    // Replace <a> tags with the inner text, but only add '@' if the inner text doesn't already start with it
    return content.replace(
        /<a [^>]*class=["'][^"']*mention[^"']*["'][^>]*>([^<]+)<\/a>/g,
        function (match, innerText) {
            // Check if the innerText already starts with @
            return innerText.startsWith("@") ? innerText : "@" + innerText;
        }
    );
}
// Recuring Task Settings
$(document).ready(function () {
    // Toggle Recurring Task Settings Visibility
    $("#recurring-task-switch").on("change", function () {
        const isChecked = $(this).is(":checked");
        $("#recurring-task-settings").toggleClass("d-none", !isChecked);

        // Toggle required attributes based on switch state
        $(
            "#recurrence-frequency, #recurrence-starts-from, #recurrence-occurrences"
        ).prop("required", isChecked);

        // Trigger change event to update dependent fields
        if (isChecked) {
            $("#recurrence-frequency").trigger("change");
        }
    });

    // Dynamic Display Based on Recurrence Frequency Type
    $("#recurrence-frequency").on("change", function () {
        const value = $(this).val();

        // Hide all frequency-specific groups
        $(
            "#recurrence-day-of-week-group, #recurrence-day-of-month-group, #recurrence-month-of-year-group"
        ).addClass("d-none");

        // Show appropriate groups based on selected frequency
        switch (value) {
            case "weekly":
                $("#recurrence-day-of-week-group").removeClass("d-none");
                break;
            case "monthly":
                $("#recurrence-day-of-month-group").removeClass("d-none");
                break;
            case "yearly":
                $(
                    "#recurrence-day-of-month-group, #recurrence-month-of-year-group"
                ).removeClass("d-none");
                break;
        }
    });

    // Initialize Settings on Page Load
    function initializeSettings() {
        const isRecurring = $("#recurring-task-switch").is(":checked");

        // Toggle visibility of recurring task settings
        $("#recurring-task-settings").toggleClass("d-none", !isRecurring);

        // If recurring is enabled, show appropriate fields based on frequency
        if (isRecurring) {
            $("#recurrence-frequency").trigger("change");
        }
    }

    // Run initialization on page load
    initializeSettings();
});

//Edit Recuring Task Settings
$(document).ready(function () {
    // Toggle Recurring Task Settings Visibility
    $("#edit-recurring-task-switch").on("change", function () {
        const isChecked = $(this).is(":checked");
        $("#edit-recurring-task-settings").toggleClass("d-none", !isChecked);

        // Toggle required attributes based on switch state
        $(
            "#edit-recurrence-frequency, #edit-recurrence-starts-from, #edit-recurrence-occurrences"
        ).prop("required", isChecked);

        // Trigger change event to update dependent fields
        if (isChecked) {
            $("#edit-recurrence-frequency").trigger("change");
        }
    });

    // Dynamic Display Based on Recurrence Frequency Type
    $("#edit-recurrence-frequency").on("change", function () {
        const value = $(this).val();

        // Hide all frequency-specific groups
        $(
            "#edit-recurrence-day-of-week-group, #edit-recurrence-day-of-month-group, #edit-recurrence-month-of-year-group"
        ).addClass("d-none");

        // Show appropriate groups based on selected frequency
        switch (value) {
            case "weekly":
                $("#edit-recurrence-day-of-week-group").removeClass("d-none");
                break;
            case "monthly":
                $("#edit-recurrence-day-of-month-group").removeClass("d-none");
                break;
            case "yearly":
                $(
                    "#edit-recurrence-day-of-month-group, #edit-recurrence-month-of-year-group"
                ).removeClass("d-none");
                break;
        }
    });

    // Initialize Settings on Page Load
    function initializeSettings() {
        const isRecurring = $("#edit-recurring-task-switch").is(":checked");

        // Toggle visibility of recurring task settings
        $("#edit-recurring-task-settings").toggleClass("d-none", !isRecurring);

        // If recurring is enabled, show appropriate fields based on frequency
        if (isRecurring) {
            $("#edit-recurrence-frequency").trigger("change");
        }
    }

    // Run initialization on page load
    initializeSettings();
});

// Task Reminder Settings

$(document).ready(function () {
    // Toggle Reminder Settings Visibility
    $("#reminder-switch").on("change", function () {
        if ($(this).is(":checked")) {
            $("#reminder-settings").removeClass("d-none");
            $("#time-of-day").prop("required", true);
        } else {
            $("#reminder-settings").addClass("d-none");
            $("#time-of-day").prop("required", false);
        }
    });

    // Dynamic Display Based on Frequency Type
    $("#frequency-type").on("change", function () {
        const value = $(this).val();
        $("#day-of-week-group").addClass("d-none");
        $("#day-of-month-group").addClass("d-none");

        if (value === "weekly") {
            $("#day-of-week-group").removeClass("d-none");
        } else if (value === "monthly") {
            $("#day-of-month-group").removeClass("d-none");
        }
    });

    $("#edit-reminder-switch").on("change", function () {
        if ($(this).is(":checked")) {
            $("#edit-reminder-settings").removeClass("d-none");
        } else {
            $("#edit-reminder-settings").addClass("d-none");
        }
    });
    $("#edit-frequency-type").on("change", function () {
        const value = $(this).val();
        $("#edit-day-of-week-group").addClass("d-none");
        $("#edit-day-of-month-group").addClass("d-none");

        if (value === "weekly") {
            $("#edit-day-of-week-group").removeClass("d-none");
        } else if (value === "monthly") {
            $("#edit-day-of-month-group").removeClass("d-none");
        }
    });
});
