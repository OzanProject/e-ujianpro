@extends('layouts.student.app')

@section('page_title', 'Ujian Berlangsung')

@section('content')
<style>
    /* Custom Modern Styles for Exam Interface */
    :root {
        --primary: #4F46E5; /* Indigo 600 */
        --primary-light: #EEF2FF; /* Indigo 50 */
        --success: #10B981; /* Emerald 500 */
        --warning: #F59E0B; /* Amber 500 */
        --danger: #EF4444; /* Red 500 */
        --dark: #1E293B; /* Slate 800 */
        --gray: #64748B; /* Slate 500 */
        --light: #F8FAFC; /* Slate 50 */
    }
    
    body {
        background-color: #F1F5F9;
    }

    .exam-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Timer Badge */
    .timer-badge {
        background: white;
        color: var(--danger);
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 1.25rem;
        font-weight: 700;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        border: 1px solid #FECACA;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Question Card */
    .question-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.025);
        border: 1px solid rgba(255,255,255,0.5);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .question-header {
        background: linear-gradient(to right, #F8FAFC, #FFFFFF);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #E2E8F0;
        display: flex;
        justify-content: space-between;
        align-items: center;
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

    /* Option Cards */
    .option-label {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        border: 2px solid #E2E8F0;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
        margin-bottom: 1rem;
        position: relative;
    }

    .option-label:hover {
        border-color: #cbd5e1;
        background-color: #f8fafc;
        transform: translateY(-2px);
    }

    /* Custom Radio Input (Hidden) */
    .option-input {
        position: absolute;
        opacity: 0;
    }

    /* Selected State */
    .option-input:checked + .option-label {
        border-color: var(--primary);
        background-color: var(--primary-light);
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1);
    }

    /* Radio Indicator Circle */
    .option-indicator {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        margin-right: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s;
        background: white;
    }

    .option-input:checked + .option-label .option-indicator {
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
        transition: transform 0.2s;
    }

    .option-input:checked + .option-label .option-indicator::after {
        transform: scale(1);
    }

    .option-content {
        font-size: 1rem;
        color: #334155;
        font-weight: 500;
    }

    /* Navigation Sidebar */
    .nav-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        border: none;
        position: sticky;
        top: 20px;
    }

    .nav-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.5rem;
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
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
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

    /* Buttons */
    .btn-action {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.2s;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
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
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
    }
    
    .btn-next:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 8px -1px rgba(79, 70, 229, 0.4);
    }

    .btn-finish {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
        border: none;
        width: 100%;
        padding: 1rem;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        transition: all 0.2s;
    }

    .btn-finish:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4);
    }

    /* Custom Checkbox for Doubtful */
    .custom-checkbox-styled {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: background 0.2s;
    }
    
    .custom-checkbox-styled:hover {
        background: #FFFBEB;
    }

    .custom-checkbox-styled input {
        width: 1.2em;
        height: 1.2em;
        accent-color: var(--warning);
    }

</style>

<div class="exam-container">
    <div class="row">
        <!-- Question Section -->
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
                    @foreach($questions as $index => $question)
                        <div class="question-block" id="question-{{ $index + 1 }}" style="display: {{ $index == 0 ? 'block' : 'none' }}">
                            
                            <!-- Reading Text Block -->
                            @if($question->readingText)
                                <div class="reading-text-card mb-4 p-4 bg-blue-50 border-l-4 border-indigo-500 rounded-r-lg shadow-sm">
                                    <h6 class="font-bold text-gray-800 mb-2 border-b border-blue-200 pb-2 flex items-center">
                                        <i class="fas fa-book-open mr-2 text-indigo-600"></i> 
                                        Bacaan: {{ $question->readingText->title }}
                                    </h6>
                                    <div class="prose max-w-none text-gray-700 leading-relaxed text-sm overflow-y-auto max-h-96 pr-2 custom-scrollbar">
                                        {!! $question->readingText->content !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Question Text -->
                            <div class="question-text">
                                {!! $question->content !!}
                            </div>
                            
                            <!-- Options -->
                            <div class="options-group">
                                @foreach($question->options as $option)
                                    <div>
                                        <input class="option-input answer-option" 
                                               type="radio" 
                                               id="opt-{{ $question->id }}-{{ $option->id }}" 
                                               name="question_{{ $question->id }}" 
                                               value="{{ $option->id }}"
                                               data-question-id="{{ $question->id }}"
                                               data-index="{{ $index + 1 }}"
                                               {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id ? 'checked' : '' }}>
                                        
                                        <label for="opt-{{ $question->id }}-{{ $option->id }}" class="option-label">
                                            <div class="option-indicator"></div>
                                            <div class="option-content">{!! $option->content !!}</div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="question-footer bg-light p-4 border-top d-flex justify-content-between align-items-center">
                    <button class="btn btn-action btn-prev" id="prev-btn" onclick="changeQuestion(-1)" disabled>
                        <i class="fas fa-arrow-left mr-2"></i> Sebelumnya
                    </button>
                    
                    <label class="custom-checkbox-styled text-warning font-weight-bold">
                        <input type="checkbox" id="ragu-check">
                        <span>Ragu-ragu</span>
                    </label>

                    <button class="btn btn-action btn-next" id="next-btn" onclick="changeQuestion(1)">
                        Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Sidebar -->
        <div class="col-lg-3 col-md-12">
            <div class="nav-card">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h5 class="font-weight-bold mb-0 text-dark">Navigasi Soal</h5>
                    <p class="text-xs text-muted mt-1">Klik nomor untuk pindah soal</p>
                </div>
                
                <div class="nav-grid">
                    @foreach($questions as $index => $question)
                        @php
                            $statusClass = '';
                            if(isset($savedAnswers[$question->id])) {
                                $statusClass = 'answered'; 
                                // Note: We don't have is_doubtful in savedAnswers array yet (needs controller update to pass it ideally)
                                // For now, simple answered/active logic
                            }
                        @endphp
                        <button class="nav-item-btn {{ $statusClass }}" 
                                id="nav-{{ $index + 1 }}" 
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
                        <div class="d-flex justify-content-center gap-3 text-xs text-muted">
                            <div class="d-flex align-items-center mr-2"><span class="w-3 h-3 bg-white border rounded mr-1"></span> Belum</div>
                            <div class="d-flex align-items-center mr-2"><span class="w-3 h-3 bg-primary rounded mr-1"></span> Sudah</div>
                            <div class="d-flex align-items-center"><span class="w-3 h-3 bg-warning rounded mr-1"></span> Ragu</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Form for Finish --}}
