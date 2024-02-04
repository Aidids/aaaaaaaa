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
                                Announcements
                                </h4>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="float-md-right mb-3 mb-sm-3 mb-md-0">
                        <button class="btn btn-primary">
                        Add Announcement
                        </button>
                    </div>
                </div>
            </div>
            <div>
                <div class="d-flex justify-content-between">
                    <div class="d-flex justify-content-start">
                        <div class="filters-wrapper d-flex justify-content-start flex-wrap">
                            <div class="column-filter single-filter">
                                <div class="dropdown keep-inside-clicks-open">
                                    <button type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-filter">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2">
                                            </rect>
                                            <line x1="3" y1="9" x2="21" y2="9">
                                            </line>
                                            <line x1="9" y1="21" x2="9" y2="9">
                                            </line>
                                        </svg>
                                    </button>
                                    <div aria-labelledby="dropdownMenuButton-manage" class="dropdown-menu">
                                        <div class="btn-dropdown-close d-sm-none">
                                            <span class="title">
                                            Manage Columns
                                            </span>
                                            <span class="back float-right">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                                    <line x1="18" y1="6" x2="6" y2="18">
                                                    </line>
                                                    <line x1="6" y1="6" x2="18" y2="18">
                                                    </line>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="dropdown-item">
                                            <h6>Want to manage datatable?
                                            </h6>
                                            <p class="text-justify mb-0 filter-subtitle-text">
                                            Please drag and drop your column to reorder your table and enable see option as you want.
                                            </p>
                                        </div>
                                        <div class="dropdown-item manage-column-options custom-scrollbar pt-0">
                                            <div>
                                                <div class="row py-2">
                                                    <div class="col-12 d-flex justify-content-between">
                                                        <div class="content-type">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                                                                <line x1="3" y1="12" x2="21" y2="12">
                                                                </line>
                                                                <line x1="3" y1="6" x2="21" y2="6">
                                                                </line>
                                                                <line x1="3" y1="18" x2="21" y2="18">
                                                                </line>
                                                            </svg>
                                                            Title
                                                        </div>
                                                        <label class="custom-control border-switch mb-0 mr-3">
                                                            <input type="checkbox" id="switch-0" class="border-switch-control-input">
                                                            <span class="border-switch-control-indicator">
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row py-2">
                                                    <div class="col-12 d-flex justify-content-between">
                                                        <div class="content-type">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                                                                <line x1="3" y1="12" x2="21" y2="12">
                                                                </line>
                                                                <line x1="3" y1="6" x2="21" y2="6">
                                                                </line>
                                                                <line x1="3" y1="18" x2="21" y2="18">
                                                                </line>
                                                            </svg>
                                                            Department
                                                        </div>
                                                        <label class="custom-control border-switch mb-0 mr-3">
                                                            <input type="checkbox" id="switch-1" class="border-switch-control-input">
                                                            <span class="border-switch-control-indicator">
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row py-2">
                                                    <div class="col-12 d-flex justify-content-between">
                                                        <div class="content-type">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                                                                <line x1="3" y1="12" x2="21" y2="12">
                                                                </line>
                                                                <line x1="3" y1="6" x2="21" y2="6">
                                                                </line>
                                                                <line x1="3" y1="18" x2="21" y2="18">
                                                                </line>
                                                            </svg>
                                                            Start date
                                                        </div>
                                                        <label class="custom-control border-switch mb-0 mr-3">
                                                            <input type="checkbox" id="switch-2" class="border-switch-control-input">
                                                            <span class="border-switch-control-indicator">
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row py-2">
                                                    <div class="col-12 d-flex justify-content-between">
                                                        <div class="content-type">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                                                                <line x1="3" y1="12" x2="21" y2="12">
                                                                </line>
                                                                <line x1="3" y1="6" x2="21" y2="6">
                                                                </line>
                                                                <line x1="3" y1="18" x2="21" y2="18">
                                                                </line>
                                                            </svg>
                                                            End date
                                                        </div>
                                                        <label class="custom-control border-switch mb-0 mr-3">
                                                            <input type="checkbox" id="switch-3" class="border-switch-control-input">
                                                            <span class="border-switch-control-indicator">
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row py-2">
                                                    <div class="col-12 d-flex justify-content-between">
                                                        <div class="content-type">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                                                                <line x1="3" y1="12" x2="21" y2="12">
                                                                </line>
                                                                <line x1="3" y1="6" x2="21" y2="6">
                                                                </line>
                                                                <line x1="3" y1="18" x2="21" y2="18">
                                                                </line>
                                                            </svg>
                                                            Description
                                                        </div>
                                                        <label class="custom-control border-switch mb-0 mr-3">
                                                            <input type="checkbox" id="switch-4" class="border-switch-control-input">
                                                            <span class="border-switch-control-indicator">
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row py-2">
                                                    <div class="col-12 d-flex justify-content-between">
                                                        <div class="content-type">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                                                                <line x1="3" y1="12" x2="21" y2="12">
                                                                </line>
                                                                <line x1="3" y1="6" x2="21" y2="6">
                                                                </line>
                                                                <line x1="3" y1="18" x2="21" y2="18">
                                                                </line>
                                                            </svg>
                                                            Created by
                                                        </div>
                                                        <label class="custom-control border-switch mb-0 mr-3">
                                                            <input type="checkbox" id="switch-5" class="border-switch-control-input">
                                                            <span class="border-switch-control-indicator">
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row py-2">
                                                    <div class="col-12 d-flex justify-content-between">
                                                        <div class="content-type">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                                                                <line x1="3" y1="12" x2="21" y2="12">
                                                                </line>
                                                                <line x1="3" y1="6" x2="21" y2="6">
                                                                </line>
                                                                <line x1="3" y1="18" x2="21" y2="18">
                                                                </line>
                                                            </svg>
                                                            Actions
                                                        </div>
                                                        <label class="custom-control border-switch mb-0 mr-3">
                                                            <input type="checkbox" id="switch-6" class="border-switch-control-input">
                                                            <span class="border-switch-control-indicator">
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dropdown-divider d-none d-sm-block">
                                        </div>
                                        <div class="dropdown-item">
                                            <div class="row filter-action-button-wrapper">
                                                <div class="col-12 d-flex justify-content-between">
                                                    <button type="button" class="btn btn-clear pl-sm-0">
                                                    Clear
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                    Apply
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn d-block d-sm-none btn-toggle-filters">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3">
                                    </polygon>
                                </svg>
                                Filters
                            </button>
                            <span class="mobile-filters-wrapper">
                                <div class="filters-loop-wrapper">
                                    <div class="single-filter calendar-root">
                                        <div class="input-date">
                                            <span class="mr-2">
                                            Created
                                            </span>
                                        </div>
                                    </div>
                                    <div class="single-filter multi-select-filter search-filter-dropdown">
                                        <div class="dropdown dropdown-with-animation chips-dropdown">
                                            <a href="javascript: void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-filter px-3">
                                            Department
                                            </a>
                                            <div aria-labelledby="dropdownMenuLink" class="dropdown-menu py-0">
                                                <div class="btn-dropdown-close d-sm-none">
                                                    <span class="title">
                                                    Department
                                                    </span>
                                                    <span class="back float-right">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                                            <line x1="18" y1="6" x2="6" y2="18">
                                                            </line>
                                                            <line x1="6" y1="6" x2="18" y2="18">
                                                            </line>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="form-group form-group-with-search">
                                                    <span class="form-control-feedback">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                                            <circle cx="11" cy="11" r="8">
                                                                </circle>
                                                                <line x1="21" y1="21" x2="16.65" y2="16.65">
                                                                    </line>
                                                                </svg>
                                                            </span>
                                                            <input type="text" placeholder="Search" autofocus="autofocus" class="form-control">
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
                                        <button type="button" class="btn btn-primary btn-with-shadow d-sm-none btn-close-filter-wrapper d-flex justify-content-center align-items-center">
                                        Close
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="mr-0 single-filter single-search-wrapper">
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
                            </div>
                        </div>
                        <div class="h-100">
                            <div class="card border-0 bg-transparent">
                                <div class="card-body p-0">
                                    <div class="datatable">
                                        <div class="my-2 d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <p class="text-muted mb-0">
                                                Showing
                                                0
                                                to
                                                0
                                                items
                                                of
                                                0
                                                </p>
                                            </div>
                                        </div>
                                        <div class="table-responsive custom-scrollbar table-view-responsive shadow py-primary">
                                            <table class="table mb-0">
                                                <thead>
                                                    <tr><th track-by="0" class="datatable-th pt-0">
                                                        <span class="font-size-default">
                                                            <span>
                                                            Title
                                                            </span>
                                                        </span>
                                                    </th>
                                                    <th track-by="1" class="datatable-th pt-0">
                                                        <span class="font-size-default">
                                                            <span>
                                                            Department
                                                            </span>
                                                        </span>
                                                    </th>
                                                    <th track-by="2" class="datatable-th pt-0">
                                                        <span class="font-size-default">
                                                            <span>
                                                            Start date
                                                            </span>
                                                        </span>
                                                    </th>
                                                    <th track-by="3" class="datatable-th pt-0">
                                                        <span class="font-size-default">
                                                            <span>
                                                            End date
                                                            </span>
                                                        </span>
                                                    </th>
                                                    <th track-by="4" class="datatable-th pt-0">
                                                        <span class="font-size-default">
                                                            <span>
                                                            Description
                                                            </span>
                                                        </span>
                                                    </th>
                                                    <th track-by="5" class="datatable-th pt-0">
                                                        <span class="font-size-default">
                                                            <span>
                                                            Created by
                                                            </span>
                                                        </span>
                                                    </th>
                                                    <th track-by="6" class="datatable-th pt-0 text-right">
                                                        <span class="font-size-default">
                                                        Actions
                                                        </span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                        <div class="no-data-found-wrapper text-center p-primary">
                                            <img src="https://payday.gainhq.com/images/no_data.svg" alt="" class="mb-primary">
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
                                        <div>
                                            <div class="d-flex flex-column flex-md-row justify-content-between">
                                                <nav style="display: none;">
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
                                                        <li class="page-item disabled">
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
        </div>
    </div>
</div>


@endsection
