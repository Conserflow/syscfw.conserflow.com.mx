<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AvisoEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // public $tries = 10; //Reintentos de envio de correo

    public $mensaje;
    public $isError;
    public $detalle;
    public $asunto;
    public $from_test;
    public $from_Name;
    public $data_attachments;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mensaje = "Mensaje desconocido", $isError = false, $detalle = array(), $asunto, $from_test = "", $attachments = array(), $from_Name = "Avisos Automaticos")
    {
        $this->mensaje = $mensaje;
        $this->isError = $isError;
        $this->detalle = $detalle;
        $this->asunto = $asunto;
        $this->from_test = "webmaster@conserflow.com";
        $this->from_Name = $from_Name;
        $this->data_attachments = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from($this->from_test, $this->from_Name)
            ->subject($this->asunto)
            ->view("emails.aviso");

        foreach ($this->data_attachments as $file)
        {
            $email->attachData(base64_decode($file["base64"]), $file["name"]);
        }
        return $email;
    }
}
