function loginActivities(formData) {
    $("#attachPanels").empty().html('<div class="col text-center"><div class="spinner-border" role="status" aria-hidden="true"></div> Loading...</div>');
    $.ajax({
        type: 'GET',
        url: timeTrackerBaseURL + 'php/activity.php',
        data: formData,
        success: function(values) {
            var data = JSON.parse(values);
            $("#attachPanels").empty();

            for (x in data) {
                var cardHeader = $('<div class="card-header" />');
                var cardHeaderRow = $('<div class="row pt-2" />');
                cardHeaderRow.append('<div class="col-6 text-left"><span class="vertical-line"></span>' + data[x].start_time + '</div>');
                var stopCol = $('<div class="col-6 text-right" />');
                stopCol.append('<i class="far fa-clock"></i> ' + data[x].end_time);
                cardHeaderRow.append(stopCol);
                cardHeader.append(cardHeaderRow);
                
                var cardInner = $("<div class='card card-style-1' />");
                cardInner.append(cardHeader);

                var cardBody = $("<div class='card-body' />");
                cardBody.append(data[x].task_name);
                cardInner.append(cardBody);

                var cardFooter = $("<div class='card-footer' />");
                cardFooter.append("<i class='fab fa-twitter'></i> " + data[x].name);
                cardInner.append(cardFooter);

                var cardCol = $("<div class='col-lg-6 mb-4' />");
                cardCol.append(cardInner);

                $("#attachPanels").append(cardCol);
            }
        }
    });
}


$(document).ready(function() {

    if ($("#attachPanels").length > 0) {
        loginActivities({ 'type': 'login' });
    }
});