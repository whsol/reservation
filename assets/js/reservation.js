!function( $ ) {

    $('#showAvailableDates').datepicker({
        inline: true,
        startDate: WhSol.Reservation.startDate,
        endDate: WhSol.Reservation.endDate,
        maxViewMode: 0,
        language: WhSol.Reservation.locale,
        weekStart: WhSol.Reservation.first_day,
        beforeShowDay: function (date) {

            var theday = date.getDate();
            /* index of months are based on 0, so we need to add 1 to the month for matching correctly */
            var themonth = date.getMonth() + 1;
            var theyear = date.getFullYear();

            // ensure date is corresponding, for days, months and years
            if (WhSol.Reservation.disableDays[theyear] && WhSol.Reservation.disableDays[theyear][themonth]){
                if ($.inArray(theday, WhSol.Reservation.disableDays[theyear][themonth]) != -1) {
                    return {enabled:false, classes:'bg-warning'};
                }
            }
        }
    }).on('changeDate', function (e) {
        if (e.dates.length != 0) {
            $('#workingTimeSelect').prop( "disabled", true );
            $.request('reservationForm::onChangeDate', {
                data: {date: e.format()},
                update: {'reservationForm::workingTimes': '#workingTimes'},
                complete: function() {
                    $('#reservSubmit').prop( "disabled", true );
                }
            });
            $('#reservDate').val(e.format());
        }
    });

    $('#reservForm').submit(function(event){
        $('#reservForm').request('onDoReserv', {
            update: {
                'reservationForm::complete': '#reservContainer'
            },
            success: function(data){
                if (data.success == true) {
                    $(this.options.update['reservationForm::complete']).html(data['reservationForm::complete']);
                }
                else {
                    alert(data.message);
                }
                return false;
            }
        })
        event.preventDefault();
    });

    $('#workingTimes').on('change', 'select', function(e) {
        if ($(this).val() != '') {
            $('#reservSubmit').prop( "disabled", false );
        }
        else {
            $('#reservSubmit').prop( "disabled", true );
        }
    });
}( window.jQuery );