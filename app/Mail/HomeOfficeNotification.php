<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\HomeOfficeRequest;

class HomeOfficeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $homeOfficeRequest;
    public $title;
    public $bodyMessage;

    public function __construct(HomeOfficeRequest $homeOfficeRequest, $title, $bodyMessage)
    {
        $this->homeOfficeRequest = $homeOfficeRequest;
        $this->title = $title;
        $this->bodyMessage = $bodyMessage;
    }

    public function build()
    {
        return $this->subject($this->title)
                    ->html($this->buildHtml());
    }

    private function buildHtml()
    {
        $id = str_pad($this->homeOfficeRequest->id, 4, '0', STR_PAD_LEFT);
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <img src='https://ragon.com.mx/wp-content/uploads/2022/10/logo-ragon-1.png' alt='Ragon' style='max-height: 50px;'>
            </div>
            <h2 style='color: #61b0a5; border-bottom: 2px solid #61b0a5; padding-bottom: 10px;'>{$this->title}</h2>
            <p style='font-size: 16px; color: #333;'>{$this->bodyMessage}</p>
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px;'>
                <h4 style='margin-top: 0; color: #555;'>Detalles de la Solicitud</h4>
                <ul style='list-style: none; padding: 0;'>
                    <li><strong>ID de Solicitud:</strong> HO-{$id}</li>
                    <li><strong>Usuario:</strong> {$this->homeOfficeRequest->user->name}</li>
                    <li><strong>Fecha de Inicio:</strong> {$this->homeOfficeRequest->scheduled_start_date}</li>
                    <li><strong>Fecha de Entrega:</strong> {$this->homeOfficeRequest->scheduled_end_date}</li>
                </ul>
            </div>
            <p style='margin-top: 30px; font-size: 12px; color: #888; text-align: center;'>
                Este es un mensaje automático del Sistema de Tickets y Equipos de Grupo Ragon.<br>
                Por favor, no respondas a este correo.
            </p>
        </div>";
    }
}
