@include('components.head')

@include('components.navbar')


<main id="main">
    {{-- Hero --}}
    @include('components.hero')
    {{-- Hero End --}}


    {{-- About --}}
    @include('components.about')
    {{-- About End --}}

    {{-- Categori --}}
    @include('components.categori')
    {{-- Categori End --}}

</main>


@include('components.scripts')
