@extends('layouts.student.app')

@section('page_title', 'Ujian Berlangsung')

@section('content')
    <style>
        :root {
            --primary: #4F46E5;
            --primary-light: #EEF2FF;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --dark: #1E293B;
            --gray: #64748B;
            --light: #F8FAFC;
        }

        body {
            background-color: #F1F5F9;
        }

        .exam-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .timer-badge {
            background: white;
            color: var(--danger);
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 1.25rem;
            font-weight: 700;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .05);
            border: 1px solid #FECACA;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .question-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .05), 0 4px 6px -2px rgba(0, 0, 0, .025);
            border: 1px solid rgba(255, 255, 255, .5);
            overflow: hidden;
            margin-bottom: 2rem;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        .question-header {
            background: linear-gradient(to right, #F8FAFC, #FFFFFF);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .question-number {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
        }

        .question-text {
            font-size: 1.15rem;
            line-height: 1.8;
            color: var(--dark);
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .reading-text-card {
            background: #ffffff;
            border: 1px solid #CBD5E1;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .04);
        }

        .reading-text-card-header {
            background: #F1F5F9;
            border-bottom: 1px solid #CBD5E1;
            padding: .75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
        }

        .reading-text-content {
            padding: 1rem;
            max-height: 28rem;
            overflow-y: auto;
            color: #334155;
            line-height: 1.75;
            background: white;
        }

        .option-label {
            display: flex;
            align-items: flex-start;
            padding: 1.25rem;
            border: 2px solid #E2E8F0;
            border-radius: 16px;
            cursor: pointer;
            transition: all .2s ease;
            background: white;
            margin-bottom: 1rem;
            position: relative;
            width: 100%;
        }

        .option-label:hover {
            border-color: #CBD5E1;
            background-color: #F8FAFC;
            transform: translateY(-2px);
        }

        .option-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .option-input:checked+.option-label {
            border-color: var(--primary);
            background-color: var(--primary-light);
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, .1);
        }

        .option-indicator {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid #CBD5E1;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all .2s;
            background: white;
            margin-top: .1rem;
        }

        .option-input:checked+.option-label .option-indicator {
            border-color: var(--primary);
            background: var(--primary);
        }

        .option-indicator::after {
            content: '';
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
            transform: scale(0);
            transition: transform .2s;
        }

        .option-input:checked+.option-label .option-indicator::after {
            transform: scale(1);
        }

        .option-checkbox-indicator {
            border-radius: 7px !important;
        }

        .option-checkbox-indicator::after {
            border-radius: 3px !important;
        }

        .option-content {
            font-size: 1rem;
            color: #334155;
            font-weight: 500;
            line-height: 1.65;
            width: 100%;
        }

        .nav-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .05);
            border: none;
            position: sticky;
            top: 20px;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: .5rem;
            padding: 1.5rem;
        }

        .nav-item-btn {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
            background: white;
            color: var(--gray);
            font-weight: 600;
            font-size: .9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
        }

        .nav-item-btn:hover {
            background: #F1F5F9;
            color: var(--dark);
        }

        .nav-item-btn.active {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        .nav-item-btn.answered {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .nav-item-btn.doubtful {
            background: var(--warning);
            color: white;
            border-color: var(--warning);
        }

        .btn-action {
            padding: .75rem 1.5rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all .2s;
            text-transform: uppercase;
            font-size: .85rem;
            letter-spacing: .05em;
        }

        .btn-prev {
            background: white;
            border: 1px solid #E2E8F0;
            color: var(--gray);
        }

        .btn-prev:hover {
            background: #F8FAFC;
            color: var(--dark);
        }

        .btn-next {
            background: linear-gradient(135deg, #4F46E5 0%, #4338CA 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, .3);
        }

        .btn-next:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(79, 70, 229, .4);
        }

        .btn-finish {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            border: none;
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, .3);
            transition: all .2s;
        }

        .btn-finish:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, .4);
        }

        .custom-checkbox-styled {
            display: flex;
            align-items: center;
            gap: .5rem;
            cursor: pointer;
            padding: .5rem 1rem;
            border-radius: 8px;
            transition: background .2s;
        }

        .custom-checkbox-styled:hover {
            background: #FFFBEB;
        }

        .custom-checkbox-styled input {
            width: 1.2em;
            height: 1.2em;
            accent-color: var(--warning);
        }

        .question-text img,
        .reading-text-card img,
        .option-content img,
        .boolean-content img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 8px;
            margin: 10px 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .1);
            display: block;
        }

        .question-text table,
        .reading-text-card table,
        .option-content table,
        .boolean-content table {
            width: 100% !important;
            display: block;
            overflow-x: auto;
            border-collapse: collapse;
        }

        .bg-soft-success {
            background-color: #ECFDF5;
        }

        .bg-soft-danger {
            background-color: #FEF2F2;
        }

        @media (max-width: 991.98px) {
            .nav-card {
                position: static;
                margin-top: 1.5rem;
            }

            .question-header {
                padding: 1rem;
            }

            .timer-badge {
                font-size: 1rem;
            }

            .nav-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }
    </style>

    <div class="exam-container">
        <div class="row">
            <div class="col-lg-9 col-md-12">
                <div class="question-card">
                    <div class="question-header">
                        <div class="question-number">
                            Soal No. <span id="current-number" class="text-primary text-2xl">1</span>
                        </div>
                        <div class="timer-badge">
                            <i class="fas fa-stopwatch"></i>
                            <span id="timer-display">00:00:00</span>
                        </div>
                    </div>

                    <div class="p-4 p-md-5">
                        @forelse($questions as $index => $question)
                            @php
                                $savedAnswer = $savedAnswers->get($question->id);
                            @endphp

                            <div class="question-block" id="question-{{ $index + 1 }}"
                                style="display: {{ $index == 0 ? 'block' : 'none' }}">
                                @if($question->questionGroup)
                                    <div class="mb-3">
                                        <span
                                            class="badge badge-lg bg-indigo-100 text-indigo-700 px-3 py-2 rounded-lg border border-indigo-200">
                                            <i class="fas fa-layer-group mr-2"></i>
                                            Grup: <strong>{{ $question->questionGroup->name }}</strong>
                                        </span>
                                    </div>
                                @endif

                                @if($question->readingText)
                                    <div class="reading-text-card mb-4">
                                        <div class="reading-text-card-header">
                                            <h6 class="font-weight-bold text-slate-700 m-0 text-sm">
                                                <i class="fas fa-book-open mr-2 text-indigo-600"></i>
                                                Petunjuk / Bacaan
                                                @if($question->readingText->title)
                                                    <span class="text-muted">: {{ $question->readingText->title }}</span>
                                                @endif
                                            </h6>
                                            <span class="text-xs text-slate-500 font-medium">Baca stimulus dengan teliti</span>
                                        </div>
                                        <div class="reading-text-content">
                                            {!! $question->readingText->content !!}
                                        </div>
                                    </div>
                                @endif

                                <div class="question-text">
                                    {!! $question->content !!}
                                </div>

                                <div class="answer-section">
                                    @if($question->type == 'essay')
                                        <div class="form-group">
                                            <label class="font-weight-bold mb-2">Jawaban Esai Anda:</label>
                                            <textarea class="form-control essay-input" id="essay-{{ $question->id }}" rows="6"
                                                placeholder="Tulis jawaban Anda di sini..." data-question-id="{{ $question->id }}"
                                                data-index="{{ $index + 1 }}">{{ $savedAnswer ? $savedAnswer->answer_text : '' }}</textarea>
                                            <small class="text-muted mt-2 d-block">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Jawaban akan tersimpan otomatis saat Anda mengetik.
                                            </small>
                                        </div>
                                    @elseif($question->type == 'multiple_choice_complex')
                                        @php
                                            $savedArr = $savedAnswer && $savedAnswer->answer_text ? json_decode($savedAnswer->answer_text, true) : [];
                                            if (!is_array($savedArr))
                                                $savedArr = [];
                                            $savedArr = array_map('strval', $savedArr);
                                        @endphp
                                        <div class="options-group">
                                            @foreach($question->options as $option)
                                                <div>
                                                    <input class="option-input answer-complex" type="checkbox"
                                                        id="opt-{{ $question->id }}-{{ $option->id }}"
                                                        name="question_{{ $question->id }}[]" value="{{ $option->id }}"
                                                        data-question-id="{{ $question->id }}" data-index="{{ $index + 1 }}" {{ in_array((string) $option->id, $savedArr, true) ? 'checked' : '' }}>
                                                    <label for="opt-{{ $question->id }}-{{ $option->id }}" class="option-label">
                                                        <div class="option-indicator option-checkbox-indicator"></div>
                                                        <div class="option-content">{!! $option->content !!}</div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($question->type == 'boolean_grid')
                                        @php
                                            $savedGrid = $savedAnswer && $savedAnswer->answer_text ? json_decode($savedAnswer->answer_text, true) : [];
                                            if (!is_array($savedGrid))
                                                $savedGrid = [];
                                        @endphp
                                        <div class="options-group">
                                            <div class="table-responsive bg-white rounded-lg border shadow-sm">
                                                <table class="table table-borderless mb-0">
                                                    <thead class="bg-light border-bottom">
                                                        <tr>
                                                            <th class="py-3 px-4 font-weight-bold text-dark">Pernyataan</th>
                                                            <th class="py-3 px-4 text-center" style="width: 100px;">Benar</th>
                                                            <th class="py-3 px-4 text-center" style="width: 100px;">Salah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($question->options as $option)
                                                            @php
                                                                $ansGrid = array_key_exists($option->id, $savedGrid) ? (string) $savedGrid[$option->id] : null;
                                                            @endphp
                                                            <tr class="border-bottom">
                                                                <td class="py-3 px-4 align-middle font-weight-medium boolean-content">
                                                                    {!! $option->content !!}</td>
                                                                <td class="py-3 px-4 align-middle text-center bg-soft-success">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            name="grid_{{ $question->id }}_{{ $option->id }}"
                                                                            id="grid-b-{{ $option->id }}" value="1"
                                                                            class="custom-control-input answer-grid"
                                                                            data-question-id="{{ $question->id }}"
                                                                            data-option-id="{{ $option->id }}"
                                                                            data-index="{{ $index + 1 }}" {{ $ansGrid === '1' ? 'checked' : '' }}>
                                                                        <label class="custom-control-label"
                                                                            for="grid-b-{{ $option->id }}"></label>
                                                                    </div>
                                                                </td>
                                                                <td class="py-3 px-4 align-middle text-center bg-soft-danger">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            name="grid_{{ $question->id }}_{{ $option->id }}"
                                                                            id="grid-s-{{ $option->id }}" value="0"
                                                                            class="custom-control-input answer-grid"
                                                                            data-question-id="{{ $question->id }}"
                                                                            data-option-id="{{ $option->id }}"
                                                                            data-index="{{ $index + 1 }}" {{ $ansGrid === '0' ? 'checked' : '' }}>
                                                                        <label class="custom-control-label"
                                                                            for="grid-s-{{ $option->id }}"></label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @else
                                        {{-- multiple_choice --}}
                                        <div class="options-group">
                                            @foreach($question->options as $option)
                                                <div>
                                                    <input class="option-input answer-option" type="radio"
                                                        id="opt-{{ $question->id }}-{{ $option->id }}"
                                                        name="question_{{ $question->id }}" value="{{ $option->id }}"
                                                        data-question-id="{{ $question->id }}" data-index="{{ $index + 1 }}" {{ $savedAnswer && (string) $savedAnswer->question_option_id === (string) $option->id ? 'checked' : '' }}>
                                                    <label for="opt-{{ $question->id }}-{{ $option->id }}" class="option-label">
                                                        <div class="option-indicator"></div>
                                                        <div class="option-content">{!! $option->content !!}</div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning mb-0">
                                Belum ada soal yang tersedia untuk sesi ujian ini.
                            </div>
                        @endforelse
                    </div>

                    <div
                        class="question-footer bg-light p-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <button class="btn btn-action btn-prev" id="prev-btn"
                            onclick="changeQuestion(window.currentQuestion - 1)" disabled>
                            <i class="fas fa-arrow-left mr-2"></i> Sebelumnya
                        </button>

                        <label class="custom-checkbox-styled text-warning font-weight-bold mb-0">
                            <input type="checkbox" id="ragu-check">
                            <span>Ragu-ragu</span>
                        </label>

                        <button class="btn btn-action btn-next" id="next-btn"
                            onclick="changeQuestion(window.currentQuestion + 1)">
                            Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-12">
                <div class="nav-card">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                        <h5 class="font-weight-bold mb-0 text-dark">Navigasi Soal</h5>
                        <p class="text-xs text-muted mt-1">Klik nomor untuk pindah soal</p>
                    </div>

                    <div class="nav-grid">
                        @foreach($questions as $index => $question)
                            @php
                                $ans = $savedAnswers->get($question->id);
                                $statusClass = '';
                                if ($ans) {
                                    $statusClass = 'answered';
                                    if ($ans->is_doubtful) {
                                        $statusClass = 'doubtful';
                                    }
                                }
                            @endphp
                            <button class="nav-item-btn {{ $statusClass }}" id="nav-{{ $index + 1 }}"
                                onclick="goToQuestion({{ $index + 1 }})">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <div class="p-4 bg-gray-50 border-top rounded-bottom-lg">
                        <button class="btn btn-finish" onclick="finishExam()">
                            <i class="fas fa-check-circle mr-2"></i> Selesai Ujian
                        </button>
                        <div class="mt-3 text-center">
                            <div class="d-flex justify-content-center gap-3 text-xs text-muted flex-wrap">
                                <div class="d-flex align-items-center mr-2"><span
                                        class="w-3 h-3 bg-white border rounded mr-1"></span> Belum</div>
                                <div class="d-flex align-items-center mr-2"><span
                                        class="w-3 h-3 bg-primary rounded mr-1"></span> Sudah</div>
                                <div class="d-flex align-items-center"><span class="w-3 h-3 bg-warning rounded mr-1"></span>
                                    Ragu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="finish-form" action="#" method="POST" style="display: none;">
        @csrf
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            window.currentQuestion = 1;
            const totalQuestions = {{ count($questions) }};
            let remainingSeconds = {{ $remainingSeconds }};

            function startTimer() {
                const display = document.getElementById('timer-display');
                const timer = setInterval(function () {
                    if (remainingSeconds > 0) remainingSeconds--;

                    if (remainingSeconds <= 0) {
                        clearInterval(timer);
                        display.textContent = "00:00:00";
                        display.classList.add('text-danger');
                        alert('Waktu Habis!');
                        forceFinishExam();
                        return;
                    }

                    const hours = Math.floor(remainingSeconds / 3600);
                    const minutes = Math.floor((remainingSeconds % 3600) / 60);
                    const seconds = Math.floor(remainingSeconds % 60);

                    display.textContent =
                        String(hours).padStart(2, '0') + ':' +
                        String(minutes).padStart(2, '0') + ':' +
                        String(seconds).padStart(2, '0');
                }, 1000);
            }

            startTimer();

            window.changeQuestion = function (n) {
                goToQuestion(n);
            };

            window.goToQuestion = function (n) {
                if (n < 1 || n > totalQuestions) return;

                const oldBlock = document.getElementById('question-' + window.currentQuestion);
                const oldNav = document.getElementById('nav-' + window.currentQuestion);
                if (oldBlock) oldBlock.style.display = 'none';
                if (oldNav) oldNav.classList.remove('active');

                window.currentQuestion = n;

                const newBlock = document.getElementById('question-' + window.currentQuestion);
                const newNav = document.getElementById('nav-' + window.currentQuestion);
                if (newBlock) newBlock.style.display = 'block';
                if (newNav) newNav.classList.add('active');

                document.getElementById('current-number').textContent = window.currentQuestion;
                document.getElementById('prev-btn').disabled = (window.currentQuestion === 1);

                const nextBtn = document.getElementById('next-btn');
                nextBtn.style.display = window.currentQuestion === totalQuestions ? 'none' : 'inline-block';
                nextBtn.onclick = function () { changeQuestion(window.currentQuestion + 1); };
                document.getElementById('prev-btn').onclick = function () { changeQuestion(window.currentQuestion - 1); };

                const navBtn = document.getElementById('nav-' + window.currentQuestion);
                document.getElementById('ragu-check').checked = navBtn ? navBtn.classList.contains('doubtful') : false;

                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            if (document.getElementById('nav-1')) {
                document.getElementById('nav-1').classList.add('active');
            }

            function saveAnswer(questionId, answer, type) {
                const url = '{{ request()->route("subdomain") ? route("institution.student.exam.store_answer", request()->route("subdomain")) : route("student.exam.store_answer") }}';
                const payload = {
                    exam_session_id: '{{ $session->id }}',
                    question_id: questionId,
                    type: type
                };

                if (type === 'multiple_choice') {
                    payload.option_id = answer;
                } else if (type === 'multiple_choice_complex' || type === 'boolean_grid') {
                    payload.complex_answer = answer;
                } else {
                    payload.essay_answer = answer;
                }

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                    .then(res => res.json())
                    .then(data => console.log('Saved', data))
                    .catch(err => console.error('Error:', err));
            }

            document.querySelectorAll('.answer-option').forEach(item => {
                item.addEventListener('change', event => {
                    const questionId = event.target.getAttribute('data-question-id');
                    const optionId = event.target.value;
                    const index = event.target.getAttribute('data-index');
                    const navBtn = document.getElementById('nav-' + index);
                    if (navBtn) navBtn.classList.add('answered');
                    saveAnswer(questionId, optionId, 'multiple_choice');
                });
            });

            document.querySelectorAll('.answer-complex').forEach(item => {
                item.addEventListener('change', event => {
                    const questionId = event.target.getAttribute('data-question-id');
                    const index = event.target.getAttribute('data-index');
                    const checkedBoxes = document.querySelectorAll(`input.answer-complex[data-question-id="${questionId}"]:checked`);
                    const selected = Array.from(checkedBoxes).map(cb => cb.value);
                    const navBtn = document.getElementById('nav-' + index);
                    if (navBtn) {
                        selected.length > 0 ? navBtn.classList.add('answered') : navBtn.classList.remove('answered');
                    }
                    saveAnswer(questionId, selected, 'multiple_choice_complex');
                });
            });

            document.querySelectorAll('.answer-grid').forEach(item => {
                item.addEventListener('change', event => {
                    const questionId = event.target.getAttribute('data-question-id');
                    const index = event.target.getAttribute('data-index');
                    const radios = document.querySelectorAll(`input.answer-grid[data-question-id="${questionId}"]:checked`);
                    const gridAnswers = {};
                    radios.forEach(radio => {
                        gridAnswers[radio.getAttribute('data-option-id')] = radio.value;
                    });
                    const navBtn = document.getElementById('nav-' + index);
                    if (navBtn && Object.keys(gridAnswers).length > 0) navBtn.classList.add('answered');
                    saveAnswer(questionId, gridAnswers, 'boolean_grid');
                });
            });

            let essayTimeout = null;
            document.querySelectorAll('.essay-input').forEach(item => {
                item.addEventListener('input', event => {
                    const questionId = event.target.getAttribute('data-question-id');
                    const answer = event.target.value;
                    const index = event.target.getAttribute('data-index');
                    const navBtn = document.getElementById('nav-' + index);
                    if (navBtn) {
                        answer.trim().length > 0 ? navBtn.classList.add('answered') : navBtn.classList.remove('answered');
                    }
                    clearTimeout(essayTimeout);
                    essayTimeout = setTimeout(() => saveAnswer(questionId, answer, 'essay'), 1000);
                });
            });

            const raguCheck = document.getElementById('ragu-check');
            raguCheck.addEventListener('change', function () {
                const navBtn = document.getElementById('nav-' + window.currentQuestion);
                const questionBlock = document.getElementById('question-' + window.currentQuestion);
                const firstInput = questionBlock ? questionBlock.querySelector('[data-question-id]') : null;
                if (!firstInput) return;

                const questionId = firstInput.getAttribute('data-question-id');
                if (navBtn) {
                    this.checked ? navBtn.classList.add('doubtful') : navBtn.classList.remove('doubtful');
                }

                const url = '{{ request()->route("subdomain") ? route("institution.student.exam.store_answer", request()->route("subdomain")) : route("student.exam.store_answer") }}';
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        exam_session_id: '{{ $session->id }}',
                        question_id: questionId,
                        is_doubtful: this.checked
                    })
                }).catch(err => console.error('Error saving doubtful state:', err));
            });

            window.finishExam = function () {
                if (confirm('Apakah Anda yakin ingin menyelesaikan ujian ini? Jawaban tidak dapat diubah setelah ini.')) {
                    forceFinishExam();
                }
            };

            function forceFinishExam() {
                const form = document.getElementById('finish-form');
                form.action = '{{ request()->route("subdomain") ? route("institution.student.exam.finish", ["subdomain" => request()->route("subdomain"), "id" => $session->id]) : route("student.exam.finish", $session->id) }}';
                form.submit();
            }

            let cheatCount = 0;
            const maxCheat = 3;
            let lastCheatTime = 0;

            function reportCheat(triggerType) {
                const now = Date.now();
                if (now - lastCheatTime < 3000) return;
                lastCheatTime = now;
                cheatCount++;

                const url = '{{ request()->route("subdomain") ? route("institution.student.exam.report_cheat", request()->route("subdomain")) : route("student.exam.report_cheat") }}';
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ exam_session_id: '{{ $session->id }}' })
                }).catch(err => console.error('Reporting error:', err));

                if (typeof Swal === 'undefined') {
                    alert('Peringatan: Jangan meninggalkan halaman ujian.');
                    if (cheatCount >= maxCheat) forceFinishExam();
                    return;
                }

                if (cheatCount >= maxCheat) {
                    Swal.fire({
                        title: 'DISKUALIFIKASI!',
                        text: 'Anda telah melanggar aturan ujian lebih dari ' + (maxCheat - 1) + ' kali. Ujian dihentikan otomatis.',
                        icon: 'error',
                        allowOutsideClick: false,
                        confirmButtonText: 'Keluar'
                    }).then(() => forceFinishExam());
                    return;
                }

                Swal.fire({
                    title: 'PERINGATAN KECURANGAN!',
                    html: `Dilarang meninggalkan halaman ujian!<br><br>` +
                        `<div class="p-3 bg-red-100 text-red-700 rounded-lg">` +
                        `Pelanggaran: <b>${cheatCount}</b> / ${maxCheat}<br>` +
                        `Status: <b>Terdeteksi Pindah Tab/Aplikasi</b>` +
                        `</div><br>` +
                        `<span class="text-sm text-gray-500">Jika Anda melanggar ${maxCheat} kali, ujian akan diselesaikan otomatis.</span>`,
                    icon: 'warning',
                    confirmButtonText: 'Saya Mengerti, Lanjutkan Ujian',
                    allowOutsideClick: false,
                    backdrop: `rgba(239, 68, 68, 0.4)`
                });
            }

            document.addEventListener("visibilitychange", function () {
                if (document.hidden) reportCheat('visibility');
            });

            window.addEventListener("blur", function () {
                setTimeout(() => {
                    if (!document.hasFocus()) reportCheat('blur');
                }, 500);
            });

            document.addEventListener("contextmenu", e => e.preventDefault());
            document.addEventListener("copy", e => e.preventDefault());
            document.addEventListener("cut", e => e.preventDefault());
            document.addEventListener("paste", e => e.preventDefault());
        });
    </script>
@endpush