<?php

namespace App\Http\Controllers;

use App\Mail\ContactAdminMail;
use App\Mail\ContactClientMail;
use App\Models\ContactMessage;
use App\Support\DeferredMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function how(): View
    {
        return view('shop.how');
    }

    public function studio(): View
    {
        return view('shop.studio');
    }

    public function creators(): View
    {
        return view('shop.creators');
    }

    public function faq(): View
    {
        return view('shop.faq');
    }

    public function shipping(): View
    {
        return view('shop.livraison-retours', ['zones' => site('shipping.zones'), 'days' => site('shipping.business_days'), 'countries' => site('shipping.countries')]);
    }

    public function sizeGuide(): View
    {
        return view('shop.size-guide');
    }

    public function cgv(): View
    {
        return view('shop.legal', ['key' => 'terms', 'icon' => 'fa-file-contract']);
    }

    public function confidentialite(): View
    {
        return view('shop.legal', ['key' => 'privacy', 'icon' => 'fa-user-shield']);
    }

    public function mentionsLegales(): View
    {
        return view('shop.legal', ['key' => 'notice', 'icon' => 'fa-scale-balanced']);
    }

    public function contact(): View
    {
        return view('shop.contact');
    }

    /** Formulaire de contact / demande Studio / candidature créateur (champ `type`). */
    public function contactStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:30'],
            'type' => ['nullable', 'in:contact,studio,creator'],
            'subject' => ['nullable', 'string', 'max:190'],
            'product_type' => ['nullable', 'string', 'max:60'],
            'link' => ['nullable', 'url', 'max:300'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $type = $data['type'] ?? 'contact';
        $prefix = ['contact' => '', 'studio' => '[Studio] ', 'creator' => '[Creator] '][$type];
        $subject = $prefix.($data['subject'] ?? t('contact.subject_default'));
        $body = $data['message']
            .(! empty($data['product_type']) ? "\n\nProduct: {$data['product_type']}" : '')
            .(! empty($data['link']) ? "\nReference link: {$data['link']}" : '')
            ."\nLocale: ".app()->getLocale();

        $contactMessage = ContactMessage::create([
            'name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'] ?? null,
            'subject' => mb_substr($subject, 0, 190), 'message' => $body, 'status' => 'new',
        ]);

        DeferredMail::send($contactMessage->email, $contactMessage->name, new ContactClientMail($contactMessage));
        if ($adminEmail = config('mail.admin_address')) {
            DeferredMail::send($adminEmail, null, new ContactAdminMail($contactMessage));
        }

        return response()->json(['success' => true, 'message' => t('contact.sent')], 201);
    }
}
