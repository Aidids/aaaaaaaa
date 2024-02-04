@extends('layouts.dashboard')

@section('content')
<div class="container-fluid page-body-wrapper">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb p-0 d-flex align-items-center mb-primary">
                            <li class="breadcrumb-item page-header d-flex align-items-center">
                                <h4 class="mb-0">
                                Users &amp; Roles
                                </h4>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-5 col-xl-5">
                    <div class="card card-with-shadow border-0 pb-primary">
                        <div class="card-header d-flex align-items-center p-primary primary-card-color">
                            <h5 class="card-title d-inline-block mb-0">
                            Users
                            </h5>
                            <div class="form-group form-group-with-search d-flex align-items-center">
                                <span class="form-control-feedback">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                        <circle cx="11" cy="11" r="8">
                                        </circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65">
                                        </line>
                                    </svg>
                                </span>
                                <input type="text" placeholder="Search" class="form-control">
                            </div>
                        </div>
                        <div class="p-primary d-flex align-items-center primary-card-color">
                            <ul class="nav tab-filter-menu justify-content-flex-end">
                                <li class="nav-item">
                                    <a href="javascript: void(0);" class="nav-link py-0 font-size-default active">
                                    All User
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <hr class="my-0">
                        <div class="card-body p-0">
                            <div>
                                <div class="datatable">
                                    <div class="table-responsive custom-scrollbar table-view-responsive py-primary">
                                        <table class="table mb-0">
                                            <thead>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td data-title="User" class="datatable-td pt-0 border-top-0">
                                                        <span class="">
                                                            <div class="media align-items-center" table-id="user-table">
                                                                <div class="avatars-w-50">
                                                                    <div>
                                                                        <div class="no-img rounded-circle avatar-shadow">
                                                                        FD
                                                                        </div>
                                                                        <span class="status bg-success">
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="media-body ml-3">
                                                                    <a href="javascript: void(0);">
                                                                    Foster D'Amore
                                                                    </a>
                                                                    <p class="text-muted font-size-90 mb-0">
                                                                    cbrown@example.net
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td data-title="User" class="datatable-td">
                                                        <span class="">
                                                            <div class="media align-items-center" table-id="user-table">
                                                                <div class="avatars-w-50">
                                                                    <div>
                                                                        <div class="no-img rounded-circle avatar-shadow">
                                                                        UO
                                                                        </div>
                                                                        <span class="status bg-success">
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="media-body ml-3">
                                                                    <a href="javascript: void(0);">
                                                                    Uriah O'Reilly
                                                                    </a>
                                                                    <p class="text-muted font-size-90 mb-0">
                                                                    zkrajcik@example.org
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td data-title="User" class="datatable-td">
                                                        <span class="">
                                                            <div class="media align-items-center" table-id="user-table">
                                                                <div class="avatars-w-50">
                                                                    <div>
                                                                        <div class="no-img rounded-circle avatar-shadow">
                                                                        DG
                                                                        </div>
                                                                        <span class="status bg-success">
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="media-body ml-3">
                                                                    <a href="javascript: void(0);">
                                                                    Duncan Gusikowski
                                                                    </a>
                                                                    <p class="text-muted font-size-90 mb-0">
                                                                    bradley.roob@example.org
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td data-title="User" class="datatable-td">
                                                        <span class="">
                                                            <div class="media align-items-center" table-id="user-table">
                                                                <div class="avatars-w-50">
                                                                    <div>
                                                                        <div class="no-img rounded-circle avatar-shadow">
                                                                        BW
                                                                        </div>
                                                                        <span class="status bg-success">
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="media-body ml-3">
                                                                    <a href="javascript: void(0);">
                                                                    Baby Wunsch
                                                                    </a>
                                                                    <p class="text-muted font-size-90 mb-0">
                                                                    jgreenfelder@example.com
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-primary">
                                        <div>
                                            <div class="d-flex flex-column flex-md-row justify-content-between">
                                                <div class="dropdown-menu-filter d-flex align-items-center justify-content-center justify-content-md-start">
                                                    <div class="dropdown keep-inside-clicks-open">
                                                        <button type="button" id="show-pagination-user-table" data-toggle="dropdown" class="btn btn-filter">
                                                        10
                                                        <img src="javascript: void(0);" alt="" style="height: 16px; width: 16px;">
                                                        </button>
                                                        <div aria-labelledby="show-pagination-user-table" class="my-2 dropdown-menu dropdown-menu-user-table">
                                                            <a href="javascript: void(0);" class="dropdown-item active disabled">
                                                            10
                                                            </a>
                                                            <a href="javascript: void(0);" class="dropdown-item">
                                                            20
                                                            </a>
                                                            <a href="javascript: void(0);" class="dropdown-item">
                                                            30
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <label class="text-muted ml-2 mb-0">
                                                    Items showing per page
                                                    </label>
                                                </div>
                                                <nav style="">
                                                    <ul class="pagination justify-content-center justify-content-md-end mb-0">
                                                        <li class="page-item disabled">
                                                            <a href="javascript: void(0);" aria-label="Previous" class="page-link border-0">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left">
                                                                    <line x1="19" y1="12" x2="5" y2="12">
                                                                    </line>
                                                                    <polyline points="12 19 5 12 12 5">
                                                                    </polyline>
                                                                </svg>
                                                            </a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a href="javascript: void(0);" class="page-link border-0 active disabled">
                                                            1
                                                            </a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a href="javascript: void(0);" class="page-link border-0">
                                                            2
                                                            </a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a href="javascript: void(0);" aria-label="Next" class="page-link border-0 align-content-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right menu-arrow">
                                                                    <line x1="5" y1="12" x2="19" y2="12">
                                                                    </line>
                                                                    <polyline points="12 5 19 12 12 19">
                                                                    </polyline>
                                                                </svg>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-7 col-xl-7">
                    <div class="card card-with-shadow border-0 h-100">
                        <div class="card-header d-flex align-items-center p-primary bg-transparent">
                            <h5 class="card-title d-inline-block mb-0">
                            Roles
                            </h5>
                            <div class="form-group form-group-with-search d-flex align-items-center">
                                <span class="form-control-feedback">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                        <circle cx="11" cy="11" r="8">
                                        </circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65">
                                        </line>
                                    </svg>
                                </span>
                                <input type="text" placeholder="Search" class="form-control">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div>
                                <div class="datatable">
                                    <div class="table-responsive custom-scrollbar table-view-responsive py-primary">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th track-by="0" class="datatable-th pt-0">
                                                        <span class="font-size-default">
                                                            <span>
                                                            Role name
                                                            </span>
                                                        </span>
                                                    </th>
                                                    <th track-by="1" class="datatable-th pt-0">
                                                        <span class="font-size-default">
                                                            <span>
                                                            Permission
                                                            </span>
                                                        </span>
                                                    </th>
                                                    <th track-by="2" class="datatable-th pt-0">
                                                        <span class="font-size-default">
                                                            <span>
                                                            Users
                                                            </span>
                                                        </span>
                                                    </th>
                                                    <th track-by="3" class="datatable-th pt-0 text-right">
                                                        <span class="font-size-default">
                                                        Manage Users
                                                        </span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td data-title="Role name" class="datatable-td">
                                                        <span class="">
                                                        App Admin
                                                        </span>
                                                    </td>
                                                    <td data-title="Permission" class="datatable-td">
                                                        <span>
                                                        </span>
                                                    </td>
                                                    <td data-title="Users" class="datatable-td">
                                                        <span class="">
                                                            <div avatars-group-class="avatars-group-w-50" name="exampleAvatarsGroup2" table-id="role-table">
                                                                <div class="avatar-group avatars-group-w-50">
                                                                    <div class="no-img rounded-circle avatar-bordered avatar-shadow">
                                                                    JD
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </span>
                                                    </td>
                                                    <td data-title="Manage Users" class="datatable-td text-md-right">
                                                        <span>
                                                            <div role="group" aria-label="Default action" class="btn-group btn-group-action">
                                                                <button type="button" data-toggle="tooltip" data-placement="top" title="" class="btn" data-original-title="Manage Users">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
                                                                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td data-title="Role name" class="datatable-td">
                                                        <span class="">
                                                        Manager
                                                        </span>
                                                    </td>
                                                    <td data-title="Permission" class="datatable-td">
                                                        <span>
                                                            <button class="btn btn-sm btn-primary rounded-pill px-3 py-1">
                                                            Manage
                                                            </button>
                                                        </span>
                                                    </td>
                                                    <td data-title="Users" class="datatable-td">
                                                        <span class="">
                                                            <div avatars-group-class="avatars-group-w-50" name="exampleAvatarsGroup2" table-id="role-table">
                                                                <div class="avatar-group avatars-group-w-50">
                                                                    <div class="no-img rounded-circle avatar-bordered avatar-shadow">
                                                                    SR
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </span>
                                                    </td>
                                                    <td data-title="Manage Users" class="datatable-td text-md-right">
                                                        <span>
                                                            <div role="group" aria-label="Default action" class="btn-group btn-group-action">
                                                                <button type="button" data-toggle="tooltip" data-placement="top" title="" class="btn" data-original-title="Manage Users">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
                                                                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td data-title="Role name" class="datatable-td">
                                                        <span class="">
                                                        Department Manager
                                                        </span>
                                                    </td>
                                                    <td data-title="Permission" class="datatable-td">
                                                        <span>
                                                            <button class="btn btn-sm btn-primary rounded-pill px-3 py-1">
                                                            View
                                                            </button>
                                                        </span>
                                                    </td>
                                                    <td data-title="Users" class="datatable-td">
                                                        <span class="">
                                                            <div avatars-group-class="avatars-group-w-50" name="exampleAvatarsGroup2" table-id="role-table">
                                                                <div class="avatar-group avatars-group-w-50">
                                                                    <div class="no-img rounded-circle avatar-bordered avatar-shadow">
                                                                    TS
                                                                    </div>
                                                                    <div class="no-img rounded-circle avatar-bordered avatar-shadow">
                                                                    FD
                                                                    </div>
                                                                    <div class="no-img rounded-circle avatar-bordered avatar-shadow">
                                                                    LC
                                                                    </div>
                                                                    <div class="no-img rounded-circle avatar-bordered avatar-shadow">
                                                                    SG
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </span>
                                                    </td>
                                                    <td data-title="Manage Users" class="datatable-td text-md-right">
                                                        <span>
                                                            <div role="group" aria-label="Default action" class="btn-group btn-group-action">
                                                                <button type="button" data-toggle="tooltip" data-placement="top" title="" class="btn" data-original-title="Manage Users">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
                                                                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td data-title="Role name" class="datatable-td">
                                                        <span class="">
                                                        Employee
                                                        </span>
                                                    </td>
                                                    <td data-title="Permission" class="datatable-td">
                                                        <span>
                                                            <button class="btn btn-sm btn-primary rounded-pill px-3 py-1">
                                                            View
                                                            </button>
                                                        </span>
                                                    </td>
                                                    <td data-title="Users" class="datatable-td">
                                                        <span class="">
                                                            <div avatars-group-class="avatars-group-w-50" name="exampleAvatarsGroup2" table-id="role-table">
                                                                <div class="avatar-group avatars-group-w-50">
                                                                    <img src="javascript: void(0);" alt="Dr. Banner" class="rounded-circle avatar-bordered avatar-shadow">
                                                                    <div class="no-img rounded-circle avatar-bordered avatar-shadow">
                                                                    FD
                                                                    </div>
                                                                    <div class="no-img rounded-circle avatar-bordered avatar-shadow">
                                                                    UO
                                                                    </div>
                                                                    <div class="no-img rounded-circle avatar-bordered avatar-shadow">
                                                                    DG
                                                                    </div>
                                                                    <div class="no-img rounded-circle avatar-bordered avatar-shadow">
                                                                    BW
                                                                    </div>
                                                                    <a class="pl-3 text-muted">
                                                                    +6 more
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </span>
                                                    </td>
                                                    <td data-title="Manage Users" class="datatable-td text-md-right">
                                                        <span>
                                                            <div role="group" aria-label="Default action" class="btn-group btn-group-action">
                                                                <button type="button" data-toggle="tooltip" data-placement="top" title="" class="btn" data-original-title="Manage Users">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
                                                                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-primary">
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

@endsection
