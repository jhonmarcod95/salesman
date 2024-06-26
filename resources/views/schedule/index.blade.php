@extends('layouts.app')

@section('content')
    {{--<div id="loading"></div>--}}
    <div class="header bg-green pb-6 pt-5 pt-md-6">

    </div>

    <!-- Page content -->
    <div class="container-fluid mt--7">
        <div class="row mt-5">
            <div class="col-xl-12 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        {!! Form::open(['id' => 'formFilter']) !!}
                        <div class="row align-items-center">

                            <div class="col-md-3">
                                <label>Date</label>
                                <input id="month" type="month" class="form-control" onchange="setCalendarDate(this.value)" value="{{ Carbon::now()->format('Y-m') }}">
                            </div>

                            <div class="col-md-7">
                                <label>Sales Personnel</label>
                                {!! Form::select('tsrs[]', $tsrs, null, ['class' => 'sel2', 'multiple']) !!}
                            </div>

                            <div class="col-md-2">
                                <div class="col text-right">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="retrieveSchedules(document.getElementById('month').value)">Search</button>
                                </div>
                            </div>

                        </div>
                        {!! Form::close() !!}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col text-right">
                                <a href="/virtual-schedule-report" class="btn btn-sm btn-primary"> Virtual Schedule</a>
                                <a href="/missed_itineraries" class="btn btn-sm btn-warning"> Missed Itineraries</a>
                                <a href="/change_planned_schedules" class="btn btn-sm btn-info"> Change Schedules</a>
                            </div>
                        </div>
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

        /*------------------------- fetch customers to select elem -------------------------*/
        // function retrieveCustomers(selectId, classification) {
        //     $.ajax({
        //         type:'GET',
        //         url: 'schedule-customer/' + classification,
        //         dataType: 'json',
        //         success: function(data){
        //             var customers = $(selectId);
        //             customers.empty();
        //             $(selectId).empty();
        //
        //             for(let customer of data){
        //                 customers.append('<option value=' + customer.customer_code + '>' + customer.name + '</option>');
        //             }
        //             customers.change();
        //             customers.select2();
        //         }
        //     });
        // }
        /*------------------------------------------------------------------------------------*/

        /*-------------------------- reset modal and its contents ----------------------------*/
        function resetModal() {
            document.getElementById("formAddSchedule").reset();
            document.getElementById("formUpdateSchedule").reset();

            $('#btn_save').prop('disabled', false);
            $('#btn_save_changes').prop('disabled', false);
            $('#btn_delete').prop('disabled', false);

            $("#add_mapping_event_schedule").hide();
            $("#add_customer_schedule").hide();

            $("#update_mapping_event_schedule").hide();
            $("#update_customer_schedule").hide();

            $("#errorList").html('');
            $("#updateScheduleErrorList").html('');

            $('.addScheduleModalSel2').val(null).trigger('change');
        }
        /*------------------------------------------------------------------------------------*/

        /*------------------------ plot schedules in to calendar -----------------------------*/
        function refreshCalendar(data) {
            var eventData = setEvents(data);
            $('#calendar').fullCalendar('renderEvent', eventData, true);
        }

        function rendered(endTime, startTime) {
            if(endTime && startTime){
                var startTime = moment(startTime, "HH:mm:ss a");
                var endTime = moment(endTime, "HH:mm:ss a");
                var duration = moment.duration(endTime.diff(startTime));
                var hours = parseInt(duration.asHours());
                var minutes = parseInt(duration.asMinutes())%60;
                return hours + 'h '+ minutes+' min.';
            }else{
                return "";
            }
        }


        function setEvents(data) {

            var fullname = data.full_name;

            var time = rendered(data.end_time,data.start_time);

            var eventData = {
                id: data.id,
                user_id: data.user_id,
                type: data.type,
                code: data.code,
                name: data.name,
                address: data.address,
                title: fullname + '\n' +
                       data.name + '\n' +
                       '(' + time +  ')',
                start: data.date,
                fullname: fullname,
                start_time: data.start_time,
                end_time: data.end_time,
                remarks: data.remarks,
                lat: data.lat,
                lng: data.lng,
                km_distance: data.km_distance,
                backgroundColor: data.color,
                borderColor    : data.color,
                textColor: '#ffffff',
                status: data.status
            };
            return eventData;
        }
        /*------------------------------------------------------------------------------------*/

        /*-------------------- retrieve store, update, delete --------------------------------*/
        function retrieveSchedules(date) {
            var date = new Date(date);
            var dateFrom = formatDate(new Date(date.getFullYear(), date.getMonth(), 1));
            var dateTo = formatDate(new Date(date.getFullYear(), date.getMonth() + 1, 0));
            var filterQuery = $('#formFilter').serialize();

            $("#loading").show();
            $.ajax({
                type:'GET',
                url:'/schedules/' + dateFrom + '/' + dateTo + '?' + filterQuery,
                dataType: 'json',
                success: function(data){
                    //retrieve schedules
                    $('#calendar').fullCalendar('removeEvents');
                    data.forEach(function(element) {
                        refreshCalendar(element);
                    });
                    $("#loading").hide();
                    // console.log(data);
                }
            });

        }

        function storeSchedule(){

            var data = $('#formAddSchedule').serialize() + '&date=' + selectedDate;

            $('#btn_save').prop('disabled', true);

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
                    $("#loading").hide();
                    /*------------------------------------------------*/
                },
                error: function(data){
                    var errors = $.parseJSON(data.responseText);
                    $("#errorList").html(showErrorAlert(data));
                    $('#btn_save').prop('disabled', false);
                    // console.log(errors);
                }
            });
        }

        function updateSchedule() {
            var data = $('#formUpdateSchedule').serialize() + '&date=' + selectedDate;

            $('#btn_save_change').prop('disabled', true);

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
                    selectedSchedule.code = eventData.code;
                    selectedSchedule.type = eventData.type;
                    selectedSchedule.name = eventData.name;
                    selectedSchedule.address = eventData.address;
                    selectedSchedule.start_time = eventData.start_time;
                    selectedSchedule.end_time = eventData.end_time;
                    selectedSchedule.remarks = eventData.remarks;
                    selectedSchedule.lat = eventData.lat;
                    selectedSchedule.lng = eventData.lng;
                    selectedSchedule.km_distance = eventData.km_distance;


                    $('#calendar').fullCalendar('updateEvent', selectedSchedule);
                    $('#calendar').fullCalendar('unselect');
                    $('#updateScheduleModal').modal('hide');
                    /*------------------------------------------------*/
                    // console.log(eventData);
                },
                error: function(data){
                    $("#updateScheduleErrorList").html(showErrorAlert(data));
                    $('#btn_save_change').prop('disabled', false);
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
                ,
                error: function(data){
                    alert('Change not successful.');

                }
            });
        }

        function destroySchedule() {

            $('#btn_delete').prop('disabled', true);

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
                },
                error: function(data){
                    $("#updateScheduleErrorList").html(showErrorAlert(data));
                    $('#btn_delete').prop('disabled', false);
                    // console.log(errors);
                }
            });
        }
        /*-----------------------------------------------------------------------------------*/

        /*-------------------- sched type event in add & update modal -----------------------*/
        function setModalElementVisibility(type){
            if(type == 1 || type == 5 || type == 7){ //customer and office and online visit
                $("#add_customer_schedule").show();
                $("#add_mapping_event_schedule").hide();

                $("#update_customer_schedule").show();
                $("#update_mapping_event_schedule").hide();
            }
            else{
                $("#add_customer_schedule").hide();
                $("#add_mapping_event_schedule").show();

                $("#update_customer_schedule").hide();
                $("#update_mapping_event_schedule").show();
            }
        }

        function setCalendarDate(date){
            $('#calendar').fullCalendar('gotoDate', date);
        }

        $('#sel_add_sched_type, #sel_update_sched_type').on('select2:select', function (e) {
            var type = e.params.data.id;

            /* revise this code, radius must be defined in schedule type table */
            if(type == '1'){ // customer
                $("#radius-add").val('0.5');
                $("#radius-update").val('0.5');

                retrieveCustomers('#sel-customer-codes', null);
            }
            else if(type == '7'){ // online visit
                $("#radius-add").val('0.5');
                $("#radius-update").val('0.5');

                retrieveCustomers('#sel-customer-codes', null);
            }
            else if(type == '5'){ // office
                $("#radius-add").val('0.1');
                $("#radius-update").val('0.1');

                retrieveCustomers('#sel-customer-codes', 6);
            }
            else{
                $("#radius-add").val('10');
                $("#radius-update").val('10');
            }

            setModalElementVisibility(type);
        });
        /*-----------------------------------------------------------------------------------*/


        /****************************************************
         * ************* CALENDAR EVENTS ***************
         * ********************************************/

        var currentDate = '{{ Carbon::now()->format('Y-m-d') }}';
        var dragEvent;

        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left:   null,
                    center: null,
                    right:  null
                },

                defaultDate: currentDate,
                selectable: true,
                selectHelper: true,

                /*-------------- click day for adding schedule ------------------*/
                dayClick: function(date, allDay, jsEvent, view) {

                    //disable add
                    if(date.isBefore(currentDate)) {
                        $('#calendar').fullCalendar('unselect');
                        return false;
                    }

                    resetModal();
                    selectedDate = date.format();

                    $('#start-date').val(selectedDate);
                    $('#end-date').val(selectedDate);
                    $("#start-date").attr("min", selectedDate);
                    $("#end-date").attr("min", selectedDate);

                    $('#addModalLabel').text(date.format('MMMM D, Y'));
                    $('#addScheduleModal').modal('show');

                },
                /*----------- click event to update & delete schedule -----------*/
                eventClick: function(calEvent, jsEvent, view) {

                    //disable update if past date or sched already visited
                    if(calEvent.start.isBefore(currentDate) || calEvent.status == '1') {
                        $('#calendar').fullCalendar('unselect');
                        return false;
                    }

                    resetModal();

                    //disable delete current day is selected
                    if(calEvent.start.format() == moment().format('YYYY-MM-DD')){
                        $('#btn_delete').prop('disabled', true);

                        //disable update if current day and type is mapping and activity
                        if(calEvent.type == '1' || calEvent.type == '5'){
                            $('#btn_save_changes').prop('disabled', true);
                        }
                    }
                    else{
                        $('#btn_save_changes').prop('disabled', false);
                        $('#btn_delete').prop('disabled', false);
                    }


                    selectedSchedule = calEvent;
                    selectedDate = calEvent.start.format();

                    $('#user_id').val(calEvent.user_id);
                    $('#tsr_name').val(calEvent.fullname);

                    //set a value in select2
                    $('#sel_update_sched_type').val(calEvent.type).trigger('change');
                    $('#sel_customer_code').val(calEvent.code).trigger('change');
                    setModalElementVisibility(calEvent.type);

                    $('#schedule_name').val(calEvent.name);
                    $('#address').val(calEvent.address);

                    $('#start_time').val(calEvent.start_time);
                    $('#end_time').val(calEvent.end_time);
                    $('#radius-update').val(calEvent.km_distance);
                    $('#remarks').val(calEvent.remarks);

                    $('#a-map-preview').attr("href", "https://www.google.com/maps/place/" + calEvent.lat + "," + calEvent.lng + "");

                    $('#updateModalLabel').text(calEvent.start.format('MMMM D, Y'));
                    $('#updateScheduleModal').modal('show');
                },

                /*------------ drag event to another to change date -------------*/
                eventDrop: function(event, delta, revertFunc) {

                    //disable update if past date or sched already visited
                    if(dragEvent.start.isBefore(currentDate) || event.start.isBefore(currentDate) || event.status == '1' || dragEvent.start.format('Y-M-DD') == currentDate) {
                        $('#calendar').fullCalendar('unselect');
                        revertFunc();
                        return false;
                    }

                    if (!confirm("Are you sure about this change?")) {
                        revertFunc();
                    }
                    else{
                        changeDateSchedule(event);
                    }
                },
                eventDragStop: function(event, jsEvent, ui, view ) {
                    dragEvent = event;
                },
                /*---------------------------------------------------------------*/
                editable: true,
                eventLimit: 3,
            });
        });


        function retrieveCustomers(selectId, classification){
            $(selectId).empty();

            $(selectId).select2({
                ajax: {
                    url: 'schedule-customer/' + classification, // set classification to office else null or all
                    dataType: 'json',
                    delay: 400,
                    data: function(params) {
                        return {
                            q: params.term // search term
                        };
                    },
                    processResults: function(data, params) {
                        let resData = [];
                        data.forEach(function(value) {
                            if (value.name.toUpperCase().indexOf(params.term.toUpperCase()) != -1)
                                resData.push(value)
                        });
                        return {
                            results: $.map(resData, function(item) {
                                return {
                                    text: item.name,
                                    id: item.customer_code
                                }
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                // closeOnSelect: false
            })
        }

    </script>

@endsection
