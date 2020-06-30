(function($){
    const span = $('#feature_timestamp');
    const originalContent = span.html();
    const editBtn = $('#edit_feature_timestamp');
    const fieldset = editBtn.siblings('#timestampdiv');
    const save = fieldset.find('.save-timestamp');
    const cancel = fieldset.find('.cancel-timestamp');
    const selectedMonth = fieldset.find('#mm');
    const selectedDay = fieldset.find('#jj');
    const selectedYear = fieldset.find('#aa');
    const selectedHour = fieldset.find('#hh');
    const selectedMinute = fieldset.find('#mn');
    const originalMonth = fieldset.find('#hidden_feature_mm');
    const originalDay = fieldset.find('#hidden_feature_jj');
    const originalYear = fieldset.find('#hidden_feature_aa');
    const originalHour = fieldset.find('#hidden_feature_hh');
    const originalMinute = fieldset.find('#hidden_feature_mn');
    const originalDate = new Date(originalYear.val(), originalMonth.val() - 1, originalDay.val(), originalHour.val(), originalMinute.val());

    save.click(event => {
        event.preventDefault();
        editBtn.show();
        fieldset.hide();
        const yy = selectedYear.val(), mm = selectedMonth.val(), dd = selectedDay.val(), hh = selectedHour.val(), mn = selectedMinute.val()
        const selectedDate = new Date(yy, mm - 1, dd, hh, mn);
        if (selectedDate.toUTCString() === originalDate.toUTCString()) {
            span.html(originalContent);
        } else {
            const monthText = fieldset.find( 'option[value="' + mm + '"]', '#mm' ).attr( 'data-text' );
            span.html(`Featured on: <b>${monthText} ${parseInt(dd, 10)}, ${yy} @ ${('00' + hh).slice(-2)}:${('00' + mn).slice(-2)}</b>`);
        }
    });

    cancel.click(event => {
        event.preventDefault();
        selectedMonth.val(originalMonth.val());
        selectedDay.val(originalDay.val());
        selectedYear.val(originalYear.val());
        selectedHour.val(originalHour.val());
        selectedMinute.val(originalMinute.val());
        editBtn.show();
        fieldset.hide();
        span.html(originalContent)
    });

    editBtn.click(event => {
        event.preventDefault();
        fieldset.show();
        editBtn.hide();
    });
})(jQuery);