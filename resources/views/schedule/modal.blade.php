<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['id' => 'formAddSchedule']) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h5>Technical Sales Representative</h5>
                            {!! Form::select('tsr_id', $tsrs, null, ['class' => 'addScheduleModalSel2', 'required']) !!}
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h5>Customer</h5>
                            {!! Form::select('customer_codes[]', $customers, null, ['class' => 'addScheduleModalSel2', 'multiple', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>Start Time</h5>
                            {!! Form::time('start_time', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>End Time</h5>
                            {!! Form::time('end_time', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h5>Remarks</h5>
                            {!! Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => '3']) !!}
                        </div>
                    </div>
                </div>

                {{-- Error Display Here --}}
                <div class="text text-warning">
                    <ul id="errorList">
                    </ul>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                {!! Form::button('Close', ['class' => 'btn btn-secondary', 'data-dismiss' => 'modal']) !!}
                {!! Form::button('Save', ['class' => 'btn btn-primary', 'onclick' => 'storeSchedule()']) !!}
            </div>
        </div>
    </div>
</div>

<!-- Update Schedule Modal -->
<div class="modal fade" id="updateScheduleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['id' => 'formUpdateSchedule']) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h5>Technical Sales Representative</h5>
                            {!! Form::text('tsr_name', null, ['id' => 'tsr_name', 'class' => 'form-control', 'disabled']) !!}
                            {!! Form::hidden('tsr_id', null, ['id' => 'tsr_id']) !!}
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h5>Customer</h5>
                            {!! Form::select('customer_code', $customers, null, ['id' => 'customer_code', 'class' => 'updateScheduleModalSel2', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>Start Time</h5>
                            {!! Form::time('start_time', null, ['id' => 'start_time', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>End Time</h5>
                            {!! Form::time('end_time', null, ['id' => 'end_time', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h5>Remarks</h5>
                            {!! Form::textarea('remarks', null, ['id' => 'remarks', 'class' => 'form-control', 'rows' => '3']) !!}
                        </div>
                    </div>
                </div>

                {{-- Error Display Here --}}
                <div class="text text-warning">
                    <ul id="updateScheduleErrorList">
                    </ul>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                {!! Form::button('Close', ['class' => 'btn btn-secondary', 'data-dismiss' => 'modal']) !!}
                {!! Form::button('Save Changes', ['class' => 'btn btn-primary', 'onclick' => 'updateSchedule()']) !!}
                {!! Form::button('Delete', ['class' => 'btn btn-warning', 'onclick' => 'destroySchedule()']) !!}
            </div>
        </div>
    </div>
</div>