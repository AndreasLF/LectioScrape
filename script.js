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
                themeSystem: 'bootstrap4',
                displayEventTime: true,
                weekNumbers: true,
                timeFormat: 'H(:mm)',
                slotEventOverlap: false,
                eventOverlap: false,
                eventClick: function(event, jsEvent, view) {
                    $('#eventModalTitle').html(event.title);
                    $('#eventModalBody').html(event.description);
                    $('#eventModalLectioButtonLink').prop("href",event.lessonURL);
                    $('#fullCalendarModal').modal();
                },
                firstDay: 1
            })
    
    
            $("#googleButton").click(function(){
                
                //Gets the current view from the calendar
                var view = $('#calendar').fullCalendar('getView'); 
                
                //Formats the start date in the view with Moment
                var startDate = view.start.format();
                
                //Formats the end date in the view with Moment
                var endDate = view.end.format();
                
                //Open uploadToGoogleCal.php and pass startDate and endDate via $_GET
                window.location.replace("setDatesSession.php?startDate=" + startDate + "&endDate=" + endDate );

            });
    
            $("#databaseButton").click(function(){
                
                //Gets the current view from the calendar
                var view = $('#calendar').fullCalendar('getView'); 
                
                //Formats the start date in the view with Moment
                var startDate = view.start.format();
                
                //Formats the end date in the view with Moment
                var endDate = view.end.format();
                
                //Open sendScheduleToDatabase.php and pass startDate and endDate via $_GET
                window.location.replace("sendScheduleToDatabase.php?startDate=" + startDate + "&endDate=" + endDate );

            });
    

        });



