@extends('layouts.app')

@section('title', 'Comentarios - Partida')

@section('content')
    <div class="page-header">
        <h1>💬 Comentarios de la Partida</h1>
        <a href="{{ route('torneos.bracket', $match->tournament) }}" class="btn btn-secondary">Volver al bracket</a>
    </div>

    {{-- Info de la partida --}}
    <div class="match-info-card">
        <div class="match-teams">
            <div class="team {{ $match->winner_id === $match->team1_id ? 'winner' : '' }}">
                @if($match->team1)
                    <a href="{{ route('teams.show', $match->team1) }}">{{ $match->team1->name }}</a>
                @else
                    <span class="text-muted">Por definir</span>
                @endif
            </div>
            <div class="vs">
                @if($match->isCompleted())
                    <span class="score">{{ $match->score_team1 }} - {{ $match->score_team2 }}</span>
                @else
                    VS
                @endif
            </div>
            <div class="team {{ $match->winner_id === $match->team2_id ? 'winner' : '' }}">
                @if($match->team2)
                    <a href="{{ route('teams.show', $match->team2) }}">{{ $match->team2->name }}</a>
                @else
                    <span class="text-muted">Por definir</span>
                @endif
            </div>
        </div>
        <div class="match-meta">
            <span>{{ $match->tournament->name }}</span>
            @if($match->scheduled_at)
                <span>📅 {{ $match->scheduled_at->format('d/m/Y H:i') }}</span>
            @endif
        </div>
    </div>

    {{-- Lista de comentarios --}}
    <div class="comments-section">
        @if($comments->isEmpty())
            <div class="empty-comments">
                <p class="text-muted">No hay comentarios en esta partida.</p>
            </div>
        @else
            <div class="comments-list">
                @foreach($comments as $comment)
                    <div class="comment {{ $comment->is_system ? 'system-comment' : '' }} {{ $comment->team_id === $match->team1_id ? 'team1-comment' : ($comment->team_id === $match->team2_id ? 'team2-comment' : '') }}">
                        @if($comment->is_system)
                            <div class="comment-content system">
                                <span class="system-icon">ℹ️</span>
                                {{ $comment->content }}
                            </div>
                        @else
                            <div class="comment-header">
                                <a href="{{ route('users.show', $comment->user) }}" class="comment-author">
                                    {{ $comment->user->name }}
                                </a>
                                @if($comment->team)
                                    <span class="comment-team badge badge-secondary">{{ $comment->team->name }}</span>
                                @endif
                                @if($comment->user->canManageTournaments())
                                    <span class="badge badge-primary">{{ ucfirst($comment->user->role) }}</span>
                                @endif
                            </div>
                            <div class="comment-content">
                                {{ $comment->content }}
                            </div>
                        @endif
                        <div class="comment-footer">
                            <span class="comment-time">
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                            @auth
                                @if($comment->user_id === auth()->id() || auth()->user()->role === 'admin')
                                    @if(!$comment->is_system)
                                        <form action="{{ route('matches.comments.destroy', $comment) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-link text-danger" onclick="return confirm('¿Eliminar este comentario?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Formulario para nuevo comentario --}}
        @if($canComment)
            <div class="comment-form-section">
                <form action="{{ route('matches.comments.store', $match) }}" method="POST" class="comment-form">
                    @csrf
                    <div class="form-group">
                        <textarea name="content" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Escribe un comentario..."
                                  maxlength="1000"
                                  required></textarea>
                    </div>
                    <div class="form-actions">
                        @if($userTeam)
                            <span class="text-muted">Comentando como miembro de <strong>{{ $userTeam->name }}</strong></span>
                        @endif
                        <button type="submit" class="btn btn-primary">Enviar comentario</button>
                    </div>
                </form>
            </div>
        @else
            @guest
                <div class="login-prompt">
                    <p><a href="{{ route('login') }}">Inicia sesión</a> para poder comentar.</p>
                </div>
            @else
                <div class="no-permission">
                    <p class="text-muted">Solo los capitanes de los equipos participantes pueden comentar.</p>
                </div>
            @endguest
        @endif
    </div>
@endsection

@push('styles')
<style>
    .match-info-card {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        color: white;
    }

    .match-teams {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2rem;
    }

    .match-teams .team {
        flex: 1;
        text-align: center;
    }

    .match-teams .team:first-child {
        text-align: right;
    }

    .match-teams .team:last-child {
        text-align: left;
    }

    .match-teams .team a {
        color: white;
        font-size: 1.25rem;
        font-weight: bold;
        text-decoration: none;
    }

    .match-teams .team.winner a {
        color: #ffc107;
    }

    .match-teams .vs {
        font-size: 1.5rem;
        font-weight: bold;
        color: rgba(255,255,255,0.5);
    }

    .match-teams .vs .score {
        color: #28a745;
    }

    .match-meta {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        margin-top: 1rem;
        color: rgba(255,255,255,0.6);
        font-size: 0.9rem;
    }

    .comments-section {
        background: var(--bg-secondary, #f8f9fa);
        border-radius: 8px;
        padding: 1.5rem;
    }

    .empty-comments {
        text-align: center;
        padding: 2rem;
    }

    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .comment {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        border-left: 4px solid #ddd;
    }

    .comment.team1-comment {
        border-left-color: #007bff;
    }

    .comment.team2-comment {
        border-left-color: #28a745;
    }

    .comment.system-comment {
        background: #e9ecef;
        border-left-color: #6c757d;
    }

    .comment-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .comment-author {
        font-weight: bold;
        color: #007bff;
        text-decoration: none;
    }

    .comment-author:hover {
        text-decoration: underline;
    }

    .comment-team {
        font-size: 0.75rem;
    }

    .comment-content {
        line-height: 1.5;
    }

    .comment-content.system {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #666;
        font-style: italic;
    }

    .comment-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.5rem;
        font-size: 0.85rem;
    }

    .comment-time {
        color: #999;
    }

    .btn-link {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .comment-form-section {
        border-top: 1px solid #ddd;
        padding-top: 1.5rem;
    }

    .comment-form .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.75rem;
    }

    .login-prompt, .no-permission {
        text-align: center;
        padding: 1rem;
        border-top: 1px solid #ddd;
        margin-top: 1rem;
    }

    .inline {
        display: inline;
    }
</style>
@endpush
