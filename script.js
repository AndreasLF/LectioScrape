$(document).ready(function() {

            //When the page is ready the calendar will be initialized

            $('#calendar').fullCalendar({
                events: 'fullcalendarFeed.php',
                editable: false,
                header: {
                    left: 'prev',
                    center: '',
                    right: 'next'
                },
                defaultView: 'agendaWeek',
                minTime: '07:00:00',
                maxTime: '19:00:00',
                themeSystem: 'standard',
                displayEventTime: true,
                weekNumbers: true,
                timeFormat: 'H(:mm)',
                slotEventOverlap: false,
                eventOverlap: false,
                eventClick: function(event, jsEvent, view) {
                    $('#eventModalTitle').html(event.title);
                    $('#eventModalBody').html(event.description);
                    $('#fullCalendarModal').modal();
                },
                firstDay: 1
            })
    
    
        

        });



