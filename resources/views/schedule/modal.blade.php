<!-- Add Schedule Modal -->
<div id="loading"></div>
<div class="modal fade" id="addScheduleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Schedule (<span id="addModalLabel"></span>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['id' => 'formAddSchedule']) !!}

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>Technical Sales Representative</h5>
                            {!! Form::select('user_id', $tsrs, null, ['class' => 'addScheduleModalSel2']) !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>Schedule Type</h5>
                            {!! Form::select('type', $scheduleTypes, null, ['id' => 'sel_add_sched_type', 'class' => 'addScheduleModalSel2']) !!}
                        </div>
                    </div>
                </div>

                {{-- Customer --}}
                <div id="add_customer_schedule">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Customer</h5>
                                {!! Form::select('customer_codes[]', $customers, null, ['class' => 'addScheduleModalSel2', 'multiple', 'required']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Event & Mapping Name --}}
                <div id="add_mapping_event_schedule">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Name</h5>
                                {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => '100']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Address</h5>
                                {!! Form::text('address', null, ['id' => 'add-address', 'class' => 'form-control', 'rows' => '2', 'maxlength' => '150']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Start & End Date --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>Start Date</h5>
                            {!! Form::date('start_date', '', ['class' => 'form-control', 'id' => 'start-date']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>End Date</h5>
                            {!! Form::date('end_date', '', ['class' => 'form-control', 'id' => 'end-date']) !!}
                        </div>
                    </div>
                </div>

                {{-- Start & End Time & Radius --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <h5>Start Time</h5>
                            {!! Form::time('start_time', '08:00', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <h5>End Time</h5>
                            {!! Form::time('end_time', '18:00', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <h5>Radius (KM)</h5>
                            {!! Form::number('radius', 2, ['class' => 'form-control', 'min' => '0', 'max' => '6371', 'id' => 'radius-add']) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h5>Remarks</h5>
                            {!! Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => '2', 'maxlength' => '1000']) !!}
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
                {!! Form::button('Save', ['id' => 'btn_save', 'class' => 'btn btn-primary', 'onclick' => 'storeSchedule()']) !!}
            </div>
        </div>
    </div>
</div>

<!-- Update Schedule Modal -->
<div class="modal fade" id="updateScheduleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Schedule (<span id="updateModalLabel"></span>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['id' => 'formUpdateSchedule']) !!}

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>Technical Sales Representative</h5>
                            {!! Form::text('tsr_name', null, ['id' => 'tsr_name', 'class' => 'form-control', 'disabled']) !!}
                            {!! Form::hidden('user_id', null, ['id' => 'user_id']) !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>Schedule Type</h5>
                            {!! Form::select('type', $scheduleTypes, null, ['id' => 'sel_update_sched_type', 'class' => 'updateScheduleModalSel2']) !!}
                        </div>
                    </div>
                </div>

                {{-- Customer --}}
                <div id="update_customer_schedule">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Customer</h5>
                                {!! Form::select('customer_code[]', $customers, null, ['id' => 'sel_customer_code', 'class' => 'updateScheduleModalSel2', 'required']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Event & Mapping Name --}}
                <div id="update_mapping_event_schedule">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Name</h5>
                                {!! Form::text('name', null, ['id' => 'schedule_name', 'class' => 'form-control', 'maxlength' => '100']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5>Address</h5>
                                {!! Form::text('address', null, ['id' => 'address', 'class' => 'form-control', 'rows' => '2', 'maxlength' => '150']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Start & End Time --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <h5>Start Time</h5>
                            {!! Form::time('start_time', null, ['id' => 'start_time', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <h5>End Time</h5>
                            {!! Form::time('end_time', null, ['id' => 'end_time', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <h5>Radius (KM)</h5>
                            {!! Form::number('radius', 2, ['class' => 'form-control', 'min' => '0', 'max' => '6371', 'id' => 'radius-update']) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h5>Remarks</h5>
                            {!! Form::textarea('remarks', null, ['id' => 'remarks', 'class' => 'form-control', 'rows' => '3', 'maxlength' => '1000']) !!}
                        </div>
                        <div class="form-group">
                            <a id="a-map-preview" href="#" target="_blank">Map preview</a>
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
                {!! Form::button('Save Changes', ['id' => 'btn_save_changes', 'class' => 'btn btn-primary', 'onclick' => 'updateSchedule()']) !!}
                {!! Form::button('Delete', ['id' => 'btn_delete', 'class' => 'btn btn-warning', 'onclick' => 'destroySchedule()']) !!}
            </div>
        </div>
    </div>
</div>
