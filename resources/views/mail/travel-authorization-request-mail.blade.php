@component('mail::message')
    <p style="text-align: center;">
        {{ $requester->name }} from {{ $requester->department->name }} has applied for <strong>Travel Authorization (E-Form)</strong> and assigned <strong>you</strong> as the approver.
    </p>
    <p style="text-align: center;">Click the link below to navigate to Dayang HR Portal.</p>
    @component('mail::button', ['url' => 'https://app.desb.net/hq/login', 'color' => 'success'])
        app.desb.net
    @endcomponent
@endcomponent
