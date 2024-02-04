<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#holidayModal">
</button>

<!-- Modal -->

<div class="modal fade" id="holidayModal" tabindex="-1" role="dialog" aria-labelledby="holidayModalLabel" aria-modal="true">
    <div class="modal-body custom-scrollbar undefined">
        <div role="document" class="modal-dialog modal-dialog-top modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                    Add Holiday
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                  </div>
                 <div class="modal-body custom-scrollbar undefined">
                    <form data-url="app/holidays">
                        <div>
                            <div class="form-group" placeholder="Enter name " required="required">
                                <label class="">
                                Name
                                </label>
                                <small class="text-muted font-italic">
                                </small>
                                <div>
                                    <input type="text" name="model" id="formData.name" required="required" placeholder="Enter name " autocomplete="false" class="form-control ">
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
                                                        <input placeholder="Enter start date " class="form-control" type="text"  onfocus="(this.type ='date')" id="dateHoliday">
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
                                    <div class="form-group" min-date="Thu Oct 27 2022 13:20:31 GMT+0800 (Malaysia Time)" placeholder="Enter end date " required="required">
                                        <label class="">
                                        End date
                                        </label>
                                        <small class="text-muted font-italic">
                                        </small>
                                        <div>
                                            <span>
                                                <div class="date-picker-input">
                                                    <div class="input-group">
                                                        <input placeholder="Enter end date " class="form-control" type="text"  onfocus="(this.type ='date')" id="dateHoliday">
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
                            <div class="form-group" list="[object Object],[object Object],[object Object],[object Object],[object Object],[object Object]" list-value-field="name" label="Available for (As default to all)" id="departments" error-message="">
                                <label class="text-left d-block mb-2 mb-lg-0">
                                Available for (As default to all)
                                </label>
                                <small class="text-muted font-italic">
                                You can set the holiday only for specific department by adding them.
                                </small>
                                <div>
                                    <div class="dropdown-search-select">
                                        <div class="search-filter-dropdown">
                                            <div class="dropdown dropdown-with-animation">
                                                <div id="dropdownMenuLink" data-toggle="dropdown" class="p-2 chips-container custom-scrollbar" aria-expanded="false" onclick="showHolidayDepartment()">
                                                    <span class="add">
                                                    + Add
                                                    </span>
                                                </div>
                                                <div aria-labelledby="dropdownMenuLink" class="d-none dropdown-menu chips-dropdown-menu py-0 show" id="holidayDepartment" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 38px, 0px);">
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
                                                        Main Department
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
                                                        <span class="check-sign float-right">
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
                        </div>
                        <div class="mb-primary">
                            <div class="customized-checkbox checkbox-default">
                                <input type="checkbox" name="formData_repeats_annually" id="repeats_annually" value="0">
                                <label for="repeats_annually" class="">
                                Repeats annually
                                </label>
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







