@component('mail::message')
# Hi,
You hava been invited to join the team
{{$invitation->team->name}}.
Because you are already registered to the platform, then you can accept or reject invitation in your [team management console]({{$url}}).
@component('mail::button', ['url' => $url])
       Go to Dashboard
@endcomponent
Thanks,<br>
{{ config('app.name') }}
@endcomponent
