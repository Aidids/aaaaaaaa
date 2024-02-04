@if($flag === 'RequestLeave')
    @component('mail::message')
        <p style="text-align: center;">{{$requester->name}} from {{$requester->department->name}} has applied for leave and assigned <strong>you</strong> as the approver. Click the link below to navigate to Dayang HR Portal.</p>
        @component('mail::button', ['url' => 'https://app.desb.net/hq/login', 'color' => 'success'])
            app.desb.net
        @endcomponent
    @endcomponent
@endif

@if($flag === 'LeaveStatusUpdate')
    @component('mail::message')
        <p style="text-align: center;">
            {{ $requester->name }} your <br>
            <strong style="text-transform: uppercase;">{{ $type }} </strong>
            Request has been
            <strong style="text-transform: uppercase;">{{ $leaveRequest->overall_status }}</strong>.
        </p>
        <br>
        <p style="text-align: center;">Click the link below to navigate to Dayang HR Portal.</p>
        @component('mail::button', ['url' => 'https://app.desb.net/hq/login', 'color' => 'success'])
            app.desb.net
        @endcomponent
    @endcomponent
@endif
