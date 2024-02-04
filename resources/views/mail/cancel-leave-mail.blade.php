@component('mail::message')
    <p style="text-align: center;">
        {{ $requester->name }} has cancel their <br>
        <strong style="text-transform: uppercase;">{{ $leaveRequest->leaveBalance->leave->name }} </strong>
        on
        <strong>{{ date('d M Y', strtotime($leaveRequest->start_date)) }}</strong>.
    </p>
@endcomponent

