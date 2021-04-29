<?php namespace App {

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\HasOne;

    class CommentTicket extends Model
    {
        protected $fillable = [
            'user_id',
            'ticket_id',
            'description',
            'is_handler',
            'is_new',
            'is_read'
        ];

        public function user(): HasOne {
            return $this->hasOne(User::class, 'id', 'user_id');
        }

        public function ticket(): BelongsTo {
            return $this->belongsTo(Ticket::class);
        }

        public function commentViewer() {
            return $this->hasMany(CommentViewer::class, 'comment_id', 'id');
        }
    }
}
