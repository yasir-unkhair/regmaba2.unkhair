<?php

namespace App\Jobs;

use App\Mail\NotifResetpassword;
use App\Mail\PenetapanUktMail;
use App\Mail\RegisterMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMail implements ShouldQueue, ShouldBeUnique
// class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 60; // 1 menit

    protected $peserta;
    protected $email;
    protected $content;

    /**
     * Create a new job instance.
     */
    public function __construct($params)
    {
        $this->peserta = $params['get'];
        $this->email = $params['email'];
        $this->content = $params['content'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch ($this->content) {
            case 'registrasi':
                Mail::to($this->email)->send(new RegisterMail($this->peserta));
                break;

            case 'penetapanukt':
                Mail::to($this->email)->send(new PenetapanUktMail($this->peserta));
                break;

            case 'reset-password':
                Mail::to($this->email)->send(new NotifResetpassword($this->peserta));
                break;

            default:
                break;
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->email;
    }
}
