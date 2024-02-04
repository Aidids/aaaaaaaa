<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#adddocumentModal">
</button>

<!-- Modal -->

<div class="modal fade" id="adddocumentModal" tabindex="-1" role="dialog" aria-labelledby="adddocumentModalLabel" aria-modal="true">
      <div class="modal-body custom-scrollbar undefined">
        <div role="document" class="modal-dialog modal-dialog-top modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adddocumentModalLabel">
                    Add Document
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                  </div>
                <div class="modal-body custom-scrollbar undefined">
                    <form data-url="app/employee/documents">
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
                        <div class="form-group" placeholder="Enter document ">
                            <label class="">
                            Document
                            </label>
                            <small class="text-muted font-italic">
                            </small>
                            <div>
                                <div class="custom-file">
                                    <input type="file" id="formData.file" class="custom-file-input">
                                        <label for="formData.file" class="custom-file-label">
                                        Enter document
                                        </label>
                                </div>
                            </div>
                            <small class="text-muted font-italic mt-3 d-inline-block">
                            Document size allowed: 5MB. Document type allowed: png, jpg, jpeg, txt, pdf, doc, docx, csv. Please check file and file format before upload.
                            </small>
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







