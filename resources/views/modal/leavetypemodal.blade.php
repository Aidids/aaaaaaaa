
<!-- Modal -->

<div class="modal fade" id="addLeaveTypeModal" tabindex="-1" role="dialog" aria-labelledby="addLeaveTypeModalLabel" aria-modal="true">
    <div class="modal-body custom-scrollbar undefined">
        <div role="document" class="modal-dialog modal-dialog-top modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLeaveTypeModalLabel">
                    Add Leave Type
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <div class="modal-body custom-scrollbar undefined">
                    <form data-url="app/leave-types">
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
                        <div class="form-group" list="[object Object],[object Object],[object Object]" placeholder="Enter type ">
                            <label class="">
                            Type
                            </label>
                            <small class="text-muted font-italic">
                            </small>
                            <div>
                                <select id="formData.type" class="custom-select" style="background-image: url(&quot;https://payday.gainhq.com/images/chevron-down.svg&quot;);">
                                    <option disabled="disabled" value="" selected>
                                    Select a type
                                    </option>
                                    <option value="paid">
                                    Paid
                                    </option>
                                    <option value="unpaid">
                                    Unpaid
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" placeholder="Enter amount " required="required">
                            <label class="">
                            Amount
                            </label>
                            <small class="text-muted font-italic">
                            </small>
                            <div>
                                <input type="number" name="model" id="formData.amount" required="required" placeholder="Enter amount " autocomplete="false" class="form-control ">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="form-group" radio-checkbox-name="enabled" required="required" list="[object Object],[object Object]">
                                <label class="">
                                Enabled
                                </label>
                                <small class="text-muted font-italic">
                                </small>
                                <div>
                                    <div class="app-radio-group">
                                        <label class="customized-radio radio-default custom-radio-default">
                                            <input type="radio" name="enabled" id="enabled-0" required="required" class="radio-inline" value="1">
                                            <span class="outside">
                                                <span class="inside">
                                                </span>
                                            </span>
                                            Yes
                                        </label>
                                        <label class="customized-radio radio-default custom-radio-default">
                                            <input type="radio" name="enabled" id="enabled-1" required="required" class="radio-inline" value="0">
                                            <span class="outside">
                                                <span class="inside">
                                                </span>
                                            </span>
                                            No
                                        </label>
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







