<div class="locale-switcher inline-flex items-center gap-2">
    <form action="{{ route('locale.change') }}" method="POST" class="inline-block">
        @csrf
        <select 
            name="locale" 
            onchange="this.form.submit()"
            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
        >
            <option value="pt_BR" {{ app()->getLocale() === 'pt_BR' ? 'selected' : '' }}>
                🇧🇷 {{ __('app.locale_pt_BR') ?? 'Português (Brasil)' }}
            </option>
            <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>
                🇺🇸 {{ __('app.locale_en') ?? 'English (US)' }}
            </option>
            <option value="es" {{ app()->getLocale() === 'es' ? 'selected' : '' }}>
                🇪🇸 {{ __('app.locale_es') ?? 'Español' }}
            </option>
            <option value="de" {{ app()->getLocale() === 'de' ? 'selected' : '' }}>
                🇩🇪 {{ __('app.locale_de') ?? 'Deutsch' }}
            </option>
        </select>
    </form>
    
    @if(Session::has('currency'))
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ Session::get('currency') }}
        </span>
    @endif
</div>