<form id="finish-form" action="#" method="POST" style="display: none;">
    @csrf
</form>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    let currentQuestion = 1;
    const totalQuestions = {{ count($questions) }};
    let remainingSeconds = {{ $remainingSeconds }};
    
    // Timer Logic
    function startTimer() {
        const display = document.getElementById('timer-display');
        const timer = setInterval(function() {
            if (remainingSeconds > 0) remainingSeconds--;
            
            if (remainingSeconds <= 0) {
                clearInterval(timer);
                display.textContent = "00:00:00";
                display.classList.add('text-danger', 'blink');
                alert('Waktu Habis!'); 
                // finishExam(); // Uncomment to auto submit
                return;
            }

            let hours = Math.floor(remainingSeconds / 3600);
            let minutes = Math.floor((remainingSeconds % 3600) / 60);
            let seconds = Math.floor(remainingSeconds % 60);

            display.textContent = 
                (hours < 10 ? "0" + hours : hours) + ":" + 
                (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                (seconds < 10 ? "0" + seconds : seconds);
        }, 1000);
    }
    startTimer();

    // Navigation Logic
    window.changeQuestion = function(n) {
        goToQuestion(currentQuestion + n);
    }

    window.goToQuestion = function(n) {
        if (n < 1 || n > totalQuestions) return;

        // Visual update for Question Block
        document.getElementById('question-' + currentQuestion).style.display = 'none';
        
        // Remove active class from old nav
        document.getElementById('nav-' + currentQuestion).classList.remove('active');
        
        currentQuestion = n;
        
        document.getElementById('question-' + currentQuestion).style.display = 'block';
        document.getElementById('current-number').textContent = currentQuestion;
        
        // Add active class to new nav
        document.getElementById('nav-' + currentQuestion).classList.add('active');

        // Button states
        document.getElementById('prev-btn').disabled = (currentQuestion === 1);
        const nextBtn = document.getElementById('next-btn');
        if (currentQuestion === totalQuestions) {
            nextBtn.style.display = 'none';
        } else {
            nextBtn.style.display = 'inline-block';
            nextBtn.disabled = false;
        }

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Initialize first question active state
    document.getElementById('nav-1').classList.add('active');

    // Answer Saving Logic
    document.querySelectorAll('.answer-option').forEach(item => {
        item.addEventListener('change', event => {
            let questionId = event.target.getAttribute('data-question-id');
            let optionId = event.target.value;
            let index = event.target.getAttribute('data-index');
            
            // Mark nav as answered
            const navBtn = document.getElementById('nav-' + index);
            navBtn.classList.add('answered');
            navBtn.classList.remove('btn-outline-secondary'); // fallback removal

            // Send AJAX
            // Note: We use flexible route for subdomain support
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
                    option_id: optionId
                })
            })
            .then(res => res.json())
            .then(data => console.log('Saved'))
            .catch(err => console.error('Error:', err));
        });
    });

    // Doubtful Toggle Logic (Visual only for now, unless backed by DB later)
    const raguCheck = document.getElementById('ragu-check');
    raguCheck.addEventListener('change', function() {
        const navBtn = document.getElementById('nav-' + currentQuestion);
        if(this.checked) {
            navBtn.classList.add('doubtful');
        } else {
            navBtn.classList.remove('doubtful');
        }
    });

    // Finish Exam
    window.finishExam = function() {
        if(confirm('Apakah Anda yakin ingin menyelesaikan ujian ini? Jawaban tidak dapat diubah setelah ini.')) {
            const form = document.getElementById('finish-form');
            form.action = '{{ request()->route("subdomain") ? route("institution.student.exam.finish", ["subdomain" => request()->route("subdomain"), "id" => $session->id]) : route("student.exam.finish", $session->id) }}';
            form.submit();
        }
    }
});
</script>
@endpush
