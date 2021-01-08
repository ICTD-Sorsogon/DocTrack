<script>
    if (typeof app_vars === 'undefined') { app_vars = {};}
    app_vars.csrf_token = '{{ csrf_token() }}';
    app_vars.settings = JSON.parse('{!! \App\Http\AppVars::getAppJSON() !!}');
    if (typeof window.Laravel === 'undefined') {
        window.Laravel = {};
    }
    window.Laravel.csrfToken = app_vars.csrf_token;
    window.app_vars = app_vars;
</script>

