

<!-- Modal -->

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-modal="true">
    <div class="modal-body custom-scrollbar undefined">
        <div class="modal-dialog modal-lg modal-dialog-scrollable " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                    Request Leave
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                    <div class="modal-body custom-scrollbar undefined">
                        <form enctype="multipart/form-data">
                            <div>
                                <p>
                                Leave availability
                                <a data-toggle="collapse" href="javascript:void(0);" aria-expanded="false" aria-controls="leaveAvailability" class="ml-2" onclick="showleaveavailability()">
                                Show
                                </a>
                                </p>
                                <div id="leaveAvailability" class="d-none collapse show" style="">
                                    <div class="note note-warning rounded p-4 mb-3">
                                        <div class="row">
                                            <div>
                                            No available leaves for this employee
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group" list-value-field="name" required="required" list="[object Object]">
                                    <label class="">
                                    Leave Type
                                    </label>
                                    <small class="text-muted font-italic">
                                    </small>
                                    <div>
                                        <select id="formData.leave_type_id" class="custom-select" style="background-image: url(&quot;https://payday.gainhq.com/images/chevron-down.svg&quot;);">
                                            <option disabled="disabled" value="" selected="selected">
                                            Choose a leave type
                                            </option>
                                            <option value="1">
                                            Paid Casual
                                            </option>
                                            <option value="2">
                                            Paid Sick
                                            </option>
                                            <option value="3">
                                            Unpaid Casual
                                            </option>
                                            <option value="4">
                                            Unpaid Sick
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row align-items-center my-primary py-3">
                                    <div class="col-md-3">
                                        <label>
                                        Leave
                                        <span class="text-muted">
                                        (Leave type)
                                        </span>
                                        </label>
                                    </div>
                                    <div class="col-md-9">
                                        <div>
                                            <div class="app-radio-group">
                                                <label class="customized-radio radio-default custom-radio-default">
                                                    <input type="radio" name="formData_leave_duration" id="formData_leave_duration-0" class="radio-inline" value="single_day" onclick="showLeaveSingleDay()" checked="checked">
                                                    <span class="outside">
                                                        <span class="inside">
                                                        </span>
                                                    </span>
                                                    Single Day
                                                </label>
                                                <label class="customized-radio radio-default custom-radio-default">
                                                    <input type="radio" name="formData_leave_duration" id="formData_leave_duration-1" class="radio-inline" value="multi_day" onclick="showLeaveMultiDay()">
                                                    <span class="outside">
                                                        <span class="inside">
                                                        </span>
                                                    </span>
                                                    Multi Day
                                                </label>
                                                <label class="customized-radio radio-default custom-radio-default">
                                                    <input type="radio" name="formData_leave_duration" id="formData_leave_duration-2" class="radio-inline" value="half_day" onclick="showLeaveHalfDay()">
                                                    <span class="outside">
                                                        <span class="inside">
                                                        </span>
                                                    </span>
                                                    Half Day
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Date  --}}

                                {{-- Single Day --}}

                                <div class="form-group show" required="required" placeholder="Enter date" id="leaveSingleDay">
                                    <label class="">
                                    Date
                                    </label>
                                    <small class="text-muted font-italic">
                                    </small>
                                    <div>
                                        <span>
                                            <div class="date-picker-input">
                                                <div class="input-group">
                                                    <input placeholder="Enter date" type="text"  onfocus="(this.type ='date')" class="form-control" id="dateSingleDay">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                                                </rect>
                                                                <line x1="16" y1="2" x2="16" y2="6">
                                                                </line>
                                                                <line x1="8" y1="2" x2="8" y2="6">
                                                                </line>
                                                                <line x1="3" y1="10" x2="21" y2="10">
                                                                </line>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vc-popover-content-wrapper">
                                            </div>
                                        </span>
                                    </div>
                                </div>

                                {{-- Multi Day --}}

                                <div class="d-none row show" id="leaveMultiDay">
                                    <div class="col-md-6">
                                        <div class="form-group" required="required" placeholder="Enter start date">
                                            <label class="">
                                            Start date
                                            </label>
                                            <small class="text-muted font-italic">
                                            </small>
                                            <div>
                                                <span>
                                                    <div class="date-picker-input">
                                                        <div class="input-group">
                                                            <input placeholder="Enter start date" class="form-control" type="text"  onfocus="(this.type ='date')" id="dateMultiDay1">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                                                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                                                        </rect>
                                                                        <line x1="16" y1="2" x2="16" y2="6">
                                                                        </line>
                                                                        <line x1="8" y1="2" x2="8" y2="6">
                                                                        </line>
                                                                        <line x1="3" y1="10" x2="21" y2="10">
                                                                        </line>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="vc-popover-content-wrapper">
                                                    </div>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" required="required" placeholder="Enter end date">
                                            <label class="">
                                            End date
                                            </label>
                                            <small class="text-muted font-italic">
                                            </small>
                                            <div>
                                                <span>
                                                    <div class="date-picker-input">
                                                        <div class="input-group">
                                                            <input placeholder="Enter end date" class="form-control" type="text"  onfocus="(this.type ='date')" id="dateMultiDay2">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                                                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                                                        </rect>
                                                                        <line x1="16" y1="2" x2="16" y2="6">
                                                                        </line>
                                                                        <line x1="8" y1="2" x2="8" y2="6">
                                                                        </line>
                                                                        <line x1="3" y1="10" x2="21" y2="10">
                                                                        </line>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="vc-popover-content-wrapper">
                                                    </div>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Half Day --}}
                            <div class="d-none row show" id="leaveHalfDay">
                                <label>Date</label>
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-6">
                                        <div>
                                            <span>
                                                <div class="date-picker-input">
                                                    <div class="input-group">
                                                        <input placeholder="Enter date" class="form-control" type="text"  onfocus="(this.type ='date')" id="halfDay">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                                                    </rect>
                                                                    <line x1="16" y1="2" x2="16" y2="6">
                                                                    </line>
                                                                    <line x1="8" y1="2" x2="8" y2="6">
                                                                    </line>
                                                                    <line x1="3" y1="10" x2="21" y2="10">
                                                                    </line>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="vc-popover-content-wrapper">
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div>
                                            <div class="app-radio-group">
                                                <label class="customized-radio radio-default custom-radio-default">
                                                    <input type="radio" name="formData_day_type" id="formData_day_type-0" class="radio-inline" value="first_half">
                                                    <span class="outside">
                                                        <span class="inside">
                                                        </span>
                                                    </span>
                                                    First half
                                                </label>
                                                <label class="customized-radio radio-default custom-radio-default">
                                                    <input type="radio" name="formData_day_type" id="formData_day_type-1" class="radio-inline" value="last_half">
                                                    <span class="outside">
                                                        <span class="inside">
                                                        </span>
                                                    </span>
                                                    Second half
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                {{-- Reason Note --}}

                                <div class="form-group" text-area-rows="4" placeholder="Add reason note here">
                                    <label class="">
                                    Reason note
                                    </label>
                                    <small class="text-muted font-italic">
                                    </small>
                                    <div>
                                        <textarea type="textarea" id="formData.note" name="model" placeholder="Add reason note here" rows="4" spellcheck="false" class="custom-scrollbar ">
                                        </textarea>
                                    </div>
                                </div>

                                {{-- Attachment --}}

                                <div class="form-group mb-0">
                                    <label>
                                    Attachments
                                    </label>
                                    <div>
                                        <div id="attachments" name="attachments" class="dropzone dz-clickable">
                                            <div class="dz-default dz-message ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload-cloud text-primary">
                                                    <polyline points="16 16 12 12 8 16">
                                                    </polyline>
                                                    <line x1="12" y1="12" x2="12" y2="21">
                                                    </line>
                                                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3">
                                                    </path>
                                                    <polyline points="16 16 12 12 8 16">
                                                    </polyline>
                                                </svg>
                                                <div class="input-area">
                                                    <label id="uploadLeaveAttachment" for="upload" class="text-primary text-center">
                                                    Browse
                                                    </label>
                                                    <input id="upload" type="file" class="form-control d-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                    Allowed file types: jpeg, jpg, gif, png, pdf.(Max file size is 4MB)
                                    </small>
                                </div>
                            </div>
                        </form>
                    </div>

      {{-- Modal Footer --}}

      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary mr-2">
        Cancel
        </button>&nbsp;
        <button type="submit" class="btn text-center btn-primary">
        <span class="w-100">
        Save
        </span>
        </button>
        </div>
            </div>
        </div>
    </div>
</div>



