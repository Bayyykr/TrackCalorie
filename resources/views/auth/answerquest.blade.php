@include('components.head')
@include('components.navbar')

<div class="header-message">
    <a href="{{ route('forum.forum') }}" class="back-arrow">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m15 18-6-6 6-6" />
        </svg>
    </a>
</div>

<div class="container answer-body">
    <div class="content answer-content">
        @if (isset($post))
            <div class="question">
                <div class="question-header">

                    @php
                        $userName = isset($post->user->name) ? $post->user->name : 'Pengguna Dihapus';
                    @endphp
                    @auth
                        <img src="{{ asset('storage/' . Auth::user()->image_path) }}" alt="{{ $userName }}"
                            class="avatar">
                    @endauth
                    <div class="question-meta">
                        <div class="question-author">{{ $userName }}</div>
                        <div class="question-date">Ditanyakan pada 20 Juni 2025 
                            <span class="question-tag">Membangun Massa Otot</span>
                        </div>
                    </div>
                </div>

                <h2 class="question-title">{{ $post->title }}</h2>
            </div>

            @foreach ($post->answers as $answer)
                <div class="answer">
                    <div class="answer-header">
                        @php
                            $answerUserAvatar = isset($answer->user->avatar_url)
                                ? $answer->user->avatar_url
                                : asset('images/default-avatar.png');
                            $answerUserName = isset($answer->user->name) ? $answer->user->name : 'Pengguna Dihapus';
                        @endphp
                        @auth
                            <img src="{{ asset('storage/' . Auth::user()->image_path) }}" alt="{{ $answerUserName }}"
                                class="avatar">
                        @endauth
                        <div class="answer-meta">
                            <div class="answer-author">{{ $answerUserName }}</div>
                            <div class="answer-date">Dijawab pada 20 Juni 2025 
                                <span class="question-tag">Membangun Massa Otot</span>
                            </div>
                        </div>
                    </div>
                    <div class="answer-description">{{ $answer->content }}</div>
                </div>
            @endforeach

            <form action="{{ route('forum.answer.store') }}" method="POST" class="response-box" id="answerForm">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <textarea class="response-input" name="content" placeholder="Masukkan jawaban Anda di sini!" rows="1" required>{{ old('content', '') }}</textarea>
                <button type="submit" class="send-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                    </svg>
                </button>
            </form>
        @else
            <div class="alert alert-danger">
                Pertanyaan tidak ditemukan.
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('answerForm');
        const textarea = form.querySelector('textarea');

        // Cek jika halaman dimuat ulang setelah submit berhasil
        if (performance.navigation.type === 1) { // 1 berarti halaman di-reload
            textarea.value = ''; // Kosongkan textarea
        }

        form.addEventListener('submit', function() {
            // Kosongkan textarea setelah form disubmit
            setTimeout(function() {
                textarea.value = '';
            }, 100);
        });
    });
</script>
