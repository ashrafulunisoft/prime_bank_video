@extends('layouts.receptionist')

@section('title', 'Rate Your Experience')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3><i class="fas fa-star"></i> How was your call?</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('video.feedback.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $session->id }}">
                        
                        <div class="text-center mb-4">
                            <p class="text-muted">Your feedback helps us improve our service</p>
                        </div>
                        
                        <div class="form-group text-center mb-4">
                            <label class="d-block mb-3">Rate your experience</label>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="rating" value="{{ $i }}" id="rating-{{ $i }}" class="d-none" required>
                                    <label for="rating-{{ $i }}" class="star-label">
                                        <i class="fas fa-star star-icon" data-rating="{{ $i }}"></i>
                                    </label>
                                @endfor
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label>Comments (optional)</label>
                            <textarea name="comment" class="form-control" rows="4" 
                                placeholder="Share your experience or suggestions..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            Submit Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .rating-stars {
        display: inline-flex;
        gap: 10px;
    }
    
    .star-label {
        cursor: pointer;
        font-size: 36px;
        transition: all 0.2s;
    }
    
    .star-icon {
        color: #ddd;
        transition: all 0.2s;
    }
    
    .star-label:hover .star-icon,
    .star-label:hover ~ .star-label .star-icon,
    input:checked ~ .star-label .star-icon {
        color: #ffc107;
    }
    
    input:checked ~ .star-label:hover .star-icon,
    input:checked ~ .star-label:hover ~ .star-label .star-icon {
        color: #ffc107;
    }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('input[name="rating"]').forEach(input => {
        input.addEventListener('change', function() {
            const rating = this.value;
            document.querySelectorAll('.star-icon').forEach(star => {
                const starRating = parseInt(star.dataset.rating);
                if (starRating <= rating) {
                    star.style.color = '#ffc107';
                } else {
                    star.style.color = '#ddd';
                }
            });
        });
    });
</script>
@endpush
