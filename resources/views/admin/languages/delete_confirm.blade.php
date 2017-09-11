                        <h4>{{ trans('messages.delete_language_confirm_warning') }}</h4>
                        <ul class="modern-listing">
                            @foreach ($languages->get() as $language)
                                <li>
                                    <i class="icon-notification2 text-warning"></i>
                                    <h4 class="text-warning">{{ $language->name }}</h4>
                                    <p>
                                        @if ($language->users()->count())
                                            <span class="text-bold text-danger">
                                                {{ $language->users()->count() }}
                                            </span>
                                            {{ trans('messages.' . \Acelle\Library\Tool::getPluralPrase("user", $language->users()->count())) }}
                                        @else
                                            {{ trans('messages.no_user') }}
                                        @endif
                                    </p>                        
                                </li>
                            @endforeach
                        </ul>