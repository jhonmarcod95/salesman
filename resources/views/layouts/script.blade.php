<script src="{{ url('vendor/jquery/dist/jquery.min.js') }}"></script>
@if (Request::is('schedules'))
    <script src="{{ url('js/popper.min.js') }}" ></script>
    {{--Fullcalendar--}}
    <script src='{{ url('fullcalendar/moment.min.js') }}'></script>
    <script src='{{ url('fullcalendar/jquery.min.js') }}'></script>
    <script src='{{ url('fullcalendar/jquery-ui.min.js') }}'></script>
    <script src='{{ url('fullcalendar/fullcalendar.min.js') }}'></script>
@else
    <script src="{{ asset('js/all.js') }}" defer></script>
@endif

{{-- popover fixer --}}
<script src="{{ url('js/bootstrap.min.js') }}" defer></script>

<!-- Select2 -->
<script src='{{ url('select2/select2.min.js') }}'></script>
<script>
    /*---------- allow select2 in bootstrap modal -----------*/
    $('.addScheduleModalSel2').select2({
        dropdownParent: $('#addScheduleModal'),
        width: '100%',
        placeholder: "Select a record"

    });
    $('.updateScheduleModalSel2').select2({
        dropdownParent: $('#updateScheduleModal'),
        width: '100%',
        placeholder: "Select a record"
    });
    /*-------------------------------------------------------*/

    $('.sel2').select2({
        width: '100%',
        placeholder: "Select a record"
    });
</script>

<script>
function formatAMPM(time) {
    var time = new Date('1995-12-30 ' + time);
    return time.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
}
function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}
</script>

@yield('script')
