/**
 * Dashboard Analytics
 */
'use strict';

(function () {
    let cardColor, headingColor, axisColor, shadeColor, borderColor;

    cardColor = config.colors.white;
    headingColor = config.colors.headingColor;
    axisColor = config.colors.axisColor;
    borderColor = config.colors.borderColor;

    // Projects Statistics Chart
    var options = {
        series: project_data,
        colors: bg_colors,
        labels: labels,
        chart: {
            type: 'donut',
            height: 300,
            width: 300,
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },

            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#projectStatisticsChart"), options);
    chart.render();

    // Tasks Statistics Chart

    var options = {
        labels: labels,
        series: task_data,
        colors: bg_colors,
        chart: {
            type: 'donut',
            height: 300,
            width: 300,
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },

            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#taskStatisticsChart"), options);
    chart.render();


    // Todos Statistics Chart
    var options = {
        labels: [done, pending],
        series: todo_data,
        colors: [config.colors.success, config.colors.danger],
        chart: {
            type: 'donut',
            height: 300,
            width: 300,
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },

            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#todoStatisticsChart"), options);
    chart.render();
})();

window.icons = {
    refresh: 'bx-refresh',
    toggleOn: 'bx-toggle-right',
    toggleOff: 'bx-toggle-left'
}

function loadingTemplate(message) {
    return '<i class="bx bx-loader-alt bx-spin bx-flip-vertical" ></i>'
}


function queryParamsUpcomingBirthdays(p) {
    return {
        "upcoming_days": $('#upcoming_days_bd').val(),
        "user_ids": $('#birthday_user_filter').val(),
        "client_ids": $('#birthday_client_filter').val(),
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

$('#upcoming_days_birthday_filter').on('click', function (e) {
    e.preventDefault();
    $('#birthdays_table').bootstrapTable('refresh');


})

addDebouncedEventListener('#birthday_user_filter, #birthday_client_filter', 'change', function (e, refreshTable) {    
    e.preventDefault();
    if (typeof refreshTable === 'undefined' || refreshTable) {
        $('#birthdays_table').bootstrapTable('refresh');
    }
});

$(document).on('click', '.clear-upcoming-bd-filters', function (e) {
    e.preventDefault();
    $('#upcoming_days_bd').val('');
    $('#birthday_user_filter').val('').trigger('change', [0]);
    $('#birthday_client_filter').val('').trigger('change', [0]);
    $('#birthdays_table').bootstrapTable('refresh');
})


function queryParamsUpcomingWa(p) {
    return {
        "upcoming_days": $('#upcoming_days_wa').val(),
        "user_ids": $('#wa_user_filter').val(),
        "client_ids": $('#wa_client_filter').val(),
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

$('#upcoming_days_wa_filter').on('click', function (e) {
    e.preventDefault();
    $('#wa_table').bootstrapTable('refresh');


})

addDebouncedEventListener('#wa_user_filter, #wa_client_filter', 'change', function (e, refreshTable) {
    e.preventDefault();
    if (typeof refreshTable === 'undefined' || refreshTable) {
        $('#wa_table').bootstrapTable('refresh');
    }
});


$(document).on('click', '.clear-upcoming-wa-filters', function (e) {
    e.preventDefault();
    $('#upcoming_days_wa').val('');
    $('#wa_user_filter, #wa_client_filter').val('').trigger('change', [0]);
    $('#wa_table').bootstrapTable('refresh');
})

function queryParamsMol(p) {
    return {
        "upcoming_days": $('#upcoming_days_mol').val(),
        "user_ids": $('#mol_user_filter').val(),
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

$('#upcoming_days_mol_filter').on('click', function (e) {
    e.preventDefault();
    $('#mol_table').bootstrapTable('refresh');

})

addDebouncedEventListener('#mol_user_filter', 'change', function (e, refreshTable) {
    e.preventDefault();
    if (typeof refreshTable === 'undefined' || refreshTable) {
        $('#mol_table').bootstrapTable('refresh');
    }
});

$(document).on('click', '.clear-upcoming-mol-filters', function (e) {
    e.preventDefault();
    $('#upcoming_days_mol').val('');
    $('#mol_user_filter').val('').trigger('change', [0]);
    $('#mol_table').bootstrapTable('refresh');
})

document.addEventListener("DOMContentLoaded", function () {
    let incomeExpenseChart = null; // Initialize the chart variable

    function getFilters() {
        // Get the values from hidden inputs
        var startDate = $('#filter_date_range_from').val();
        var endDate = $('#filter_date_range_to').val();

        // Check if the input values are not empty
        if (startDate && endDate) {
            return {
                start_date: startDate,
                end_date: endDate,
            };
        }

        // If dates are not set or input is empty, return null
        return {
            start_date: null,
            end_date: null,
        };
    }

    function updateIEChart() {
        $.ajax({
            type: "GET",
            url: baseUrl + "/reports/income-vs-expense-report-data",
            dataType: "JSON",
            data: getFilters(),
            success: function (response) {
                // Parse the income and expenses
                const income = parseNumericValue(response.total_income);
                const expenses = parseNumericValue(response.total_expenses);

                // Update the chart series
                if (incomeExpenseChart) {
                    incomeExpenseChart.updateSeries([{
                        name: label_income,
                        data: [income]
                    }, {
                        name: label_expense,
                        data: [expenses]
                    }]);

                    // Update subtitle if needed
                    incomeExpenseChart.updateOptions({
                        subtitle: {
                            text: response.date_label || '' // Update with a relevant label
                        }
                    });
                } else {
                    // Initial chart options and render
                    const options = {
                        series: [{
                            name: label_income,
                            data: [income]
                        }, {
                            name: label_expense,
                            data: [expenses]
                        }],
                        chart: {
                            type: 'bar',
                            height: 250
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true,
                                endingShape: 'rounded'
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val, opts) {
                                return currencySymbol + val.toLocaleString();
                            }
                        },
                        colors: ['#005B41', '#ED2B2A'], // Green for income, red for expenses
                        xaxis: {
                            categories: [label_total],
                            labels: {
                                formatter: function (val) {
                                    return currencySymbol + val.toLocaleString();
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: label_amount
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return window.currencySymbol + val.toLocaleString();
                                }
                            }
                        },
                        subtitle: {
                            text: response.date_label || '' // Set subtitle initially
                        }
                    };

                    // Initialize chart
                    incomeExpenseChart = new ApexCharts(document.querySelector("#income-expense-chart"), options);
                    incomeExpenseChart.render().catch(error => console.error("Chart Render Error:", error));
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error: ", status, error);
            }
        });
    }

    $('#filter_date_range').on('apply.daterangepicker', function (ev, picker) {
        // Set the values in hidden inputs
        $('#filter_date_range_from').val(picker.startDate.format('YYYY-MM-DD'));
        $('#filter_date_range_to').val(picker.endDate.format('YYYY-MM-DD'));
        updateIEChart(); // Update report when dates are applied
    });

    $('#filter_date_range').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        // Clear the hidden inputs
        $('#filter_date_range_from').val('');
        $('#filter_date_range_to').val('');
        picker.setStartDate(moment());
        picker.setEndDate(moment());
        picker.updateElement();
        updateIEChart(); // Update report when dates are cleared        
    });

    // Initial chart update
    updateIEChart();

    // Utility function to parse numeric values
    function parseNumericValue(value) {
        if (typeof value === 'number') {
            return value;
        }
        if (typeof value === 'string') {
            return parseFloat(value.replace(/[^0-9.]/g, '')) || 0;
        }
        return 0;
    }
});
