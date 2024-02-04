<template>
    <div>
        <div class="h-100">
            <div class="card border-0 bg-transparent card-body p-0 datatable">
                <div class="mt-2 table-responsive custom-scrollbar table-view-responsive shadow py-primary">
                    <table class="table font-size-default mb-5">
                        <thead>
                        <tr>
                            <th class="datatable-th pt-0" style="width: 40%">Name</th>
                            <th class="datatable-th pt-0 text-center">Manager</th>
                            <th class="datatable-th pt-0 text-center">Description</th>
                            <th class="datatable-th pt-0 text-center">Team Member</th>
                            <th class="datatable-th pt-0 text-center">View</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="mb-0">
                            <td class="datatable-td text-start pt-0">IT</td>
                            <td class="datatable-td text-center pt-0">Jasri Ishak</td>
                            <td class="datatable-td text-center pt-0">Infra,Support,Programming</td>
                            <td class="datatable-td text-center pt-0">7</td>
                            <td class="datatable-td text-right pt-4">
                                <a href="/employee/" target="_blank"  class="badge badge-pill badge-primary">Active</a>
                            </td>
                        </tr>
                        <tr class="mb-0">
                            <td class="datatable-td text-start pt-0">IT</td>
                            <td class="datatable-td text-center pt-0">Jasri Ishak</td>
                            <td class="datatable-td text-center pt-0">Infra,Support,Programming</td>
                            <td class="datatable-td text-center pt-0">7</td>
                            <td class="datatable-td text-right pt-4">
                                <a href="/employee/" target="_blank"  class="badge badge-pill badge-primary">Active</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Department"
}
</script>
