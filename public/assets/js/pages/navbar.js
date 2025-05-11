
'use strict';

$(document).ready(function () {
    $('#global-search').select2({
        placeholder: label_search,
        minimumInputLength: 1, // Search initiated on typing the first character
        dropdownCssClass: "custom-select2",
        ajax: {
            url: baseUrl + '/search', // Laravel route
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // Pass the search query
                };
            },
            processResults: function (data) {
                var formattedResults = [];

                for (var module in data.results) {
                    if (data.results.hasOwnProperty(module) && data.results[module].length > 0) {
                        formattedResults.push({
                            text: module.charAt(0).toUpperCase() + module.slice(1), // Capitalize the parent name
                            children: $.map(data.results[module], function (item) {
                                return {
                                    text: item.title,
                                    id: module.toLowerCase() + '/' + item.id,
                                    type: module.toLowerCase() // Add type to data attributes
                                };
                            })
                        });
                    }
                }

                return {
                    results: formattedResults
                };
            },
            cache: true
        },
        language: {
            inputTooShort: function () {
                return label_please_type_at_least_1_character;
            },
            searching: function () {
                return label_searching;
            },
            noResults: function () {
                return label_no_results_found;
            }
        }
    });

    $('#global-search').on('select2:select', function (e) {
        var data = e.params.data;
        var moduleType = data.type;

        var redirectUrl;

        // Customize redirection based on module type
        switch (moduleType) {
            case 'projects':
                redirectUrl = baseUrl + '/projects/information/' + data.id.split('/')[1];
                break;
            case 'tasks':
                redirectUrl = baseUrl + '/tasks/information/' + data.id.split('/')[1];
                break;
            case 'meetings':
                redirectUrl = baseUrl + '/meetings';
                break;
            case 'workspaces':
                redirectUrl = baseUrl + '/workspaces';
                break;
            case 'users':
                redirectUrl = baseUrl + '/users/profile/' + data.id.split('/')[1];
                break;
            case 'clients':
                redirectUrl = baseUrl + '/clients/profile/' + data.id.split('/')[1];
                break;
            case 'todos':
                redirectUrl = baseUrl + '/todos';
                break;
            case 'notes':
                redirectUrl = baseUrl + '/notes';
                break;
            default:
                redirectUrl = baseUrl + '/';
                break;
        }

        window.location.href = redirectUrl; // Redirect to customized URL
    });
});
