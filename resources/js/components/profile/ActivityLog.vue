<template>
<div class="col-md-9 pl-md-3 pt-md-0 pt-sm-4 pt-4">
    <div class="card card-with-shadow border-0">
        <div class="tab-content px-primary">
            <div class="tab-pane fade active show">
                <div class="d-flex justify-content-between">
                    <h5 class="d-flex align-items-center text-capitalize mb-0 title tab-content-header">
                    Activity Log
                    </h5>
                    <div class="d-flex align-items-center mb-0">
                    </div>
                </div>
                <hr>
                <div class="content py-primary">
                    <div class="content">
                        <div>
                            <div class="d-flex justify-content-between">
                                <div class="d-flex justify-content-start">
                                    <div class="filters-wrapper d-flex justify-content-start flex-wrap">
                                    </div>
                                </div>
                                <div>
                                    <div class="mr-0 single-filter single-search-wrapper">
                                        <div class="form-group form-group-with-search d-flex align-items-center">
                                            <span class="form-control-feedback">
                                                <i class="bi bi-search p-1"></i>
                                            </span>
                                            <input type="text" placeholder="Search" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="h-100">
                                <div class="card border-0 bg-transparent">
                                    <div class="card-body p-0">
                                        <div class="datatable">
                                            <div class="table-responsive custom-scrollbar table-view-responsive px-0 py-primary">
                                                <table class="table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th track-by="0" class="datatable-th pt-0">
                                                            <span class="font-size-default">
                                                                <span>
                                                                Title
                                                                </span>
                                                            </span>
                                                        </th>
                                                        <th track-by="1" class="datatable-th pt-0">
                                                            <span class="font-size-default">
                                                            <span>
                                                            Name
                                                            </span>
                                                            </span>
                                                        </th>
                                                        <th track-by="2" class="datatable-th pt-0">
                                                            <span class="font-size-default">
                                                            <span>
                                                            Time
                                                            </span>
                                                            </span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                </table>
                                                <div class="no-data-found-wrapper text-center p-primary">
                                                    <img src="img/icons/agreement.png" alt="" class="mb-primary">
                                                    <p class="mb-0 text-center">
                                                    Nothing to show here
                                                    </p>
                                                    <p class="mb-0 text-center text-secondary font-size-90">
                                                    Please add a new entity or manage the data table to see the content here
                                                    </p>
                                                    <p class="mb-0 text-center text-secondary font-size-90">
                                                    Thank you
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-primary">
                                                <div class="text-center">
                                                    <button type="button" class="btn  btn-load-more-data" style="display: none;">
                                                    <span>
                                                    Load more
                                                    </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</template>

<script>
export default {
    data() {
        return {

        }
    },
    mounted() {

    },
    methods: {

    }
}
</script>
