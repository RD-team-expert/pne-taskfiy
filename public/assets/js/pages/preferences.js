'use strict';
$(document).ready(function () {
    var $sortable = $('#sortable-menu');

    // Initialize main menu sortable
    Sortable.create($sortable[0], {
        animation: 150,
        handle: '.handle'
    });

    // Initialize submenu sortable
    $('.submenu').each(function () {
        var $submenu = $(this);
        Sortable.create($submenu[0], {
            animation: 150,
            handle: '.handle'
        });
    });

    // Handle form submission
    $('#menu-order-form').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission
        var $submitButton = $('#btnSaveMenuOrder');
        $submitButton.attr('disabled', true).html(label_please_wait);
        var menuOrder = [];
        $('#sortable-menu li').each(function () {
            var menuId = $(this).data('id');
            var submenus = [];

            // Check if there are submenus
            $(this).find('.submenu li').each(function () {
                submenus.push({ id: $(this).data('id') });
            });

            menuOrder.push({ id: menuId, submenus: submenus });
        });

        // Send the sorted IDs to your backend via AJAX
        $.ajax({
            url: baseUrl + '/save-menu-order',
            method: 'POST',
            data: {
                menu_order: menuOrder
            },
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
            },
            success: function (response) {
                if (response.error == false) {
                    toastr.success(response['message']);
                    setTimeout(function () {
                        location.reload();
                    }, parseFloat(toastTimeOut) * 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error(label_something_went_wrong);
            },
            complete: function () {
                $submitButton.attr('disabled', false).html(label_update);
            }
        });
    });
});

$(document).on('click', '#btnResetDefaultMenuOrder', function (e) {
    e.preventDefault();
    $('#confirmResetDefaultMenuOrderModal').modal('show'); // show the confirmation modal
    $('#confirmResetDefaultMenuOrderModal').off('click', '#btnconfirmResetDefaultMenuOrder');
    $('#confirmResetDefaultMenuOrderModal').on('click', '#btnconfirmResetDefaultMenuOrder', function (e) {
        $('#btnconfirmResetDefaultMenuOrder').html(label_please_wait).attr('disabled', true);
        $.ajax({
            url: baseUrl + '/reset-default-menu-order',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
            },
            success: function (response) {
                if (response.error == false) {
                    toastr.success(response['message']);
                    setTimeout(function () {
                        location.reload();
                    }, parseFloat(toastTimeOut) * 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (data) {
                toastr.error(label_something_went_wrong);
            },
            complete: function () {
                $('#confirmResetDefaultMenuOrderModal').modal('hide');
                $('#btnconfirmResetDefaultMenuOrder').attr('disabled', false).html(label_yes);
            }
        });
    });
});