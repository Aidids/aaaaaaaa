<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#workshiftModal">
</button>

<!-- Modal -->

<div class="modal fade" id="workshiftModal" tabindex="-1" role="dialog" aria-labelledby="workshiftModalLabel" aria-modal="true">
    <div class="modal-body custom-scrollbar undefined">
        <div role="document" class="modal-dialog modal-dialog-top modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="workshiftModalLabel">
                    Add Work Shift
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                  </div>
                 <div class="modal-body custom-scrollbar undefined">
                    <form data-url="app/working-shifts" method="POST" class="">
                        <div class="form-group" placeholder="Enter name " required="required">
                            <label class="">
                            Name
                            </label>
                            <small class="text-muted font-italic">
                            </small>
                            <div>
                                <input type="text" name="model" id="" required="required" placeholder="Enter name " autocomplete="false" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" placeholder="Enter start date " required="required">
                                    <label class="">
                                    Start date
                                    </label>
                                    <small class="text-muted font-italic">
                                    </small>
                                    <div>
                                        <span>
                                            <div class="date-picker-input">
                                                <div class="input-group">
                                                    <input placeholder="Enter start date " class="form-control">
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
                                <div class="form-group" placeholder="Enter end date ">
                                    <label class="">
                                    End date
                                    </label>
                                    <small class="text-muted font-italic">
                                    </small>
                                    <div>
                                        <span>
                                            <div class="date-picker-input">
                                                <div class="input-group">
                                                    <input placeholder="Enter end date " class="form-control">
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
                        <div class="form-group d-flex flex-column flex-md-row align-items-md-center my-primary">
                            <label class="mr-md-3 mb-md-0">
                            Choose a working shift type
                            </label>
                            <div>
                                <div>
                                    <div class="app-radio-group">
                                        <label class="customized-radio radio-default custom-radio-default">
                                            <input type="radio" name="formData_type" id="formData_type-0" class="radio-inline" value="regular">
                                                <span class="outside">
                                                    <span class="inside">
                                                    </span>
                                                </span>
                                                Office
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="mt-4">
                                <label>
                                Set Regular Week
                                </label>
                                <small class="text-muted">
                                (Set week with fixed time)
                                </small>
                            </div>
                            <div class="row">
                            @include('element.timepicker')
                            </div>
                            <div class="my-3">
                                <h6 class="text-danger">
                                Select weekend day (off days)
                                </h6>
                            </div>
                            <div class="mb-primary">
                                <div>
                                    <div class="app-checkbox-group row">
                                        <div class="customized-checkbox checkbox-default col-md-3">
                                            <input type="checkbox" name="formData_weekdays" id="formData_weekdays-sun" placeholder="" value="sun">
                                                <label for="formData_weekdays-sun" class="">
                                                Sunday
                                                </label>
                                        </div>
                                        <div class="customized-checkbox checkbox-default col-md-3">
                                            <input type="checkbox" name="formData_weekdays" id="formData_weekdays-mon" placeholder="" value="mon">
                                                <label for="formData_weekdays-mon" class="">
                                                Monday
                                                </label>
                                        </div>
                                        <div class="customized-checkbox checkbox-default col-md-3">
                                            <input type="checkbox" name="formData_weekdays" id="formData_weekdays-tue" placeholder="" value="tue">
                                                <label for="formData_weekdays-tue" class="">
                                                Tuesday
                                                </label>
                                        </div>
                                        <div class="customized-checkbox checkbox-default col-md-3">
                                            <input type="checkbox" name="formData_weekdays" id="formData_weekdays-wed" placeholder="" value="wed">
                                                <label for="formData_weekdays-wed" class="">
                                                Wednesday
                                                </label>
                                        </div>
                                        <div class="customized-checkbox checkbox-default col-md-3">
                                            <input type="checkbox" name="formData_weekdays" id="formData_weekdays-thu" placeholder="" value="thu">
                                                <label for="formData_weekdays-thu" class="">
                                                Thursday
                                                </label>
                                        </div>
                                        <div class="customized-checkbox checkbox-default col-md-3">
                                            <input type="checkbox" name="formData_weekdays" id="formData_weekdays-fri" placeholder="" value="fri">
                                                <label for="formData_weekdays-fri" class="">
                                                Friday
                                                </label>
                                        </div>
                                        <div class="customized-checkbox checkbox-default col-md-3">
                                            <input type="checkbox" name="formData_weekdays" id="formData_weekdays-sat" placeholder="" value="sat">
                                                <label for="formData_weekdays-sat" class="">
                                                Saturday
                                                </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" placeholder="Add description here" required="required">
                            <label class="">
                            Description
                            </label>
                                <small class="text-muted font-italic">
                                </small>
                                    <div>
                                        <textarea type="textarea" id="formData.description" name="model" required="required" placeholder="Add description here" spellcheck="false" class="custom-scrollbar ">
                                        </textarea>
                                    </div>
                        </div>
                        <div class="form-group" list="[object Object],[object Object],[object Object],[object Object],[object Object],[object Object]" list-value-field="name" label="Department " error-message="">
                            <label class="">
                            Department
                            </label>
                            <small class="text-muted font-italic">
                            </small>
                            <div>
                                <div class="dropdown-search-select">
                                    <div class="search-filter-dropdown">
                                        <div class="dropdown dropdown-with-animation">
                                            <div id="dropdownMenuLink" data-toggle="dropdown" class="p-2 chips-container custom-scrollbar">
                                                <span class="add">
                                                + Add
                                                </span>
                                            </div>
                                            <div aria-labelledby="dropdownMenuLink" class="d-none dropdown-menu chips-dropdown-menu py-0 show" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 43px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <div class="form-group form-group-with-search">
                                                    <span class="form-control-feedback">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                                            <circle cx="11" cy="11" r="8">
                                                            </circle>
                                                            <line x1="21" y1="21" x2="16.65" y2="16.65">
                                                            </line>
                                                        </svg>
                                                    </span>
                                                    <input type="text" placeholder="" autofocus="autofocus" class="form-control ">
                                                </div>
                                                <div class="dropdown-divider my-0">
                                                </div>
                                                <div class="dropdown-search-result-wrapper custom-scrollbar">
                                                    <a href="javascript: void(0);" class="dropdown-item">
                                                    All Department
                                                            <span class="check-sign float-right">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check menu-icon">
                                                                    <polyline points="20 6 9 17 4 12">
                                                                    </polyline>
                                                                </svg>
                                                            </span>
                                                    </a>
                                                    <a href="javascript: void(0);" class="dropdown-item">
                                                    Admin &amp; HRM
                                                            <span class="check-sign float-right">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check menu-icon">
                                                                    <polyline points="20 6 9 17 4 12">
                                                                    </polyline>
                                                                </svg>
                                                            </span>
                                                    </a>
                                                    <a href="javascript: void(0);" class="dropdown-item">
                                                    Accounts
                                                            <span class="check-sign float-right">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check menu-icon">
                                                                    <polyline points="20 6 9 17 4 12">
                                                                    </polyline>
                                                                </svg>
                                                            </span>
                                                    </a>
                                                    <a href="javascript: void(0);" class="dropdown-item">
                                                    Development
                                                            <span class="check-sign float-right">\
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check menu-icon">
                                                                    <polyline points="20 6 9 17 4 12">
                                                                    </polyline>
                                                                </svg>
                                                            </span>
                                                    </a>
                                                    <a href="javascript: void(0);" class="dropdown-item">
                                                    Software
                                                            <span class="check-sign float-right">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check menu-icon">
                                                                    <polyline points="20 6 9 17 4 12">
                                                                    </polyline>
                                                                </svg>
                                                            </span>
                                                    </a>
                                                    <a href="javascript: void(0);" class="dropdown-item">
                                                    UI &amp; UX
                                                            <span class="check-sign float-right">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check menu-icon">
                                                                    <polyline points="20 6 9 17 4 12">
                                                                    </polyline>
                                                                </svg>
                                                            </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-secondary mr-2">
                    Cancel
                    </button>
                    &nbsp;
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







