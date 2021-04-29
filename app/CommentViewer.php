<?php namespace App {

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class CommentViewer extends Model
    {
        protected $fillable = [
            'user_id',
            'comment_id'
        ];

        public function commentTicket() {
            return $this->belongsTo(CommentTicket::class, 'id');
        }

        public function user() {
            return $this->hasOne(User::class, 'id', 'user_id');
        }
    }
}


