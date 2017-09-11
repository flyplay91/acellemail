@if ($admin)
    <div class="admin_col text-center">
        <img width="40" class="img-circle mr-10" src="{{ action('AdminController@avatar', $admin->uid) }}" alt=""><br />
        <span class="text-small">{{ $admin->displayName() }}</span>
    </div>
@endif
