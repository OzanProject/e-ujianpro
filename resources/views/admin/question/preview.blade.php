<div class="question-preview-container p-2">
    @php
        $diffColor = $question->difficulty === 'easy' ? 'success' : ($question->difficulty === 'medium' ? 'warning' : 'danger');
        $diffText = strtoupper($question->difficulty === 'easy' ? 'Mudah' : ($question->difficulty === 'medium' ? 'Sedang' : 'Sulit'));
    @endphp

    <div class="d-flex align-items-center mb-4 mt-2 border-bottom pb-3">
        <span class="badge badge-pill badge-light-{{ $diffColor }} text-{{ $diffColor }} px-3 py-1 mr-2 border border-{{ $diffColor }}">
            <i class="fas fa-layer-group mr-1"></i> {{ $diffText }}
        </span>
        <span class="badge bg-light text-secondary border px-3 py-1">
            <i class="fas fa-book mr-1"></i> {{ $question->subject->name ?? 'Mata Pelajaran' }}
        </span>
    </div>

    @if($question->readingText)
        <div class="alert alert-info border-left-info shadow-none bg-blue-50 mb-4 p-3 rounded-lg">
            <h6 class="alert-heading font-weight-bold text-info mb-2 text-sm text-uppercase">
                <i class="fas fa-info-circle mr-1"></i> Petunjuk / Bacaan
            </h6>
            <div class="text-dark bg-white p-3 border rounded shadow-sm question-preview-html" style="font-size: 0.95rem;">
                {!! $question->readingText->content !!}
            </div>
        </div>
    @endif

    <div class="mb-4 text-dark font-weight-bold question-preview-html" style="font-size: 1.05rem; line-height: 1.8; font-family: Inter, sans-serif;">
        {!! $question->content !!}
    </div>

    @if($question->type === 'multiple_choice' || $question->type === 'multiple_choice_complex')
        <div class="options-list mt-4">
            @php $alphabet = range('A', 'Z'); @endphp
            @foreach($question->options as $index => $option)
                @if($index >= count($alphabet)) @break @endif
                @php $isCorrect = (bool) $option->is_correct; @endphp
                
                <div class="d-flex align-items-start p-3 mb-3 rounded-lg border {{ $isCorrect ? 'bg-success-fade border-success shadow-sm' : 'bg-white border-light shadow-xs' }}" 
                     style="transition: all 0.2s; {{ $isCorrect ? 'border-width: 2px !important;' : '' }}">
                    <span class="badge {{ $isCorrect ? 'badge-success shadow-sm' : 'badge-light border' }} mr-3 mt-1 py-2" style="width: 32px; font-size: 14px;">
                        {{ $alphabet[$index] }}
                    </span>
                    <div class="w-100 flex-grow-1 question-preview-html" style="font-size: 0.95rem; line-height: 1.6;">
                        {!! $option->content !!}
                    </div>
                    @if($isCorrect)
                        <div class="ml-3"><i class="fas fa-check-circle text-success fa-lg mt-2"></i></div>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif($question->type === 'boolean_grid')
        <div class="mt-4">
            <table class="table table-bordered bg-white shadow-sm">
                <thead class="bg-light-gray">
                    <tr>
                        <th>Pernyataan</th>
                        <th class="text-center" width="100">Kunci Jawaban</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($question->options as $option)
                        <tr>
                            <td class="align-middle question-preview-html">{!! $option->content !!}</td>
                            <td class="text-center align-middle">
                                @if($option->is_correct)
                                    <span class="badge badge-success px-3 py-2">BENAR</span>
                                @else
                                    <span class="badge badge-danger px-3 py-2">SALAH</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-5 bg-light-gray rounded-lg border text-muted text-center mt-4" style="border-style: dashed !important; border-width: 2px !important; border-color: #cbd5e1 !important;">
            <div class="bg-white rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width:60px; height:60px;">
                <i class="fas fa-pen-nib fa-lg text-primary opacity-75"></i>
            </div>
            <h6 class="font-weight-bold text-dark mb-1">Soal Uraian / Esai</h6>
            <p class="text-sm mb-0">Siswa akan diberikan area teks panjang untuk menjawab soal ini pada saat ujian.</p>
        </div>
    @endif
</div>

<style>
    .bg-blue-50 { background-color: #f0f8ff !important; }
    .bg-success-fade { background-color: #f0fdf4 !important; }
    .bg-light-gray { background-color: #f8f9fc !important; }
    .badge-light-success { background-color: rgba(40,167,69,0.1); border-color: rgba(40,167,69,0.2) !important; }
    .badge-light-warning { background-color: rgba(255,193,7,0.1); border-color: rgba(255,193,7,0.2) !important; }
    .badge-light-danger { background-color: rgba(220,53,69,0.1); border-color: rgba(220,53,69,0.2) !important; }
    .shadow-xs { box-shadow: 0 .125rem .25rem rgba(0,0,0,.04); }
    .question-preview-html img {
        max-width: 100% !important;
        height: auto !important;
        object-fit: contain;
        border-radius: 6px;
        border: 1px solid #e3e6f0;
        margin: 8px 0;
    }
    .question-preview-html p:last-child {
        margin-bottom: 0;
    }
    .question-preview-html table {
        width: 100% !important;
        max-width: 100% !important;
        margin-bottom: 1rem;
        border: 1px solid #e3e6f0;
    }
    .question-preview-html table td, 
    .question-preview-html table th {
        padding: 0.5rem;
        border: 1px solid #e3e6f0;
    }
</style>
