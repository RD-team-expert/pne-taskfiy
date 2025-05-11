<script>
    var label_please_wait = <?= json_encode(get_label('please_wait', 'Please wait...')) ?>;
    var label_please_select_records_to_delete = <?= json_encode(get_label('please_select_records_to_delete', 'Please select records to delete.')) ?>;
    var label_something_went_wrong = <?= json_encode(get_label('something_went_wrong', 'Something went wrong.')) ?>;
    var label_please_correct_errors = <?= json_encode(get_label('please_correct_errors', 'Please correct errors.')) ?>;
    var label_removed_from_favorite_successfully = <?= json_encode(get_label('removed_from_favorite_successfully', 'Removed from favorites successfully.')) ?>;
    var label_marked_as_favorite_successfully = <?= json_encode(get_label('marked_as_favorite_successfully', 'Marked as favorite successfully.')) ?>;
    var label_yes = <?= json_encode(get_label('yes', 'Yes')) ?>;
    var label_upload = <?= json_encode(get_label('upload', 'Upload')) ?>;
    var decimal_points = <?= intval($general_settings['decimal_points_in_currency'] ?? '2') ?>;
    var label_update = <?= json_encode(get_label('update', 'Update')) ?>;
    var label_delete = <?= json_encode(get_label('delete', 'Delete')) ?>;
    var label_view = <?= json_encode(get_label('view', 'View')) ?>;
    var label_not_assigned = <?= json_encode(get_label('not_assigned', 'Not assigned')) ?>;
    var label_delete_selected = <?= json_encode(get_label('delete_selected', 'Delete selected')) ?>;
    var label_search = <?= json_encode(get_label('search', 'Search')) ?>;
    var label_create = <?= json_encode(get_label('create', 'Create')) ?>;
    var label_users_associated_with_project = <?= json_encode(get_label('users_associated_with_project', 'Users associated with project')) ?>;
    var label_update_task = <?= json_encode(get_label('update_task', 'Update Task')) ?>;
    var label_quick_view = <?= json_encode(get_label('quick_view', 'Quick View')) ?>;
    var label_project = <?= json_encode(get_label('project', 'Project')) ?>;
    var label_task = <?= json_encode(get_label('task', 'Task')) ?>;
    var label_projects = <?= json_encode(get_label('projects', 'Projects')) ?>;
    var label_tasks = <?= json_encode(get_label('tasks', 'Tasks')) ?>;
    var label_clear_filters = <?= json_encode(get_label('clear_filters', 'Clear Filters')) ?>;
    var label_set_as_default_view = <?= json_encode(get_label('set_as_default_view', 'Set as Default View')) ?>;
    var label_default_view = <?= json_encode(get_label('default_view', 'Default View')) ?>;
    var label_save_column_visibility = <?= json_encode(get_label('save_column_visibility', 'Save Column Visibility')) ?>;
    var label_showing = <?= json_encode(get_label('showing', 'Showing')) ?>;
    var label_to_for_pagination = <?= json_encode(get_label('to_for_pagination', 'to')) ?>;
    var label_of = <?= json_encode(get_label('of', 'of')) ?>;
    var label_rows = <?= json_encode(get_label('rows', 'rows')) ?>;
    var label_rows_per_page = <?= json_encode(get_label('rows_per_page', 'rows per page')) ?>;
    var label_select = <?= json_encode(get_label('select', 'Select')) ?>;
    var label_or = <?= json_encode(get_label('or', 'or')) ?>;
    var label_drag_and_drop_files_here = <?= json_encode(get_label('drag_and_drop_files_here', 'Drag & Drop Files Here')) ?>;
    var label_drag_and_drop_update_zip_file_here = <?= json_encode(get_label('drag_and_drop_update_zip_file_here', 'Drag & Drop Update from vX.X.X to vX.X.X.zip file Here')) ?>;
    var label_only_one_file_can_be_uploaded_at_a_time = <?= json_encode(get_label('only_one_file_can_be_uploaded_at_a_time', 'Only 1 file can be uploaded at a time')) ?>;
    var label_please_enter_name = <?= json_encode(get_label('please_enter_name', 'Please enter name')) ?>;
    var label_update_the_system = <?= json_encode(get_label('update_the_system', 'Update the system')) ?>;
    var label_sending = <?= json_encode(get_label('sending', 'Sending...')) ?>;
    var label_submit = <?= json_encode(get_label('submit', 'Submit')) ?>;
    var label_allowed_max_upload_size = <?= json_encode(get_label('allowed_max_upload_size', 'Allowed max upload size')) ?>;
    var label_currency_restriction = <?= json_encode(get_label('currency_restriction', 'Only digits, commas as thousand separators, and a single decimal point are allowed.')) ?>;
    var label_currency_restriction_1 = <?= json_encode(get_label('currency_restriction_1', 'Only one decimal point is allowed.')) ?>;
    var label_currency_restriction_2 = <?= json_encode(get_label('currency_restriction_2', 'Only digits and a single decimal point are allowed.')) ?>;
    var label_invoice_id_prefix = <?= json_encode(get_label('invoice_id_prefix', 'INVC-')) ?>;
    var label_please_type_at_least_1_character = <?= json_encode(get_label('please_type_at_least_1_character', 'Please type at least 1 character')) ?>;
    var label_searching = <?= json_encode(get_label('searching', 'Searching...')) ?>;
    var label_no_results_found = <?= json_encode(get_label('no_results_found', 'No results found')) ?>;
    var label_max_files_allowed = <?= json_encode(get_label('max_files_allowed', 'Max Files Allowed')) ?>;
    var label_allowed_file_types = <?= json_encode(get_label('allowed_file_types', 'Allowed File Types')) ?>;
    var label_no_files_chosen = <?= json_encode(get_label('no_files_chosen', 'No file(s) chosen.')) ?>;
    var label_max_files_count_allowed = <?= json_encode(get_label('max_files_count_allowed', 'You can only upload :count file(s).')) ?>;
    var label_file_type_not_allowed = <?= json_encode(get_label('file_type_not_allowed', 'File type not allowed')) ?>;
    var label_mm_export_success = <?= json_encode(get_label('mm_export_success', 'Mind map exported successfully!')) ?>;
    var label_mm_export_failed = <?= json_encode(get_label('mm_export_failed', 'Failed to export mind map. Please try again.')) ?>;
    var label_no_projects_available = <?= json_encode(get_label('no_projects_available', 'No Projects Available')) ?>;
    var label_change_date_not_allowed = <?= json_encode(get_label('change_date_not_allowed', 'Change date is not allowed for this.')) ?>;
    var label_no_data_available = <?= json_encode(get_label('no_data_available', 'No Data Available')) ?>;
    var label_to = <?= json_encode(get_label('to', 'To')) ?>;
    var label_income = <?= json_encode(get_label('income', 'Income')) ?>;
    var label_expense = <?= json_encode(get_label('expense', 'Expense')) ?>;
    var label_amount = <?= json_encode(get_label('amount', 'Amount')) ?>;
    var label_total = <?= json_encode(get_label('total', 'Total')) ?>;
    var label_replies = <?= json_encode(get_label('replies', 'Replies')) ?>;
    var label_jump_to_comment = <?= json_encode(get_label('jump_to_comment', 'Jump to Comment')) ?>;
    var label_edit = <?= json_encode(get_label('edit', 'Edit')) ?>;
    var label_reply = <?= json_encode(get_label('reply', 'Reply')) ?>;
    var label_download = <?= json_encode(get_label('download', 'Download')) ?>;
    var label_preview_not_available = <?= json_encode(get_label('preview_not_available', 'Preview not available')) ?>;
    var label_err_try_again = <?= json_encode(get_label('err_try_again', 'An error occurred. Please try again.')) ?>;    
    var label_pinned_successfully = <?= json_encode(get_label('pinned_successfully', 'Pinned Successfully.')) ?>;
    var label_unpinned_successfully = <?= json_encode(get_label('unpinned_successfully', 'Unpinned Successfully.')) ?>;
    var label_click_pin = <?= json_encode(get_label('click_pin', 'Click to Pin')) ?>;
    var label_click_unpin = <?= json_encode(get_label('click_unpin', 'Click to Unpin')) ?>;
    var add_favorite = <?= json_encode(get_label('add_favorite', 'Click to mark as favorite')) ?>;
    var remove_favorite = <?= json_encode(get_label('remove_favorite', 'Click to remove from favorite')) ?>;   
    var label_drag_and_drop_file_here = <?= json_encode(get_label('drag_and_drop_file_here', 'Drag & Drop File Here')) ?>; 
    var label_no_file_chosen = <?= json_encode(get_label('no_file_chosen', 'No file chosen.')) ?>;
</script>
<script>
    function addDebouncedEventListener(selector, event, handler, delay = 300) {
        const debounce = (func, delay) => {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        };

        $(selector).on(event, debounce(handler, delay));
    }
</script>