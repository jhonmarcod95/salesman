
<script src="{{ asset('js/all.js') }}"></script>

<!-- Argon Scripts -->
<!-- Core -->
<script src="{{ url('vendor/jquery/dist/jquery.min.js') }}"></script>

{{-- conflict boostrap --}}
<script src="{{ url('js/bootstrap.min.js') }}" defer></script>
<script src="{{ url('js/popper.min.js') }}" ></script>
{{--<script src="{{ url('vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>--}}
{{-----------------------}}

<!-- Optional JS -->
<script src="{{ url('vendor/chart.js/dist/Chart.min.js') }}"></script>
<script src="{{ url('vendor/chart.js/dist/Chart.extension.js') }}"></script>

{{--<!-- Argon JS -->--}}
<script src="{{ url('js/argon.js?v=1.0.0') }}"></script>

{{-- Fullcalendar --}}
<script src='{{ url('fullcalendar/moment.min.js') }}'></script>
<script src='{{ url('fullcalendar/jquery.min.js') }}'></script>
<script src='{{ url('fullcalendar/jquery-ui.min.js') }}'></script>
<script src='{{ url('fullcalendar/fullcalendar.min.js') }}'></script>

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
        width: '100%'
    });
    /*-------------------------------------------------------*/

    $('.sel2').select2({
        width: '100%'
    });
</script>

<script>
function formatAMPM(time) {
    var time = new Date('1995-12-30 ' + time);
    return time.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
}
</script>

@yield('script')
