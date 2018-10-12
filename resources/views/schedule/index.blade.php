@extends('layouts.app')

@section('content')
    <div class="header bg-green pb-6 pt-5 pt-md-6">

    </div>

    <!-- Page content -->
    <div class="container-fluid mt--7">
        <div class="row mt-5">
            <div class="col-xl-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                {{--<h3 class="mb-0">Page visits</h3>--}}
                            </div>
                            <div class="col text-right">
                                <a href="#" class="btn btn-sm btn-primary">See all</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('schedule.modal')
@endsection


@section('script')
    <script>

        var selectedSchedule;
        var selectedDate;

        /*------------ reset form values and elements upon selecting a schedule --------------*/
        function resetElements() {
            document.getElementById("formAddSchedule").reset();
            document.getElementById("formUpdateSchedule").reset();
            $("#errorList").html('');
            $('.select2-selection__choice').remove();
        }
        /*------------------------------------------------------------------------------------*/

        /*------------------------ plot schedules in to calendar -----------------------------*/
        function refreshCalendar(data) {
            var eventData = setEvents(data);
            $('#calendar').fullCalendar('renderEvent', eventData, true);
        }
        
        function setEvents(data) {
            var fullname = data.first_name + ' ' + data.last_name;

            var eventData = {
                id: data.id,
                tsr_id: data.tsr_id,
                customer_code: data.customer_code,
                customer: data.customer,
                title: fullname + '\n' +
                       data.customer + '\n' +
                       '(' + formatAMPM(data.start_time) + '-' + formatAMPM(data.end_time) +  ')',
                start: data.date,
                fullname: fullname,
                start_time: data.start_time,
                end_time: data.end_time,
                remarks: data.remarks,
                backgroundColor: data.color,
                borderColor    : data.color,
                textColor: '#ffffff'
            };
            return eventData;
        }
        /*------------------------------------------------------------------------------------*/

        /*------------------------ store, update, delete -------------------------------------*/
        function storeSchedule(){
            var data = $('#formAddSchedule').serialize() + '&date=' + selectedDate;

            $.ajax({
                type:'POST',
                url:'{{ url('/schedules/store?') }}' + data,
                dataType: 'json',
                success: function(data){
                    /*---------- add event to calendar ------------*/
                    var i;
                    for (i = 0; i < data.length; i++) {
                        refreshCalendar(data[i]);
                    }
                    $('#calendar').fullCalendar('unselect');
                    $('#addScheduleModal').modal('hide');
                    /*------------------------------------------------*/
                    // console.log(data);
                },
                error: function(data){
                    var errors = $.parseJSON(data.responseText);
                    $("#errorList").html('');
                    $.each(errors.errors, function (key, val) {
                        $("#errorList").append('<li>' + val + '</li>');
                    });
                    // console.log(errors);
                }
            });
        }

        function updateSchedule() {
            var data = $('#formUpdateSchedule').serialize() + '&date=' + selectedDate;

            $.ajax({
                type:'PATCH',
                url:'{{ url('/schedules/update') }}/' + selectedSchedule.id + '?'  + data,
                dataType: 'json',
                success: function(data){
                    /*---------- update event to calendar ------------*/
                    var eventData = setEvents(data);
                    selectedSchedule.title = eventData.title;
                    selectedSchedule.backgroundColor = eventData.backgroundColor;
                    selectedSchedule.borderColor = eventData.borderColor;

                    $('#calendar').fullCalendar('updateEvent', selectedSchedule);
                    $('#calendar').fullCalendar('unselect');
                    $('#updateScheduleModal').modal('hide');
                    /*------------------------------------------------*/
                    // console.log(eventData);
                },
                error: function(data){
                    var errors = $.parseJSON(data.responseText);

                    $("#updateScheduleErrorList").html('');
                    $.each(errors.errors, function (key, val) {
                        $("#updateScheduleErrorList").append('<li>' + val + '</li>');
                    });
                    // console.log(errors);
                }
            });
        }
        
        function changeDateSchedule(event) {
            $.ajax({
                type:'PATCH',
                url:'{{ url('/schedules/change') }}/' + event.id,
                data: '_token={{ csrf_token() }}&date=' + event.start.format(),
                success: function(data){}
            });
        }

        function destroySchedule() {
            $.ajax({
                type:'DELETE',
                url:'{{ url('/schedules/destroy') }}/' + selectedSchedule.id,
                data: '_token={{ csrf_token() }}',
                success: function(data){
                    /*---------- delete event to calendar ------------*/
                    $('#calendar').fullCalendar('removeEvents', selectedSchedule.id);
                    $('#calendar').fullCalendar('unselect');
                    $('#updateScheduleModal').modal('hide');
                    /*------------------------------------------------*/
                    // console.log(data);
                }
            });
        }
        /*-----------------------------------------------------------------------------------*/

        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title'
                },
                defaultDate: '{{ Carbon::now() }}',
                selectable: true,
                selectHelper: true,

                /*-------------- click day for adding schedule ------------------*/
                dayClick: function(date, allDay, jsEvent, view) {
                    resetElements();
                    selectedDate = date.format();
                    $('#addScheduleModal').modal('show');
                },
                /*----------- click event to update & delete schedule -----------*/
                eventClick: function(calEvent, jsEvent, view) {
                    resetElements();
                    selectedSchedule = calEvent;
                    selectedDate = calEvent.start.format();

                    $('#tsr_id').val(calEvent.tsr_id);
                    $('#tsr_name').val(calEvent.fullname);

                    //set a value in select2
                    $('#customer_code').val(calEvent.customer_code);
                    $('#select2-customer_code-container').text(calEvent.customer);

                    $('#start_time').val(calEvent.start_time);
                    $('#end_time').val(calEvent.end_time);
                    $('#remarks').val(calEvent.remarks);

                    $('#updateScheduleModal').modal('show');
                },
                eventDrop: function(event, delta, revertFunc) {
                    
                    if (!confirm("Are you sure about this change?")) {
                        revertFunc();
                    }
                    else{
                        changeDateSchedule(event);
                    }
                },
                /*---------------------------------------------------------------*/
                editable: true,
                eventLimit: 3,
            });

            /*----------------- retrieve schedules -------------------------*/
            var schedules = JSON.parse('{!! json_encode($schedules) !!}');
            schedules.forEach(function(element) {
                refreshCalendar(element);
            });
            /*--------------------------------------------------------------*/
        });
    </script>
@endsection
