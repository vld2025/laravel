<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ReportsPdfMail extends Mailable
{
    use Queueable, SerializesModels;

    public $files;
    public $monthName;
    public $year;

    public function __construct($files = [], $monthName = null, $year = null)
    {
        $this->files = $files;
        $this->monthName = $monthName ?: date('F');
        $this->year = $year ?: date('Y');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Report PDF VLD Service - {$this->monthName} {$this->year}",
            from: config('mail.from.address', 'invoice@vldservice.ch'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reports-pdf',
            with: [
                'files' => $this->files,
                'monthName' => $this->monthName,
                'year' => $this->year,
            ]
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        
        if (is_array($this->files)) {
            foreach ($this->files as $file) {
                if (isset($file['file_path']) && Storage::disk('public')->exists($file['file_path'])) {
                    $attachments[] = Attachment::fromStorage('public/' . $file['file_path'])
                        ->as($file['file_name'] ?? 'report.pdf')
                        ->withMime('application/pdf');
                }
            }
        }
        
        return $attachments;
    }
}
